<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\Store;
use App\Models\System;
use App\Models\Transaction;
use App\Models\TransactionSellLine;
use App\Models\WagesAndCompensation;
use App\Utils\ProductUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    protected $commonUtil;
    protected $productUtil;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil)
    {
        $this->middleware('auth');
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $start_date = new Carbon('first day of this month');;
        $end_date = new Carbon('last day of this month');;

        $dashboard_data = $this->getDashboardData($start_date, $end_date);

        $best_sellings = $this->getBestSellings($start_date, $end_date, 'qty');
        $yearly_best_sellings_qty = $this->getBestSellings(date("Y") . '-01-01', date("Y") . '-12-31', 'qty');
        $yearly_best_sellings_price = $this->getBestSellings(date("Y") . '-01-01', date("Y") . '-12-31', 'total_price');

        //cash flow of last 6 months
        $start = strtotime(date('Y-m-01', strtotime('-6 month', strtotime(date('Y-m-d')))));
        $end = strtotime(date('Y-m-' . date('t', mktime(0, 0, 0, date("m"), 1, date("Y")))));


        while ($start < $end) {
            $start_date = date("Y-m", $start) . '-' . '01';
            $end_date = date("Y-m", $start) . '-' . '31';

            $cash_flow_data  = $this->getDashboardData($start_date, $end_date);

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

            $sale_amount =  $this->getSaleAmount($start_date, $end_date);
            $purchase_amount = $this->getPurchaseAmount($start_date, $end_date);
            $yearly_sale_amount[] = $sale_amount;
            $yearly_purchase_amount[] = $purchase_amount;
            $start = strtotime("+1 month", $start);
        }

        $sale_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final']);
        $sales = $sale_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->orderBy('transactions.id', 'desc')->take(5)->get();

        $payment_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('users', 'transactions.created_by', 'users.id')
            ->whereIn('transactions.type', ['sell'])
            ->where('transactions.payment_status', 'paid')
            ->whereIn('transactions.status', ['final']);

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

        $quotations = $quotation_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->orderBy('transactions.id', 'desc')->take(5)->get();

        $add_stock_query = Transaction::whereIn('transactions.type', ['add_stock'])
            ->whereIn('transactions.status', ['received']);
        $add_stocks = $add_stock_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->orderBy('transactions.id', 'desc')->take(5)->get();


        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();
        $stores = Store::getDropdown();


        return view('home.index')->with(compact(
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
            'stores',
            'month'
        ));
    }

    public function getChartAndTableSection()
    {
        $start_date = !empty(request()->start_date) ? request()->start_date : new Carbon('first day of this month');
        $end_date = !empty(request()->end_date) ? request()->end_date : new Carbon('last day of this month');
        $store_id = request()->store_id;

        $best_sellings = $this->getBestSellings($start_date, $end_date, 'qty', $store_id);
        $yearly_best_sellings_qty = $this->getBestSellings($start_date, $end_date, 'qty', $store_id);
        $yearly_best_sellings_price = $this->getBestSellings($start_date, $end_date, 'total_price', $store_id);

        $dashboard_data = $this->getDashboardData($start_date, $end_date);

        //cash flow of last 6 months
        $start = strtotime($start_date);
        $end = strtotime($end_date);
        $payment_received = [];
        $payment_sent = [];
        while ($start < $end) {
            $start_date = date("Y-m", $start) . '-' . '01';
            $end_date = date("Y-m", $start) . '-' . '31';

            $cash_flow_data  = $this->getDashboardData($start_date, $end_date, $store_id);

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

            $sale_amount =  $this->getSaleAmount($start_date, $end_date, $store_id);
            $purchase_amount = $this->getPurchaseAmount($start_date, $end_date, $store_id);
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
            'month'
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
    public function getBestSellings($start_date, $end_date, $order_by, $store_id = null)
    {
        $query =  TransactionSellLine::leftjoin('transactions', 'transaction_sell_lines.transaction_id', 'transactions.id')
            ->join('products', 'transaction_sell_lines.product_id', 'products.id')
            ->where('transaction_date', '>=', $start_date)
            ->where('transaction_date', '<=', $end_date);
        if (!empty($store_id)) {
            $query->where('transactions.store_id', '=', $store_id);
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
     * @return double
     */
    public function getSaleAmount($start_date, $end_date, $store_id = null)
    {
        $sell_query = Transaction::where('type', 'sell')->where('status', 'final');
        if (!empty($start_date)) {
            $sell_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $sell_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $sell_query->where('store_id', $store_id);
        }
        return $sell_query->sum('final_total');
    }

    /**
     * get purchase amount
     *
     * @param string $start_date
     * @param string $end_date
     * @param int $store_id
     * @return double
     */
    public function getPurchaseAmount($start_date, $end_date, $store_id = null)
    {
        $purchase_query = Transaction::where('type', 'add_stock')->where('status', 'received');
        if (!empty($start_date)) {
            $purchase_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $purchase_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $purchase_query->where('store_id', $store_id);
        }
        return $purchase_query->sum('final_total');
    }

    /**
     * get dashboard data for pos
     *
     * @param string $start_date
     * @param string $end_date
     * @return void
     */
    public function getDashboardData($start_date, $end_date, $store_id = null)
    {
        if (!empty($store_id)) {
            $store_id = $store_id;
        } else {
            $store_id = request()->get('store_id', null);
        }

        $revenue = $this->getSaleAmount($start_date, $end_date, $store_id);

        $sell_return_query = Transaction::where('type', 'sell_return')->where('status', 'final');
        if (!empty($start_date)) {
            $sell_return_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $sell_return_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $sell_return_query->where('store_id', $store_id);
        }
        $sell_return = $sell_return_query->sum('final_total');

        $purchase_return_query = Transaction::where('type', 'purchase_return')->where('status', 'final');
        if (!empty($start_date)) {
            $purchase_return_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $purchase_return_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $purchase_return_query->where('store_id', $store_id);
        }
        $purchase_return = $purchase_return_query->sum('final_total');

        $purchase =  $this->getPurchaseAmount($start_date, $end_date, $store_id);

        $revenue -= $sell_return;
        $profit = $revenue + $purchase_return - $purchase;

        $expense_query = Transaction::where('type', 'expense')->where('status', 'received');
        if (!empty($start_date)) {
            $expense_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $expense_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $expense_query->where('store_id', $store_id);
        }
        $expense = $expense_query->sum('final_total');

        //payment sent queries
        $payment_received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('type', 'sell')->where('status', 'final');
        if (!empty($start_date)) {
            $payment_received_query->whereDate('paid_on', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $payment_received_query->whereDate('paid_on', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $payment_received_query->where('store_id', $store_id);
        }
        $payment_received = $payment_received_query->sum('amount');

        $payment_purchase_return_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('type', 'purchase_return')->where('status', 'final');
        if (!empty($start_date)) {
            $payment_purchase_return_query->whereDate('paid_on', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $payment_purchase_return_query->whereDate('paid_on', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $payment_purchase_return_query->where('store_id', $store_id);
        }
        $payment_purchase_return = $payment_purchase_return_query->sum('amount');
        $payment_received_total = $payment_received - $payment_purchase_return;

        //payment sent queries
        $payment_purchase_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('type', 'add_stock')->where('status', 'final');
        if (!empty($start_date)) {
            $payment_purchase_query->whereDate('paid_on', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $payment_purchase_query->whereDate('paid_on', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $payment_purchase_query->where('store_id', $store_id);
        }
        $payment_purchase = $payment_purchase_query->sum('amount');

        $payment_expense_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('type', 'expense')->where('status', 'final');
        if (!empty($start_date)) {
            $payment_expense_query->whereDate('paid_on', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $payment_expense_query->whereDate('paid_on', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $payment_expense_query->where('store_id', $store_id);
        }
        $payment_expense = $payment_expense_query->sum('amount');

        $wages_query = WagesAndCompensation::where('id', '>', 0);
        if (!empty($start_date)) {
            $wages_query->where('payment_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $wages_query->where('payment_date', '<=', $end_date);
        }
        $wages_payment = $wages_query->sum('net_amount');

        $sell_return_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')->where('type', 'sell_return')->where('status', 'final');
        if (!empty($start_date)) {
            $sell_return_query->whereDate('transaction_date', '>=', $start_date);
        }
        if (!empty($end_date)) {
            $sell_return_query->whereDate('transaction_date', '<=', $end_date);
        }
        if (!empty($store_id)) {
            $sell_return_query->where('store_id', $store_id);
        }
        $sell_return_payment =  $sell_return_query->sum('amount');

        $payment_sent = $payment_purchase + $payment_expense + $wages_payment + $sell_return_payment;

        $data['revenue'] = $revenue;
        $data['sell_return'] = $sell_return;
        $data['profit'] = $profit;
        $data['purchase'] = $purchase;
        $data['expense'] = $expense;
        $data['purchase_return'] = $purchase_return;
        $data['payment_received'] = $payment_received_total;
        $data['payment_sent'] = $payment_sent;
        $data['current_stock_value'] = $this->productUtil->getCurrentStockValueByStore($store_id);

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
