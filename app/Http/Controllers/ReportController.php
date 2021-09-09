<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WagesAndCompensation;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $transactionUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * show the profit loss report
     *
     * @return view
     */
    public function getProfitLoss(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $sale_query = Transaction::leftjoin('stores', 'transactions.store_id', 'stores.id')
            ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final');

        if (!empty($request->start_date)) {
            $sale_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $sale_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_type_id)) {
            $sale_query->where('customer_type_id', $request->customer_type_id);
        }

        if (!empty($store_id)) {
            $sale_query->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $sale_query->where('store_pos_id', $pos_id);
        }
        if (!empty($request->product_id)) {
            $sale_query->where('product_id', $request->product_id);
        }

        $sales = $sale_query->select(
            'stores.name as store_name',
            'stores.id as store_id',
            DB::raw('SUM(transactions.final_total) as total_amount')
        )->groupBy('stores.id')->get();

        $purchase_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.type', 'add_stock')
            ->where('transactions.status', 'received');

        if (!empty($request->start_date)) {
            $purchase_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $purchase_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_type_id)) {
            $purchase_query->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $purchase_query->where('store_id', $store_id);
        }

        $purchases = $purchase_query->select(
            DB::raw('SUM(transactions.final_total) as total_amount')
        )->groupBy('transactions.id')->first();


        $expense_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('expense_categories', 'transactions.expense_category_id', 'expense_categories.id')
            ->where('transactions.type', 'expense')
            ->where('transactions.status', 'final');

        if (!empty($request->start_date)) {
            $expense_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $expense_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_type_id)) {
            $expense_query->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $expense_query->where('store_id', $store_id);
        }

        $expenses = $expense_query->select(
            'expense_categories.name as expense_category_name',
            'expense_category_id',
            DB::raw('SUM(transactions.final_total) as total_amount')
        )->groupBy('expense_categories.id')->get();

        $wages_query = WagesAndCompensation::where('id', '>', 0);

        if (!empty($request->start_date)) {
            $wages_query->where('payment_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $wages_query->where('payment_date', '<=', $request->end_date);
        }
        if (!empty($request->employee_id)) {
            $wages_query->where('employee_id', $request->employee_id);
        }
        if (!empty($request->payment_type)) {
            $wages_query->where('payment_type', $request->payment_type);
        }

        $wages = $wages_query->select(
            'wages_and_compensation.payment_type',
            DB::raw('SUM(wages_and_compensation.net_amount) as total_amount')
        )->groupBy('wages_and_compensation.payment_type')->get();

        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $employees = Employee::pluck('employee_name', 'id');
        $customer_types = CustomerType::getDropdown();
        $wages_payment_types = WagesAndCompensation::getPaymentTypes();

        // TODO:: filter for all adjustments
        return view('reports.profit_loss_report')->with(compact(
            'sales',
            'wages',
            'expenses',
            'purchases',
            'store_pos',
            'products',
            'employees',
            'stores',
            'customer_types',
            'wages_payment_types',
        ));
    }

    /**
     * show the receivable amount report
     *
     * @return view
     */
    public function getReceivableReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $sale_query = Transaction::leftjoin('stores', 'transactions.store_id', 'stores.id')
            ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.type', 'sell')
            ->where('transactions.payment_status', 'paid')
            ->where('transactions.status', 'final');

        if (!empty($request->start_date)) {
            $sale_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $sale_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_id)) {
            $sale_query->where('customer_id', $request->customer_id);
        }
        if (!empty($request->customer_type_id)) {
            $sale_query->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $sale_query->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $sale_query->where('store_pos_id', $pos_id);
        }
        if (!empty($request->product_id)) {
            $sale_query->where('product_id', $request->product_id);
        }

        $sales = $sale_query->select('transactions.*')
        ->groupBy('transactions.id')->get();


        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $customer_types = CustomerType::getDropdown();
        $customers = Customer::pluck('name', 'id');
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();

        return view('reports.receivable_report')->with(compact(
            'sales',
            'store_pos',
            'products',
            'customers',
            'stores',
            'customer_types',
            'payment_status_array',
        ));
    }

    /**
     * show the payable amount report
     *
     * @return view
     */
    public function getPayableReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];


        $add_stock_query = Transaction::leftjoin('stores', 'transactions.store_id', 'stores.id')
            ->leftjoin('add_stock_lines', 'transactions.id', 'add_stock_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('suppliers', 'transactions.supplier_id', 'suppliers.id')
            ->where('transactions.type', 'add_stock')
            ->where('transactions.payment_status', 'paid');

        if (!empty($request->start_date)) {
            $add_stock_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $add_stock_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->supplier_id)) {
            $add_stock_query->where('supplier_id', $request->supplier_id);
        }
        if (!empty($store_id)) {
            $add_stock_query->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $add_stock_query->where('store_pos_id', $pos_id);
        }
        if (!empty($request->product_id)) {
            $add_stock_query->where('product_id', $request->product_id);
        }

        $add_stocks = $add_stock_query->select('transactions.*')->groupBy('transactions.id')->get();


        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $customer_types = CustomerType::getDropdown();
        $suppliers = Supplier::pluck('name', 'id');

        return view('reports.payable_report')->with(compact(
            'add_stocks',
            'store_pos',
            'products',
            'suppliers',
            'stores',
            'customer_types',
        ));
    }
    /**
     * show the expected receivable amount report
     *
     * @return view
     */
    public function getExpectedReceivableReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $sale_query = Transaction::leftjoin('stores', 'transactions.store_id', 'stores.id')
            ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.type', 'sell')
            ->whereIn('transactions.payment_status', ['pending', 'partial'])
            ->where('transactions.status', 'final');

        if (!empty($request->start_date)) {
            $sale_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $sale_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_id)) {
            $sale_query->where('customer_id', $request->customer_id);
        }
        if (!empty($request->customer_type_id)) {
            $sale_query->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $sale_query->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $sale_query->where('store_pos_id', $pos_id);
        }
        if (!empty($request->product_id)) {
            $sale_query->where('product_id', $request->product_id);
        }

        $sales = $sale_query
            ->select(
                'transactions.*'
            )->groupBy('transactions.id')->get();


        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $customer_types = CustomerType::getDropdown();
        $customers = Customer::pluck('name', 'id');
        $payment_status_array = $this->commonUtil->getPaymentStatusArray();

        return view('reports.expected_receivable_report')->with(compact(
            'sales',
            'store_pos',
            'products',
            'customers',
            'stores',
            'customer_types',
            'payment_status_array',
        ));
    }

    /**
     * show the expected payable amount report
     *
     * @return view
     */
    public function getExpectedPayableReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $add_stock_query = Transaction::leftjoin('stores', 'transactions.store_id', 'stores.id')
            ->leftjoin('add_stock_lines', 'transactions.id', 'add_stock_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('suppliers', 'transactions.supplier_id', 'suppliers.id')
            ->where('transactions.type', 'add_stock')
            ->where('transactions.payment_status', 'pending');

        if (!empty($request->start_date)) {
            $add_stock_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $add_stock_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->supplier_id)) {
            $add_stock_query->where('supplier_id', $request->supplier_id);
        }
        if (!empty($store_id)) {
            $add_stock_query->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $add_stock_query->where('store_pos_id',  $pos_id);
        }
        if (!empty($request->product_id)) {
            $add_stock_query->where('product_id', $request->product_id);
        }

        $add_stocks = $add_stock_query->groupBy('transactions.id')->get();

        $expense_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('expense_categories', 'transactions.expense_category_id', 'expense_categories.id')
            ->where('transactions.type', 'expense')
            ->where('transactions.payment_status', 'pending')
            ->where('transactions.status', 'final');

        if (!empty($request->start_date)) {
            $expense_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $expense_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_type_id)) {
            $expense_query->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $expense_query->where('store_id', $store_id);
        }

        $expenses = $expense_query->select(
            'transactions.*',
            'expense_categories.name as expense_category_name',
            'expense_category_id',
            DB::raw('SUM(transactions.final_total) as total_amount')
        )->groupBy('transactions.id')->get();

        $wages_query = WagesAndCompensation::where('status',  'pending');

        if (!empty($request->start_date)) {
            $wages_query->where('payment_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $wages_query->where('payment_date', '<=', $request->end_date);
        }
        if (!empty($request->employee_id)) {
            $wages_query->where('employee_id', $request->employee_id);
        }
        if (!empty($request->payment_type)) {
            $wages_query->where('payment_type', $request->payment_type);
        }

        $wages = $wages_query->select(
            'wages_and_compensation.*',
            DB::raw('SUM(wages_and_compensation.net_amount) as total_amount')
        )->groupBy('wages_and_compensation.id')->get();



        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $customer_types = CustomerType::getDropdown();
        $suppliers = Supplier::pluck('name', 'id');
        $employees = Employee::pluck('employee_name', 'id');
        $wages_payment_types = WagesAndCompensation::getPaymentTypes();

        return view('reports.expected_payable_report')->with(compact(
            'add_stocks',
            'expenses',
            'wages',
            'wages_payment_types',
            'store_pos',
            'products',
            'suppliers',
            'employees',
            'stores',
            'customer_types',
        ));
    }

    /**
     * show the summary report
     *
     * @return view
     */
    public function getSummaryReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $add_stock_query = Transaction::leftjoin('stores', 'transactions.store_id', 'stores.id')
            ->leftjoin('add_stock_lines', 'transactions.id', 'add_stock_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('suppliers', 'transactions.supplier_id', 'suppliers.id')
            ->where('transactions.type', 'add_stock')
            ->where('transactions.payment_status', 'paid');

        if (!empty($request->start_date)) {
            $add_stock_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $add_stock_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->supplier_id)) {
            $add_stock_query->where('supplier_id', $request->supplier_id);
        }
        if (!empty($store_id)) {
            $add_stock_query->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $add_stock_query->where('store_pos_id', $pos_id);
        }
        if (!empty($request->product_id)) {
            $add_stock_query->where('product_id', $request->product_id);
        }

        $add_stocks = $add_stock_query->select(
            DB::raw('COUNT(transactions.id) as total_count'),
            DB::raw('SUM(final_total) as total_amount'),
            DB::raw('SUM(transaction_payments.amount) as total_paid'),
            DB::raw('SUM(total_tax) as total_taxes'),
        )->first();

        $sale_query = Transaction::leftjoin('stores', 'transactions.store_id', 'stores.id')
            ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.type', 'sell')
            ->where('transactions.payment_status', 'paid')
            ->where('transactions.status', 'final');

        if (!empty($request->start_date)) {
            $sale_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $sale_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_id)) {
            $sale_query->where('customer_id', $request->customer_id);
        }
        if (!empty($request->customer_type_id)) {
            $sale_query->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $sale_query->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $sale_query->where('store_pos_id', $pos_id);
        }
        if (!empty($request->product_id)) {
            $sale_query->where('product_id', $request->product_id);
        }

        $sales = $sale_query->select(
            DB::raw('COUNT(transactions.id) as total_count'),
            DB::raw('SUM(final_total) as total_amount'),
            DB::raw('SUM(transaction_payments.amount) as total_paid'),
            DB::raw('SUM(total_tax) as total_taxes'),
        )->first();

        $sale_return_query = Transaction::leftjoin('stores', 'transactions.store_id', 'stores.id')
            ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.type', 'sell_return')
            ->where('transactions.payment_status', 'paid')
            ->where('transactions.status', 'final');

        if (!empty($request->start_date)) {
            $sale_return_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $sale_return_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_id)) {
            $sale_return_query->where('customer_id', $request->customer_id);
        }
        if (!empty($request->customer_type_id)) {
            $sale_return_query->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $sale_return_query->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $sale_return_query->where('store_pos_id', $pos_id);
        }
        if (!empty($request->product_id)) {
            $sale_return_query->where('product_id', $request->product_id);
        }

        $sale_returns = $sale_return_query->select(
            DB::raw('COUNT(transactions.id) as total_count'),
            DB::raw('SUM(final_total) as total_amount'),
            DB::raw('SUM(transaction_payments.amount) as total_paid'),
            DB::raw('SUM(total_tax) as total_taxes'),
        )->first();

        $purchase_return_query = Transaction::leftjoin('stores', 'transactions.store_id', 'stores.id')
            ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('customers', 'transactions.customer_id', 'customers.id')
            ->where('transactions.type', 'purchase_return')
            ->where('transactions.payment_status', 'paid')
            ->where('transactions.status', 'final');

        if (!empty($request->start_date)) {
            $purchase_return_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $purchase_return_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_id)) {
            $purchase_return_query->where('customer_id', $request->customer_id);
        }
        if (!empty($request->customer_type_id)) {
            $purchase_return_query->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $purchase_return_query->where('store_id', $store_id);
        }

        if (!empty($request->product_id)) {
            $purchase_return_query->where('product_id', $request->product_id);
        }

        $purchase_returns = $purchase_return_query->select(
            DB::raw('COUNT(transactions.id) as total_count'),
            DB::raw('SUM(final_total) as total_amount'),
            DB::raw('SUM(transaction_payments.amount) as total_paid'),
            DB::raw('SUM(total_tax) as total_taxes'),
        )->first();

        $payment_received_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')

            ->where('type', 'sell')->where('status', 'final');

        if (!empty($request->start_date)) {
            $payment_received_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $payment_received_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_id)) {
            $payment_received_query->where('customer_id', $request->customer_id);
        }
        if (!empty($request->customer_type_id)) {
            $payment_received_query->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $payment_received_query->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $payment_received_query->where('store_pos_id', $pos_id);
        }
        if (!empty($request->product_id)) {
            $payment_received_query->where('product_id', $request->product_id);
        }

        $payment_received = $payment_received_query->select(
            DB::raw("SUM(IF(method='cash', amount, 0)) as total_cash"),
            DB::raw("SUM(IF(method='card', amount, 0)) as total_card"),
            DB::raw("SUM(IF(method='cheque', amount, 0)) as total_cheque"),
            DB::raw("SUM(IF(method='bank_transfer', amount, 0)) as total_bank_transfer"),
            DB::raw("SUM(IF(method='gift_card', amount, 0)) as total_gift_card"),
            DB::raw("SUM(IF(method='paypal', amount, 0)) as total_paypal"),
            DB::raw("SUM(IF(method='deposit', amount, 0)) as total_deposit"),
            DB::raw('COUNT(transaction_payments.id) total_count'),
            DB::raw('SUM(transaction_payments.amount) total_amount')
        )->first();

        $payment_sent = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')

            ->where('type', 'add_stock')->where('status', 'final');

        if (!empty($request->start_date)) {
            $payment_sent->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $payment_sent->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_id)) {
            $payment_sent->where('customer_id', $request->customer_id);
        }
        if (!empty($request->customer_type_id)) {
            $payment_sent->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $payment_sent->where('store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $payment_sent->where('store_pos_id', $pos_id);
        }
        if (!empty($request->product_id)) {
            $payment_sent->where('product_id', $request->product_id);
        }

        $payment_sent = $payment_sent->select(
            DB::raw("SUM(IF(method='cash', amount, 0)) as total_cash"),
            DB::raw("SUM(IF(method='card', amount, 0)) as total_card"),
            DB::raw("SUM(IF(method='cheque', amount, 0)) as total_cheque"),
            DB::raw('COUNT(transaction_payments.id) total_count'),
            DB::raw('SUM(transaction_payments.amount) total_amount')
        )->first();

        $expense_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('expense_categories', 'transactions.expense_category_id', 'expense_categories.id')
            ->where('transactions.type', 'expense')
            ->where('transactions.status', 'final');

        if (!empty($request->start_date)) {
            $expense_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $expense_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_type_id)) {
            $expense_query->where('customer_type_id', $request->customer_type_id);
        }
        if (!empty($store_id)) {
            $expense_query->where('store_id', $store_id);
        }

        $expenses = $expense_query->select(
            DB::raw('COUNT(transaction_payments.id) total_count'),
            DB::raw('SUM(transaction_payments.amount) as total_amount')
        )->first();

        $wages_query = WagesAndCompensation::where('id', '>', 0);

        if (!empty($request->start_date)) {
            $wages_query->where('payment_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $wages_query->where('payment_date', '<=', $request->end_date);
        }
        if (!empty($request->employee_id)) {
            $wages_query->where('employee_id', $request->employee_id);
        }
        if (!empty($request->payment_type)) {
            $wages_query->where('payment_type', $request->payment_type);
        }

        $wages = $wages_query->select(
            DB::raw('COUNT(wages_and_compensation.id) total_count'),
            DB::raw('SUM(wages_and_compensation.net_amount) as total_amount')
        )->first();


        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');
        $products = Product::pluck('name', 'id');
        $customer_types = CustomerType::getDropdown();
        $suppliers = Supplier::pluck('name', 'id');
        $employees = Employee::pluck('employee_name', 'id');
        $wages_payment_types = WagesAndCompensation::getPaymentTypes();

        return view('reports.summary_report')->with(compact(
            'add_stocks',
            'sales',
            'sale_returns',
            'purchase_returns',
            'wages_payment_types',
            'payment_received',
            'payment_sent',
            'expenses',
            'wages',
            'store_pos',
            'products',
            'suppliers',
            'employees',
            'stores',
            'customer_types',
        ));
    }

    /**
     * show the best selling product
     *
     * @return view
     */
    public function getBestSellerReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $start = strtotime(date("Y-m", strtotime("-2 months")) . '-01');
        $end = strtotime(date("Y") . '-' . date("m") . '-31');

        $product = [];
        $sold_qty = [];
        while ($start <= $end) {
            $start_date = date("Y-m", $start) . '-' . '01';
            $end_date = date("Y-m", $start) . '-' . '31';

            $best_selling_query = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->whereDate('transaction_date', '>=', $start_date)
                ->whereDate('transaction_date', '<=', $end_date);

            if (!empty($store_id)) {
                $best_selling_query->where('store_id', $store_id);
            }

            $best_selling = $best_selling_query->select(DB::raw('product_id, sum(quantity) as sold_qty'))
                ->groupBy('product_id')
                ->orderBy('sold_qty', 'desc')
                ->first();


            if (!empty($best_selling)) {
                $product_data = Product::find($best_selling->product_id);
                if (!empty($product_data)) {
                    $product[] = $product_data->name . ': ' . $product_data->sku;
                    $sold_qty[] = $best_selling->sold_qty;
                }
            }
            $start = strtotime("+1 month", $start);
        }

        $stores = Store::getDropdown();

        return view('reports.best_seller')->with(compact(
            'stores',
            'product',
            'sold_qty'
        ));
    }

    /**
     * show the product report
     *
     * @return view
     */
    public function getProductReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $query = Transaction::leftjoin('transaction_sell_lines as tsl', function ($join) {
            $join->on('transactions.id', 'tsl.transaction_id');
        })->leftjoin('add_stock_lines as pl', function ($join) {
            $join->on('transactions.id', 'pl.transaction_id');
        })
            ->join('products as p', function ($join) {
                $join->on('pl.product_id', 'p.id')
                    ->orOn('tsl.product_id', 'p.id');
            })
            ->whereIn('transactions.type', ['sell', 'add_stock'])
            ->where('transactions.payment_status', 'paid')
            ->whereIn('transactions.status', ['final', 'received']);

        if (!empty($request->start_date)) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_id)) {
            $query->where('transactions.customer_id', $request->customer_id);
        }
        if (!empty($request->customer_type_id)) {
            $query->where('customer_type_id', $request->customer_type_id);
        }
        $store_query = '';

        if (!empty($store_id)) {
            $query->where('transactions.store_id', $store_id);
            $store_query = 'AND store_id=' . $store_id;
        }

        if (!empty($request->product_id)) {
            $query->where('p.id', $request->product_id);
        }
        $transactions = $query->select(
            DB::raw("SUM(IF(transactions.type='sell', final_total, 0)) as sold_amount"),
            DB::raw("SUM(IF(transactions.type='add_stock', final_total, 0)) as purchased_amount"),
            DB::raw("SUM(IF(transactions.type='sell', tsl.quantity, 0)) as sold_qty"),
            DB::raw("SUM(IF(transactions.type='add_stock', pl.quantity, 0)) as purchased_qty"),
            DB::raw('(SELECT SUM(product_stores.qty_available) FROM product_stores JOIN products ON product_stores.product_id=products.id WHERE products.id=p.id ' . $store_query . ') as in_stock'),
            'p.name as product_name',
            'p.id'
        )->groupBy('p.id')->get();

        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');
        $products = Product::pluck('name', 'id');

        return view('reports.product_report')->with(compact(
            'transactions',
            'store_pos',
            'products',
            'stores',
        ));
    }

    /**
     * show the daily sale report
     *
     * @return view
     */
    public function getDailySaleReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        $year = request()->year;
        $month = request()->month;

        if (empty($year)) {
            $year = Carbon::now()->year;
        }
        if (empty($month)) {
            $month = Carbon::now()->month;
        }
        $start = 1;
        $number_of_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        while ($start <= $number_of_day) {
            if ($start < 10)
                $date = $year . '-' . $month . '-0' . $start;
            else
                $date = $year . '-' . $month . '-' . $start;
            $query1 = array(
                'SUM(discount_amount) AS total_discount',
                'SUM(total_tax) AS total_tax',
                'SUM(delivery_cost) AS shipping_cost',
                'SUM(final_total) AS grand_total'
            );
            $query = Transaction::where('type', 'sell')->where('status', 'final')->whereDate('transaction_date', $date);

            if (!empty($store_id)) {
                $query->where('store_id', $store_id);
            }
            $sale_data = $query->selectRaw(implode(',', $query1))->first();
            $total_discount[$start] = $sale_data->total_discount;
            $order_discount[$start] = $sale_data->order_discount;
            $total_tax[$start] = $sale_data->total_tax;
            $order_tax[$start] = $sale_data->order_tax;
            $shipping_cost[$start] = $sale_data->shipping_cost;
            $grand_total[$start] = $sale_data->grand_total;
            $start++;
        }
        $start_day = date('w', strtotime($year . '-' . $month . '-01')) + 1;
        $prev_year = date('Y', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $prev_month = date('m', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $next_year = date('Y', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));
        $next_month = date('m', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));

        $stores = Store::getDropdown();

        return view('reports.daily_sale_report', compact(
            'total_discount',
            'order_discount',
            'total_tax',
            'order_tax',
            'shipping_cost',
            'grand_total',
            'start_day',
            'year',
            'month',
            'number_of_day',
            'prev_year',
            'prev_month',
            'next_year',
            'next_month',
            'stores',
            'store_id'
        ));
    }

    /**
     * show the monthly sale report
     *
     * @return view
     */
    public function getMonthlySaleReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        $year = request()->year;

        if (empty($year)) {
            $year = Carbon::now()->year;
        }

        $start = strtotime($year . '-01-01');
        $end = strtotime($year . '-12-31');

        while ($start <= $end) {
            $start_date = $year . '-' . date('m', $start) . '-' . '01';
            $end_date = $year . '-' . date('m', $start) . '-' . '31';

            $total_discount_query = Transaction::where('type', 'sell')->where('status', 'final')->whereDate('transaction_date', '>=', $start_date)->whereDate('transaction_date', '<=', $end_date);
            if (!empty($store_id)) {
                $total_discount_query->where('store_id', $store_id);
            }
            $total_discount[] = $total_discount_query->sum('discount_amount');

            $total_tax_query = Transaction::where('type', 'sell')->where('status', 'final')->whereDate('transaction_date', '>=', $start_date)->whereDate('transaction_date', '<=', $end_date);
            if (!empty($store_id)) {
                $total_tax_query->where('store_id', $store_id);
            }
            $total_tax[] = $total_tax_query->sum('total_tax');

            $shipping_cost_query = Transaction::where('type', 'sell')->where('status', 'final')->whereDate('transaction_date', '>=', $start_date)->whereDate('transaction_date', '<=', $end_date);
            if (!empty($store_id)) {
                $shipping_cost_query->where('store_id', $store_id);
            }
            $shipping_cost[] = $shipping_cost_query->sum('delivery_cost');

            $total_query = Transaction::where('type', 'sell')->where('status', 'final')->whereDate('transaction_date', '>=', $start_date)->whereDate('transaction_date', '<=', $end_date);
            if (!empty($store_id)) {
                $total_query->where('store_id', $store_id);
            }
            $total[] = $total_query->sum('final_total');

            $start = strtotime("+1 month", $start);
        }
        $stores = Store::getDropdown();

        return view('reports.monthly_sale_report', compact(
            'year',
            'total_discount',
            'total_tax',
            'shipping_cost',
            'total',
            'stores'
        ));
    }
    /**
     * show the daily purchase report
     *
     * @return view
     */
    public function getDailyPurchaseReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        $year = request()->year;
        $month = request()->month;

        if (empty($year)) {
            $year = Carbon::now()->year;
        }
        if (empty($month)) {
            $month = Carbon::now()->month;
        }
        $start = 1;
        $number_of_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        while ($start <= $number_of_day) {
            if ($start < 10)
                $date = $year . '-' . $month . '-0' . $start;
            else
                $date = $year . '-' . $month . '-' . $start;
            $query1 = array(
                'SUM(discount_amount) AS total_discount',
                'SUM(total_tax) AS total_tax',
                'SUM(delivery_cost) AS shipping_cost',
                'SUM(final_total) AS grand_total'
            );
            $query = Transaction::where('type', 'add_stock')->where('status', 'received')->whereDate('transaction_date', $date);

            if (!empty($store_id)) {
                $query->where('store_id', $store_id);
            }
            $purchase_data = $query->selectRaw(implode(',', $query1))->first();
            $total_discount[$start] = $purchase_data->total_discount;
            $order_discount[$start] = $purchase_data->order_discount;
            $total_tax[$start] = $purchase_data->total_tax;
            $order_tax[$start] = $purchase_data->order_tax;
            $shipping_cost[$start] = $purchase_data->shipping_cost;
            $grand_total[$start] = $purchase_data->grand_total;
            $start++;
        }
        $start_day = date('w', strtotime($year . '-' . $month . '-01')) + 1;
        $prev_year = date('Y', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $prev_month = date('m', strtotime('-1 month', strtotime($year . '-' . $month . '-01')));
        $next_year = date('Y', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));
        $next_month = date('m', strtotime('+1 month', strtotime($year . '-' . $month . '-01')));

        $stores = Store::getDropdown();

        return view('reports.daily_purchase_report', compact(
            'total_discount',
            'order_discount',
            'total_tax',
            'order_tax',
            'shipping_cost',
            'grand_total',
            'start_day',
            'year',
            'month',
            'number_of_day',
            'prev_year',
            'prev_month',
            'next_year',
            'next_month',
            'stores',
            'store_id'
        ));
    }

    /**
     * show the monthly purchase report
     *
     * @return view
     */
    public function getMonthlyPurchaseReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $year = request()->year;

        if (empty($year)) {
            $year = Carbon::now()->year;
        }

        $start = strtotime($year . '-01-01');
        $end = strtotime($year . '-12-31');

        while ($start <= $end) {
            $start_date = $year . '-' . date('m', $start) . '-' . '01';
            $end_date = $year . '-' . date('m', $start) . '-' . '31';

            $total_discount_query = Transaction::where('type', 'add_stock')->where('status', 'received')->whereDate('transaction_date', '>=', $start_date)->whereDate('transaction_date', '<=', $end_date);
            if (!empty($store_id)) {
                $total_discount_query->where('store_id', $store_id);
            }
            $total_discount[] = $total_discount_query->sum('discount_amount');

            $total_tax_query = Transaction::where('type', 'add_stock')->where('status', 'received')->whereDate('transaction_date', '>=', $start_date)->whereDate('transaction_date', '<=', $end_date);
            if (!empty($store_id)) {
                $total_tax_query->where('store_id', $store_id);
            }
            $total_tax[] = $total_tax_query->sum('total_tax');

            $shipping_cost_query = Transaction::where('type', 'add_stock')->where('status', 'received')->whereDate('transaction_date', '>=', $start_date)->whereDate('transaction_date', '<=', $end_date);
            if (!empty($store_id)) {
                $shipping_cost_query->where('store_id', $store_id);
            }
            $shipping_cost[] = $shipping_cost_query->sum('delivery_cost');

            $total_query = Transaction::where('type', 'add_stock')->where('status', 'received')->whereDate('transaction_date', '>=', $start_date)->whereDate('transaction_date', '<=', $end_date);
            if (!empty($store_id)) {
                $total_query->where('store_id', $store_id);
            }
            $total[] = $total_query->sum('final_total');

            $start = strtotime("+1 month", $start);
        }
        $stores = Store::getDropdown();

        return view('reports.monthly_purchase_report', compact(
            'year',
            'total_discount',
            'total_tax',
            'shipping_cost',
            'total',
            'stores'
        ));
    }

    /**
     * show the sale report
     *
     * @return view
     */
    public function getSaleReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $query = Transaction::leftjoin('transaction_sell_lines as tsl', function ($join) {
            $join->on('transactions.id', 'tsl.transaction_id');
        })
            ->leftjoin('products as p', function ($join) {
                $join->on('tsl.product_id', 'p.id');
            })
            ->whereIn('transactions.type', ['sell'])
            ->where('transactions.payment_status', 'paid')
            ->whereIn('transactions.status', ['final']);

        if (!empty($request->start_date)) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_id)) {
            $query->where('transactions.customer_id', $request->customer_id);
        }
        if (!empty($request->customer_type_id)) {
            $query->where('customer_type_id', $request->customer_type_id);
        }
        $store_query = '';
        if (!empty($store_id)) {
            $query->where('transactions.store_id',  $store_id);
            $store_query = 'AND store_id=' . $store_id;
        }
        if (!empty($pos_id)) {
            $query->where('store_pos_id', $pos_id);
        }
        if (!empty($request->product_id)) {
            $query->where('tsl.product_id', $request->product_id);
        }

        $transactions = $query->select(
            DB::raw("SUM(IF(transactions.type='sell', final_total, 0)) as sold_amount"),
            DB::raw("SUM(IF(transactions.type='sell', tsl.quantity, 0)) as sold_qty"),
            DB::raw('(SELECT SUM(product_stores.qty_available) FROM product_stores JOIN products ON product_stores.product_id=products.id WHERE products.id=p.id ' . $store_query . ') as in_stock'),
            'p.name as product_name'
        )->groupBy('p.id')->get();

        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');
        $products = Product::pluck('name', 'id');

        return view('reports.sale_report')->with(compact(
            'transactions',
            'store_pos',
            'products',
            'stores',
        ));
    }
    /**
     * show the purchase report
     *
     * @return view
     */
    public function getPurchaseReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        $products = Product::pluck('name', 'id');

        $transactions = [];
        foreach ($products as $key => $value) {
            $query = Transaction::leftjoin('add_stock_lines', 'transactions.id', 'add_stock_lines.transaction_id')
                ->where('add_stock_lines.product_id', $key);

            if (!empty($request->start_date)) {
                $query->where('transaction_date', '>=', $request->start_date);
            }
            if (!empty($request->end_date)) {
                $query->where('transaction_date', '<=', $request->end_date);
            }
            if (!empty($request->customer_id)) {
                $query->where('transactions.customer_id', $request->customer_id);
            }
            if (!empty($request->customer_type_id)) {
                $query->where('customer_type_id', $request->customer_type_id);
            }
            $store_query = '';
            if (!empty($store_id)) {
                $query->where('transactions.store_id', $store_id);
                $store_query = 'AND store_id=' . $store_id;
            }
            if (!empty($request->product_id)) {
                $query->where('add_stock_lines.product_id', $request->product_id);
            }

            $trans = $query->select(
                'add_stock_lines.product_id as id',
                DB::raw('SUM(add_stock_lines.sub_total) as total_purchase'),
                DB::raw('SUM(add_stock_lines.quantity) as total_qty'),
                DB::raw('(SELECT SUM(product_stores.qty_available) FROM product_stores JOIN products ON product_stores.product_id=products.id WHERE products.id=add_stock_lines.product_id ' . $store_query . ') as in_stock'),
            )->groupBy('add_stock_lines.product_id')->first();

            $transactions[$key] = $trans;
        }

        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');


        return view('reports.purchase_report')->with(compact(
            'transactions',
            'store_pos',
            'products',
            'stores',
        ));
    }

    /**
     * show the payment report
     *
     * @return view
     */
    public function getPaymentReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('users', 'transactions.created_by', 'users.id')
            ->whereIn('transactions.type', ['sell', 'add_stock'])
            ->where('transactions.payment_status', 'paid')
            ->whereIn('transactions.status', ['final', 'received']);

        if (!empty($request->start_date)) {
            $query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $query->where('store_id', $store_id);
        }


        $transactions = $query->select(
            'transactions.*',
            'transaction_payments.method',
            'transaction_payments.amount',
            'transaction_payments.ref_number',
            'transaction_payments.paid_on',
            'users.name as created_by_name',
        )->groupBy('transaction_payments.id')->get();

        $stores = Store::getDropdown();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('reports.payment_report')->with(compact(
            'transactions',
            'payment_types',
            'stores',
        ));
    }

    /**
     * show the store report
     *
     * @return view
     */
    public function getStoreReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        $sale_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final']);

        if (!empty($request->start_date)) {
            $sale_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $sale_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $sale_query->where('transactions.store_id', $store_id);
        }


        $sales = $sale_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();

        $add_stock_query = Transaction::whereIn('transactions.type', ['add_stock'])
            ->whereIn('transactions.status', ['received']);

        if (!empty($request->start_date)) {
            $add_stock_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $add_stock_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $add_stock_query->where('transactions.store_id', $store_id);
        }
        $add_stocks = $add_stock_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $quotation_query = Transaction::whereIn('transactions.type', ['sell'])
            ->where('is_quotation', 1);

        if (!empty($request->start_date)) {
            $quotation_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $quotation_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $quotation_query->where('transactions.store_id', $store_id);
        }
        $quotations = $quotation_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $return_query = Transaction::whereIn('transactions.type', ['sell_return'])->whereNotNull('return_parent_id')
            ->whereIn('transactions.status', ['final']);

        if (!empty($request->start_date)) {
            $return_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $return_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $return_query->where('transactions.store_id', $store_id);
        }
        $sell_returns = $return_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();

        $expense_query = Transaction::whereIn('transactions.type', ['expense']);

        if (!empty($request->start_date)) {
            $expense_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $expense_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $expense_query->where('transactions.store_id', $store_id);
        }
        $expenses = $expense_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $stores = Store::getDropdown();
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('reports.store_report')->with(compact(
            'sales',
            'quotations',
            'add_stocks',
            'sell_returns',
            'expenses',
            'payment_types',
            'stores',
        ));
    }

    /**
     * store stock chart
     *
     * @return void
     */
    public function getStoreStockChart(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        $item_query = ProductStore::where('qty_available', '>', 0);
        if (!empty($store_id)) {
            $item_query->where('store_id', $store_id);
        }
        $total_item = $item_query->count();



        $qty_query = ProductStore::where('qty_available', '>', 0);
        if (!empty($store_id)) {
            $qty_query->where('store_id', $store_id);
        }
        $total_qty = $qty_query->sum('qty_available');


        $price_query = Product::leftjoin('product_stores', 'products.id', 'product_stores.product_id');
        if (!empty($store_id)) {
            $price_query->where('store_id', $store_id);
        }
        $total_price =  $price_query->select(DB::raw('SUM(qty_available * price) as total_price'))->first()->total_price;



        $cost_query = Product::leftjoin('product_stores', 'products.id', 'product_stores.product_id');
        if (!empty($store_id)) {
            $cost_query->where('store_id', $store_id);
        }
        $total_cost =  $cost_query->select(DB::raw('SUM(qty_available * purchase_price) as total_cost'))->first()->total_cost;
        $stores = Store::getDropdown();


        return view('reports.store_stock_chart', compact(
            'total_item',
            'total_qty',
            'total_price',
            'total_cost',
            'stores',
        ));
    }

    /**
     * product quantity alert report
     *
     * @return void
     */
    public function getProductQuantityAlertReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        $query = Product::leftjoin('product_stores', 'products.id', 'product_stores.product_id')
            ->select(DB::raw('SUM(qty_available) as qty'), 'products.*')
            ->having('qty', '<', 'alert_quantity');
        if (!empty($store_id)) {
            $query->where('store_id',  $store_id);
        }
        if (!empty($request->product_id)) {
            $query->where('products.id', $request->product_id);
        }

        $items = $query->groupBy('products.id')->get();
        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');
        $products = Product::pluck('name', 'id');

        return view('reports.product_quantity_alert_report')->with(compact(
            'items',
            'stores',
            'store_pos',
            'products'
        ));
    }

    /**
     * show the user report
     *
     * @return view
     */
    public function getUserReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $user_id = $request->user_id;
        if (empty($user_id)) {
            $user_id = Auth::user()->id;
        }

        $sale_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final']);

        if (!empty($request->start_date)) {
            $sale_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $sale_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($user_id)) {
            $sale_query->where('transactions.created_by', $user_id);
        }


        $sales = $sale_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();

        $add_stock_query = Transaction::whereIn('transactions.type', ['add_stock'])
            ->whereIn('transactions.status', ['received']);

        if (!empty($request->start_date)) {
            $add_stock_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $add_stock_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $add_stock_query->where('transactions.store_id',  $store_id);
        }
        if (!empty($user_id)) {
            $add_stock_query->where('transactions.created_by', $user_id);
        }

        $add_stocks = $add_stock_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $quotation_query = Transaction::whereIn('transactions.type', ['sell'])
            ->where('is_quotation', 1);

        if (!empty($request->start_date)) {
            $quotation_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $quotation_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $quotation_query->where('transactions.store_id',  $store_id);
        }
        if (!empty($user_id)) {
            $quotation_query->where('transactions.created_by', $user_id);
        }

        $quotations = $quotation_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $return_query = Transaction::whereIn('transactions.type', ['sell_return'])->whereNotNull('return_parent_id')
            ->whereIn('transactions.status', ['final']);

        if (!empty($request->start_date)) {
            $return_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $return_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $return_query->where('transactions.store_id',  $store_id);
        }
        if (!empty($user_id)) {
            $return_query->where('transactions.created_by', $user_id);
        }

        $sell_returns = $return_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();

        $expense_query = Transaction::whereIn('transactions.type', ['expense']);

        if (!empty($request->start_date)) {
            $expense_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $expense_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $expense_query->where('transactions.store_id',  $store_id);
        }
        if (!empty($user_id)) {
            $expense_query->where('transactions.expense_beneficiary_id', $user_id);
        }

        $expenses = $expense_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $users = User::pluck('name', 'id');
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('reports.user_report')->with(compact(
            'sales',
            'quotations',
            'add_stocks',
            'sell_returns',
            'expenses',
            'payment_types',
            'users'
        ));
    }

    public function getCustomerReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        $customer_id = $request->customer_id;

        $sale_query = Transaction::whereIn('transactions.type', ['sell'])
            ->whereIn('transactions.status', ['final']);

        if (!empty($request->start_date)) {
            $sale_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $sale_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($customer_id)) {
            $sale_query->where('transactions.customer_id', $customer_id);
        }


        $sales = $sale_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $payment_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('users', 'transactions.created_by', 'users.id')
            ->whereIn('transactions.type', ['sell'])
            ->where('transactions.payment_status', 'paid')
            ->whereIn('transactions.status', ['final']);

        if (!empty($request->start_date)) {
            $payment_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $payment_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->customer_id)) {
            $payment_query->where('customer_id',  $request->customer_id);
        }


        $payments = $payment_query->select(
            'transactions.*',
            'transaction_payments.method',
            'transaction_payments.amount',
            'transaction_payments.ref_number',
            'transaction_payments.paid_on',
            'users.name as created_by_name',
        )->groupBy('transaction_payments.id')->get();

        $quotation_query = Transaction::whereIn('transactions.type', ['sell'])
            ->where('is_quotation', 1);

        if (!empty($request->start_date)) {
            $quotation_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $quotation_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $quotation_query->where('transactions.store_id', $store_id);
        }
        if (!empty($customer_id)) {
            $quotation_query->where('transactions.customer_id', $customer_id);
        }


        $quotations = $quotation_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $return_query = Transaction::whereIn('transactions.type', ['sell_return'])->whereNotNull('return_parent_id')
            ->whereIn('transactions.status', ['final']);

        if (!empty($request->start_date)) {
            $return_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $return_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $return_query->where('transactions.store_id', $store_id);
        }
        if (!empty($customer_id)) {
            $return_query->where('transactions.customer_id', $customer_id);
        }

        $sell_returns = $return_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();

        $customers = Customer::pluck('name', 'id');
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('reports.customer_report')->with(compact(
            'sales',
            'payments',
            'quotations',
            'sell_returns',
            'payment_types',
            'customers'
        ));
    }

    /**
     * show the supplier report
     *
     * @return view
     */
    public function getSupplierReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $supplier_id = $request->supplier_id;
        $add_stock_query = Transaction::whereIn('transactions.type', ['add_stock'])
            ->whereIn('transactions.status', ['received']);

        if (!empty($request->start_date)) {
            $add_stock_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $add_stock_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $add_stock_query->where('transactions.store_id', $store_id);
        }
        if (!empty($supplier_id)) {
            $add_stock_query->where('transactions.supplier_id', $supplier_id);
        }

        $add_stocks = $add_stock_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $payment_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->leftjoin('users', 'transactions.created_by', 'users.id')
            ->whereIn('transactions.type', ['add_stock'])
            ->where('transactions.payment_status', 'paid')
            ->whereIn('transactions.status', ['received']);

        if (!empty($request->start_date)) {
            $payment_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $payment_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($request->supplier_id)) {
            $payment_query->where('supplier_id',  $request->supplier_id);
        }


        $payments = $payment_query->select(
            'transactions.*',
            'transaction_payments.method',
            'transaction_payments.amount',
            'transaction_payments.ref_number',
            'transaction_payments.paid_on',
            'users.name as created_by_name',
        )->groupBy('transaction_payments.id')->get();


        $po_query = Transaction::where('type', 'purchase_order')->where('status', '!=', 'draft');

        if (!empty($request->start_date)) {
            $po_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $po_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $po_query->where('transactions.store_id', $store_id);
        }
        if (!empty($supplier_id)) {
            $po_query->where('transactions.supplier_id', $supplier_id);
        }

        $purchase_orders = $po_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $return_query = Transaction::whereIn('transactions.type', ['purchase_return'])
            ->whereIn('transactions.status', ['final']);

        if (!empty($request->start_date)) {
            $return_query->where('transaction_date', '>=', $request->start_date);
        }
        if (!empty($request->end_date)) {
            $return_query->where('transaction_date', '<=', $request->end_date);
        }
        if (!empty($store_id)) {
            $return_query->where('transactions.store_id', $store_id);
        }
        if (!empty($supplier_id)) {
            $return_query->where('transactions.supplier_id', $supplier_id);
        }

        $purchase_returns = $return_query->select(
            'transactions.*'
        )->groupBy('transactions.id')->get();


        $suppliers = Supplier::pluck('name', 'id');
        $payment_types = $this->commonUtil->getPaymentTypeArrayForPos();

        return view('reports.supplier_report')->with(compact(
            'purchase_orders',
            'add_stocks',
            'payments',
            'purchase_returns',
            'payment_types',
            'suppliers'
        ));
    }

    /**
     * show the due amount report
     *
     * @param Request $request
     * @return void
     */
    public function getDueReport(Request $request)
    {
        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];
        $pos_id = $this->transactionUtil->getFilterOptionValues($request)['pos_id'];

        $query = Transaction::where('type', 'sell')->where('payment_status', '!=', 'paid')->where('status', 'final');
        if (!empty($store_id)) {
            $query->where('transactions.store_id', $store_id);
        }
        if (!empty($pos_id)) {
            $query->where('transactions.store_pos_id', $pos_id);
        }

        $dues =  $query->get();
        $stores = Store::getDropdown();
        $store_pos = StorePos::pluck('name', 'id');

        return view('reports.due_report')->with(compact(
            'dues',
            'stores',
            'store_pos'
        ));
    }
}
