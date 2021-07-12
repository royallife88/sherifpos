<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Store;
use App\Models\StorePos;
use App\Models\Supplier;
use App\Models\Transaction;
use App\Models\WagesAndCompensation;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
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
        if (!empty($request->store_id)) {
            $sale_query->where('store_id', $request->store_id);
        }
        if (!empty($request->pos_id)) {
            $sale_query->where('store_pos_id', $request->pos_id);
        }
        if (!empty($request->product_id)) {
            $sale_query->where('product_id', $request->product_id);
        }

        $sales = $sale_query->select(
            'stores.name as store_name',
            'stores.id as store_id',
            DB::raw('SUM(transactions.final_total) as total_amount')
        )->groupBy('stores.id')->get();

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
        if (!empty($request->store_id)) {
            $sale_query->where('store_id', $request->store_id);
        }
        if (!empty($request->pos_id)) {
            $sale_query->where('store_pos_id', $request->pos_id);
        }
        if (!empty($request->product_id)) {
            $sale_query->where('product_id', $request->product_id);
        }

        $sales = $sale_query->groupBy('transactions.id')->get();


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
        if (!empty($request->store_id)) {
            $add_stock_query->where('store_id', $request->store_id);
        }
        if (!empty($request->pos_id)) {
            $add_stock_query->where('store_pos_id', $request->pos_id);
        }
        if (!empty($request->product_id)) {
            $add_stock_query->where('product_id', $request->product_id);
        }

        $add_stocks = $add_stock_query->groupBy('transactions.id')->get();


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
        if (!empty($request->store_id)) {
            $sale_query->where('store_id', $request->store_id);
        }
        if (!empty($request->pos_id)) {
            $sale_query->where('store_pos_id', $request->pos_id);
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
        if (!empty($request->store_id)) {
            $add_stock_query->where('store_id', $request->store_id);
        }
        if (!empty($request->pos_id)) {
            $add_stock_query->where('store_pos_id', $request->pos_id);
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
        $wages_payment_types = WagesAndCompensation::getPaymentTypes();

        return view('reports.expected_payable_report')->with(compact(
            'add_stocks',
            'expenses',
            'wages',
            'wages_payment_types',
            'store_pos',
            'products',
            'suppliers',
            'stores',
            'customer_types',
        ));
    }
}
