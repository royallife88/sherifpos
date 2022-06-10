<?php

namespace App\Http\Controllers;

use App\Models\CashRegisterTransaction;
use App\Models\Employee;
use App\Models\GiftCard;
use App\Models\Leave;
use App\Models\Product;
use App\Models\Store;
use App\Models\System;
use App\Models\Transaction;
use App\Models\TransactionSellLine;
use App\Models\WagesAndCompensation;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    protected $commonUtil;
    protected $productUtil;
    protected $transactionUtil;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->middleware('auth');
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $start_date = new Carbon('first day of this month');
        $end_date = new Carbon('last day of this month');

        $store_ids = [];
        $store_pos_id = null;
        if (strtolower(session('user.job_title')) == 'cashier') {
            $store_pos_id = session('user.pos_id');
        } else {
            if (!Auth::user()->is_superadmin && !auth()->user()->is_admin) {
                $employee = Employee::where('user_id', Auth::user()->id)->first();
                $store_ids = $employee->store_id;
                $store_pos_id = null;
            }
        }

        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $stores = Store::getDropdown();

        return view('home.index')->with(compact(
            'payment_types',
            'stores',
            'start_date',
            'end_date'
        ));
    }

    public function getChartAndTableSection()
    {
        $store_id = [];
        $start_date = !empty(request()->start_date) ? request()->start_date : new Carbon('first day of this month');
        $end_date = !empty(request()->end_date) ? request()->end_date : new Carbon('last day of this month');
        $store_id = request()->input('store_id') ? [request()->input('store_id')] : [];

        $store_pos_id = null;
        if (!Auth::user()->is_superadmin && !auth()->user()->is_admin) {
            $employee = Employee::where('user_id', Auth::user()->id)->first();
            $store_id = $employee->store_id;
            $store_pos_id = null;
            if (strtolower(session('user.job_title')) == 'cashier') {
                $store_pos_id = session('user.pos_id');
            } else {
                $store_pos_id =  -1;
            }
        }

        $best_sellings = $this->getBestSellings($start_date, $end_date, 'qty', $store_id);
        $yearly_best_sellings_qty = $this->getBestSellings($start_date, $end_date, 'qty', $store_id);
        $yearly_best_sellings_price = $this->getBestSellings($start_date, $end_date, 'total_price', $store_id);

        $dashboard_data = $this->getDashboardDetails($start_date, $end_date, $store_id, $store_pos_id);

        //cash flow of last 6 months
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        $payment_received = [];
        $payment_sent = [];
        while ($start <= $end) {
            $start_date = date("Y-m", $start) . '-' . '01';
            $end_date = date("Y-m", $start) . '-' . '31';

            $cash_flow_data  = $this->getDashboardDetails($start_date, $end_date, $store_id, $store_pos_id);

            $payment_received[] = $cash_flow_data['payment_received'];
            $payment_sent[] = $cash_flow_data['payment_sent'];
            $month[] = date("F", strtotime($start_date));
            $start = strtotime("+1 month", $start);
        }

        // yearly report
        $start = strtotime(date("Y") . '-01-01');
        $end = strtotime(date("Y") . '-12-31');
        while ($start < $end) {
            $start_date = date("Y") . '-' . date('m', $start) . '-' . '01';
            $end_date = date("Y") . '-' . date('m', $start) . '-' . '31';

            $sale_amount =  $this->getSaleAmount($start_date, $end_date, $store_id, $store_pos_id);
            $purchase_amount = $this->getPurchaseAmount($start_date, $end_date, $store_id, $store_pos_id);
            $yearly_sale_amount[] = $sale_amount;
            $yearly_purchase_amount[] = $purchase_amount;
            $start = strtotime("+1 month", $start);
        }
        $start_date = !empty(request()->start_date) ? request()->start_date : new Carbon('first day of this month');
        $end_date = !empty(request()->end_date) ? request()->end_date : new Carbon('last day of this month');

        $sale_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final']);
        if (!empty($store_id)) {
            $sale_query->where('transactions.store_id', '=', $store_id);
        }
        if (!empty($store_pos_id)) {
            $sale_query->where('transactions.store_pos_id', '=', $store_pos_id);
        }

        if (!empty($start_date)) {
            $sale_query->whereDate('transactions.transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $sale_query->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        $sales = $sale_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->orderBy('transactions.id', 'desc')->take(5)->get();

        $payment_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('users', 'transactions.created_by', 'users.id')
            ->whereIn('transactions.type', ['sell'])
            ->where('transactions.payment_status', 'paid')
            ->whereIn('transactions.status', ['final']);
        if (!empty($store_id)) {
            $payment_query->where('transactions.store_id', '=', $store_id);
        }
        if (!empty($store_pos_id)) {
            $payment_query->where('transactions.store_pos_id', '=', $store_pos_id);
        }
        if (!empty($start_date)) {
            $payment_query->whereDate('transactions.transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $payment_query->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        $payments = $payment_query->select(
            'transactions.*',
            'transaction_payments.method',
            'transaction_payments.amount',
            'transaction_payments.ref_number',
            'transaction_payments.paid_on',
            'users.name as created_by_name',
        )->groupBy('transaction_payments.id')->orderBy('transactions.id', 'desc')->take(5)->get();

        $quotation_query = Transaction::whereIn('transactions.type', ['sell'])
            ->where('is_quotation', 1);
        if (!empty($store_id)) {
            $quotation_query->where('transactions.store_id', '=', $store_id);
        }
        if (!empty($store_pos_id)) {
            $quotation_query->where('transactions.store_pos_id', '=', $store_pos_id);
        }
        if (!empty($start_date)) {
            $quotation_query->whereDate('transactions.transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $quotation_query->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        $quotations = $quotation_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->orderBy('transactions.id', 'desc')->take(5)->get();

        $add_stock_query = Transaction::whereIn('transactions.type', ['add_stock'])
            ->whereIn('transactions.status', ['received']);
        if (!empty($store_id)) {
            $add_stock_query->where('transactions.store_id', '=', $store_id);
        }
        if (!empty($store_pos_id)) {
            $add_stock_query->where('transactions.store_pos_id', '=', $store_pos_id);
        }
        if (!empty($start_date)) {
            $add_stock_query->whereDate('transactions.transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $add_stock_query->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        $add_stocks = $add_stock_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->orderBy('transactions.id', 'desc')->take(5)->get();


        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('home.partials.chart_and_table')->with(compact(
            'dashboard_data',
            'payment_received',
            'payment_sent',
            'yearly_sale_amount',
            'yearly_purchase_amount',
            'sales',
            'payments',
            'quotations',
            'add_stocks',
            'payment_types',
            'best_sellings',
            'yearly_best_sellings_qty',
            'yearly_best_sellings_price',
            'month',
            'start_date',
            'end_date'
        ));
    }

    /**
     * get best selling oroduct data
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $order_by
     * @return void
     */
    public function getBestSellings($start_date, $end_date, $order_by, $store_id = [], $store_pos_id = null)
    {
        $query =  TransactionSellLine::leftjoin('transactions', 'transaction_sell_lines.transaction_id', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', 'products.id')
            ->where('transaction_date', '>=', $start_date)
            ->where('transaction_date', '<=', $end_date);

        if (!empty($store_id)) {
            $query->whereIn('transactions.store_id', $store_id);
        }
        if (!empty($store_pos_id)) {
            $query->where('transactions.store_pos_id', '=', $store_pos_id);
        }

        $result = $query->select(
            DB::raw('SUM(quantity) as qty'),
            DB::raw('SUM(sub_total) as total_price'),
            'transaction_sell_lines.*'
        )
            ->groupBy('transaction_sell_lines.product_id')
            ->orderBy($order_by, 'desc')
            ->take(5)->get();

        return $result;
    }

    /**
     * get sales amount
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $store_id
     * @param string $store_pos_id
     * @return double
     */
    public function getSaleAmount($start_date, $end_date, $store_id = [], $store_pos_id = null)
    {
        $sell_query = Transaction::where('type', 'sell')->where('status', 'final');
        if (!empty($start_date)) {
            $sell_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $sell_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty(request()->start_time)) {
            $sell_query->where('transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $sell_query->where('transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty($store_id)) {
            $sell_query->whereIn('store_id', $store_id);
        }
        if (!empty($store_pos_id)) {
            $sell_query->where('store_pos_id', $store_pos_id);
        }
        $sell_query = $sell_query->select(
            DB::raw('SUM(final_total) as total_sales'),
            DB::raw('SUM(total_tax) as total_taxes'),

        )->first();
        if (!empty($sell_query)) {
            return $sell_query->total_sales;
        }
        return 0;
    }
    /**
     * get sales amount
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $store_id
     * @param string $store_pos_id
     * @return double
     */
    public function getTotalSaleItemTaxAmount($start_date, $end_date, $store_id = [], $store_pos_id = null, $currency_id = null)
    {
        $default_currency_id = System::getProperty('currency');

        $sell_query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');
        if (!empty($start_date)) {
            $sell_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $sell_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty(request()->start_time)) {
            $sell_query->where('transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $sell_query->where('transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty($store_id)) {
            $sell_query->whereIn('store_id', $store_id);
        }
        if (!empty($store_pos_id)) {
            $sell_query->where('store_pos_id', $store_pos_id);
        }
        if (!empty($currency_id)) {
            if ($currency_id == $default_currency_id) {
                $sell_query->where(function ($q) use ($currency_id) {
                    $q->where('received_currency_id', $currency_id)
                        ->orWhereNull('received_currency_id');
                });
            } else {
                $sell_query->where(function ($q) use ($currency_id) {
                    $q->where('received_currency_id', $currency_id);
                });
            }
        }

        $sell_query = $sell_query->select(
            DB::raw('SUM(IF(products.tax_method = "inclusive", item_tax, 0)) as total_tax'),
            DB::raw('SUM(IF(products.tax_method = "inclusive", (item_tax / quantity) * quantity_returned, 0)) as total_return_tax')
        )->first();

        if (!empty($sell_query)) {
            return $sell_query->total_tax - $sell_query->total_return_tax;
        }
        return 0;
    }

    /**
     * get sales amount
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $store_id
     * @param string $store_pos_id
     * @return double
     */
    public function getTotalSaleGeneralTaxAmount($start_date, $end_date, $store_id = [], $store_pos_id = null, $currency_id = null)
    {
        $default_currency_id = System::getProperty('currency');

        $sell_query = Transaction::where('transactions.type', 'sell')
            ->where('transactions.status', 'final');
        if (!empty($start_date)) {
            $sell_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $sell_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty(request()->start_time)) {
            $sell_query->where('transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $sell_query->where('transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty($store_id)) {
            $sell_query->whereIn('store_id', $store_id);
        }
        if (!empty($store_pos_id)) {
            $sell_query->where('store_pos_id', $store_pos_id);
        }
        if (!empty($currency_id)) {
            if ($currency_id == $default_currency_id) {
                $sell_query->where(function ($q) use ($currency_id) {
                    $q->where('received_currency_id', $currency_id)
                        ->orWhereNull('received_currency_id');
                });
            } else {
                $sell_query->where(function ($q) use ($currency_id) {
                    $q->where('received_currency_id', $currency_id);
                });
            }
        }
        $sell = $sell_query->select(
            DB::raw('SUM(IF(transactions.tax_method = "inclusive", total_tax, 0)) as total_tax'),
        )->first();

        $sell_return_total_tax = $this->getTotalSaleReturnGeneralTaxAmount($start_date, $end_date, $store_id, $store_pos_id);
        if (!empty($sell)) {
            return $sell->total_tax - $sell_return_total_tax;
        }
        return 0;
    }

    /**
     * get sales amount
     *
     * @param string $start_date
     * @param string $end_date
     * @param string $store_id
     * @param string $store_pos_id
     * @return double
     */
    public function getTotalSaleReturnGeneralTaxAmount($start_date, $end_date, $store_id = [], $store_pos_id = null)
    {
        $sell_query = Transaction::join('transactions as sell_return', 'transactions.id', 'sell_return.return_parent_id')
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');
        if (!empty($start_date)) {
            $sell_query->whereDate('transactions.transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $sell_query->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        if (!empty(request()->start_time)) {
            $sell_query->where('transactions.transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $sell_query->where('transactions.transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty($store_id)) {
            $sell_query->whereIn('transactions.store_id', $store_id);
        }
        if (!empty($store_pos_id)) {
            $sell_query->where('transactions.store_pos_id', $store_pos_id);
        }
        $sell_return = $sell_query->select(
            DB::raw('SUM(IF(transactions.tax_method = "inclusive", transactions.total_tax, 0)) as total_tax'),
        )->first();

        if (!empty($sell_return)) {
            return $sell_return->total_tax ?? 0;
        }
        return 0;
    }

    /**
     * get purchase amount
     *
     * @param string $start_date
     * @param string $end_date
     * @param int $store_id
     * @param int $store_pos_id
     * @return double
     */
    public function getPurchaseAmount($start_date, $end_date, $store_id = [], $store_pos_id = null)
    {
        $purchase_query = Transaction::where('type', 'add_stock')->where('status', 'received');
        if (!empty($start_date)) {
            $purchase_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $purchase_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $purchase_query->whereIn('store_id', $store_id);
        }
        if (!empty($store_pos_id)) {
            $purchase_query->where('store_pos_id', $store_pos_id);
        }
        return $purchase_query->sum('final_total');
    }

    /**
     * get dashboard data for pos
     *
     * @param string $start_date
     * @param string $end_date
     * @param array $store_id
     * @param string $store_pos_id
     * @return void
     */
    public function getDashboardData($start_date, $end_date, $store_id = [], $store_pos_id = null)
    {
        $exchange_rate_currencies = $this->commonUtil->getExchangeRateCurrencies(true);

        $data = [];
        $i = 0;
        foreach ($exchange_rate_currencies as $currency) {
            $data[$i]['currency'] = $currency;
            $data[$i]['data'] = $this->getDashboardDetails($start_date, $end_date, $store_id, $store_pos_id, $currency['currency_id']);

            $i++;
        }

        return $data;
    }
    /**
     * get dashboard data for pos
     *
     * @param string $start_date
     * @param string $end_date
     * @param array $store_id
     * @param string $store_pos_id
     * @return void
     */
    public function getDashboardDetails($start_date, $end_date, $store_id = [], $store_pos_id = null, $currency_id = null)
    {
        $default_currency_id = System::getProperty('currency');

        if (!empty($store_id)) {
            $store_id = $store_id;
        } else {
            $store_id = request()->input('store_id') ? [request()->input('store_id')] : [];
        }

        if (!Auth::user()->is_superadmin && !auth()->user()->is_admin) {
            $store_pos_id = null;
            if (!empty(session('user.pos_id'))) {
                $store_pos_id = session('user.pos_id');
            } else {
                $store_pos_id =  -1;
            }
        }

        $total_sale_item_tax_inclusive = $this->getTotalSaleItemTaxAmount($start_date, $end_date, $store_id, $store_pos_id);
        $total_sale_general_tax_inclusive = $this->getTotalSaleGeneralTaxAmount($start_date, $end_date, $store_id, $store_pos_id);


        $transaction_query = Transaction::whereIn('type', ['sell', 'sell_return', 'purchase_return', 'expense', 'add_stock'])->whereIn('status', ['final', 'received']);
        if (!empty($start_date)) {
            $transaction_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $transaction_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty(request()->start_time)) {
            $transaction_query->where('transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $transaction_query->where('transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty($store_id)) {
            $transaction_query->whereIn('store_id', $store_id);
        }
        if (!empty($store_pos_id)) {
            $transaction_query->where('store_pos_id', $store_pos_id);
        }
        if (!empty($currency_id)) {
            if ($currency_id == $default_currency_id) {
                $transaction_query->where(function ($q) use ($currency_id) {
                    $q->where('received_currency_id', $currency_id)
                        ->orWhereNull('received_currency_id');
                });
            } else {
                $transaction_query->where(function ($q) use ($currency_id) {
                    $q->where('received_currency_id', $currency_id);
                });
            }
        }
        $transaction_query->select(
            DB::raw('SUM(IF(transactions.type="sell_return", final_total, 0)) as total_sell_return'),
            DB::raw('SUM(IF(transactions.type="sell_return", gift_card_amount, 0)) as total_gift_card_amount'),
            DB::raw('SUM(IF(transactions.type="purchase_return", final_total, 0)) as total_purchase_return'),
            DB::raw('SUM(IF(transactions.type="expense", final_total, 0)) as total_expense'),
            DB::raw('SUM(IF(transactions.type="add_stock", final_total, 0)) as total_purchases'),
            DB::raw('SUM(IF(transactions.type="sell", final_total, 0)) as total_sell'),
            DB::raw('SUM(IF(transactions.type="sell" AND transactions.delivery_cost_given_to_deliveryman="1", delivery_cost, 0)) as total_delivery_cost_given_to_deliveryman'),
        );
        $transaction_query = $transaction_query->first();

        $gift_card_returned = $transaction_query->total_gift_card_amount ?? 0;


        $revenue = $transaction_query->total_sell ?? 0;
        // $total_delivery_cost_given_to_deliveryman = $transaction_query->total_delivery_cost_given_to_deliveryman ?? 0;
        // $revenue = $revenue - $total_delivery_cost_given_to_deliveryman;

        $sell_return  = $transaction_query->total_sell_return - $gift_card_returned; // for gift card return no change in sell return

        $purchase_return = $transaction_query->total_purchase_return ?? 0;

        $purchase = $transaction_query->total_purchases ?? 0;

        $revenue -= $sell_return;

        $cost_query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($start_date)) {
            $cost_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $cost_query->whereDate('transaction_date',  '<=', $end_date);
        }
        if (!empty(request()->start_time)) {
            $cost_query->where('transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $cost_query->where('transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty($store_id)) {
            $cost_query->whereIn('store_id', $store_id);
        }
        if (!empty($store_pos_id)) {
            $cost_query->where('store_pos_id', $store_pos_id);
        }

        if (!empty($currency_id)) {
            if ($currency_id == $default_currency_id) {
                $cost_query->where(function ($q) use ($currency_id) {
                    $q->where('received_currency_id', $currency_id)
                        ->orWhereNull('received_currency_id');
                });
            } else {
                $cost_query->where(function ($q) use ($currency_id) {
                    $q->where('received_currency_id', $currency_id);
                });
            }
        }

        $cost_query = $cost_query->select(
            DB::raw("SUM(transaction_sell_lines.quantity * transaction_sell_lines.purchase_price) as cost_of_sold_products"),
            DB::raw("SUM(transaction_sell_lines.quantity_returned * transaction_sell_lines.purchase_price) as cost_of_sold_returned_products")
        )->first();

        $cost_sold_product = $cost_query->cost_of_sold_products ?? 0;
        $cost_sold_returned_product = $cost_query->cost_of_sold_returned_products ?? 0;

        if (!empty($currency_id)) {
            if ($currency_id == $default_currency_id) {
                $gift_card_sold = GiftCard::whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->sum('balance');
            } else {
                $gift_card_sold = 0;
            }
        } else {
            $gift_card_sold = GiftCard::whereDate('created_at', '>=', $start_date)->whereDate('created_at', '<=', $end_date)->sum('balance');
        }

        $profit = $revenue - $cost_sold_product + $cost_sold_returned_product + $gift_card_sold - $gift_card_returned - $total_sale_item_tax_inclusive - $total_sale_general_tax_inclusive;  //excluding taxes from profit as its not part of profit
        $expense_query = Transaction::where('type', 'expense')->where('status', 'received');
        if (!empty($start_date)) {
            $expense_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $expense_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty(request()->start_time)) {
            $expense_query->where('transaction_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $expense_query->where('transaction_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty($store_id)) {
            $expense_query->whereIn('store_id', $store_id);
        }
        if (!empty($store_pos_id)) {
            $expense_query->where('store_pos_id', $store_pos_id);
        }
        if (!empty($currency_id)) {
            if ($currency_id == $default_currency_id) {
                $expense = $expense_query->sum('final_total');
            } else {
                $expense = 0; //expense does not have currency
            }
        } else {
            $expense = $expense_query->sum('final_total');
        }

        //payment sent queries

        $payment_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->whereIn('type', ['sell', 'purchase_return', 'add_stock', 'expense', 'sell_return'])->where('status', 'final');

        if (!empty($start_date)) {
            $payment_query->whereDate('paid_on', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $payment_query->whereDate('paid_on', '<=', $end_date);
        }
        if (!empty(request()->start_time)) {
            $payment_query->where('paid_on', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $payment_query->where('paid_on', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty($store_id)) {
            $payment_query->whereIn('store_id', $store_id);
        }
        if (!empty($store_pos_id)) {
            $payment_query->where('store_pos_id', $store_pos_id);
        }
        if (!empty($currency_id)) {
            if ($currency_id == $default_currency_id) {
                $payment_query->where(function ($q) use ($currency_id) {
                    $q->where('received_currency_id', $currency_id)
                        ->orWhereNull('received_currency_id');
                });
            } else {
                $payment_query->where(function ($q) use ($currency_id) {
                    $q->where('received_currency_id', $currency_id);
                });
            }
        }

        $payment_query->select(
            DB::raw('SUM(IF(transactions.type="sell", transaction_payments.amount, 0)) as total_sell_paid'),
            DB::raw('SUM(IF(transactions.type="sell_return", transaction_payments.amount, 0)) as total_sell_return_paid'),
            DB::raw('SUM(IF(transactions.type="purchase_return", transaction_payments.amount, 0)) as total_purchase_return_paid'),
            DB::raw('SUM(IF(transactions.type="expense", transaction_payments.amount, 0)) as total_expense_paid'),
            DB::raw('SUM(IF(transactions.type="add_stock", transaction_payments.amount, 0)) as total_add_stock_paid'),
        );
        $payment_query = $payment_query->first();

        $payment_received = $payment_query->total_sell_paid ?? 0;

        $payment_purchase_return = $payment_query->total_purchase_return_paid ?? 0;
        $payment_received_total = $payment_received - $payment_purchase_return;

        $payment_purchase = $payment_query->total_add_stock_paid ?? 0;

        $payment_expense = $payment_query->total_expense_paid ?? 0;

        $sell_return_payment = $payment_query->total_sell_return_paid ?? 0;

        $wages_query = WagesAndCompensation::where('id', '>', 0);
        if (!empty($start_date)) {
            $wages_query->where('payment_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $wages_query->where('payment_date', '<=', $end_date);
        }
        if (!empty(request()->start_time)) {
            $wages_query->where('payment_date', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
        }
        if (!empty(request()->end_time)) {
            $wages_query->where('payment_date', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
        }
        if (!empty($currency_id)) {
            if ($currency_id == $default_currency_id) {
                $wages_payment = $wages_query->sum('net_amount');
            } else {
                $wages_payment = 0; //expense does not have currency
            }
        } else {
            $wages_payment = $wages_query->sum('net_amount');
        }

        $payment_sent = $payment_purchase + $payment_expense + $wages_payment + $sell_return_payment;

        if (!empty($currency_id)) {
            if ($currency_id == $default_currency_id) {
                $current_stock_value = $this->productUtil->getCurrentStockValueByStore($store_id);
            } else {
                $current_stock_value = 0; //expense does not have currency
            }
        } else {
            $current_stock_value = $this->productUtil->getCurrentStockValueByStore($store_id);
        }

        $data['revenue'] = $revenue;
        $data['sell_return'] = $sell_return;
        $data['profit'] = $profit;
        $data['purchase'] = $purchase;
        $data['expense'] = $expense;
        $data['purchase_return'] = $purchase_return;
        $data['payment_received'] = $payment_received_total;
        $data['payment_sent'] = $payment_sent;
        $data['current_stock_value'] = $current_stock_value;

        return $data;
    }

    /**
     * show the user transactin
     *
     * @param int $year
     * @param int $month
     * @return void
     */
    public function myTransaction($year, $month)
    {
        $start = 1;
        $number_of_day = date('t', mktime(0, 0, 0, $month, 1, $year));
        while ($start <= $number_of_day) {
            if ($start < 10)
                $date = $year . '-' . $month . '-0' . $start;
            else
                $date = $year . '-' . $month . '-' . $start;
            $sale_generated[$start] = Transaction::where('type', 'sell')->where('status', 'final')->whereDate('transaction_date', $date)->where('created_by', Auth::id())->count();
            $sale_grand_total[$start] = Transaction::where('type', 'sell')->where('status', 'final')->whereDate('transaction_date', $date)->where('created_by', Auth::id())->sum('final_total');
            $purchase_generated[$start] = Transaction::where('type', 'add_stock')->whereDate('transaction_date', $date)->where('created_by', Auth::id())->count();
            $purchase_grand_total[$start] = Transaction::where('type', 'add_stock')->whereDate('transaction_date', $date)->where('created_by', Auth::id())->sum('final_total');
            $quotation_generated[$start] = Transaction::where('type', 'sell')->where('is_quotation', 1)->whereDate('transaction_date', Auth::id())->count();
            $quotation_grand_total[$start] = Transaction::where('type', 'sell')->where('is_quotation', 1)->whereDate('transaction_date', Auth::id())->sum('final_total');
            $start++;
        }
        $start_day = date('w', strtotime($year . '-' . $month . '-01')) + 1;
        $prev_year = date('Y', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $prev_month = date('m', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $next_year = date('Y', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));
        $next_month = date('m', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));
        return view('user.my_transactions', compact(
            'start_day',
            'year',
            'month',
            'number_of_day',
            'prev_year',
            'prev_month',
            'next_year',
            'next_month',
            'sale_generated',
            'sale_grand_total',
            'purchase_generated',
            'purchase_grand_total',
            'quotation_generated',
            'quotation_grand_total'
        ));
    }

    /**
     * show the user leaves
     *
     * @param int $year
     * @param int $month
     * @return void
     */
    public function myHoliday($year, $month)
    {
        $start = 1;
        $number_of_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $employee = Employee::where('user_id', Auth::user()->id)->first();
        while ($start <= $number_of_day) {
            if ($start < 10) {
                $date = $year . '-' . $month . '-0' . $start;
            } else {
                $date = $year . '-' . $month . '-' . $start;
            }
            $holiday_found = Leave::where('employee_id', $employee->id)->whereDate('start_date', '<=', $date)->whereDate('end_date', '>=', $date)->where('status', 'approved')->first();
            if ($holiday_found) {
                $holidays[$start] = $this->commonUtil->format_date($holiday_found->start_date) . ' ' . __("lang.to") . ' ' . $this->commonUtil->format_date($holiday_found->end_date);
            } else {
                $holidays[$start] = false;
            }
            $start++;
        }

        $start_day = date('w', strtotime($year . '-' . $month . '-01')) + 1;
        $prev_year = date('Y', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $prev_month = date('m', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $next_year = date('Y', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));
        $next_month = date('m', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));

        return view('user.my_holidays', compact(
            'start_day',
            'year',
            'month',
            'number_of_day',
            'prev_year',
            'prev_month',
            'next_year',
            'next_month',
            'holidays'
        ));
    }

    /**
     * show the help page content
     *
     * @return void
     */
    public function getHelp()
    {
        $help_page_content = System::getProperty('help_page_content');

        return view('home.help')->with(compact(
            'help_page_content'
        ));
    }
}
