<?php

namespace App\Http\Controllers;

use App\Models\ExpenseBeneficiary;
use App\Models\ExpenseCategory;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->transactionUtil = $transactionUtil;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $expenses = Transaction::leftjoin('users', 'transactions.created_by', 'users.id')
            ->where('type', 'expense')
            ->select('transactions.*', 'users.name as created_by')
            ->get();

        $expense_categories = ExpenseCategory::pluck('name', 'id');

        return view('expense.index')->with(compact(
            'expenses',
            'expense_categories'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $expense_categories = ExpenseCategory::pluck('name', 'id');
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();

        return view('expense.create')->with(compact(
            'expense_categories',
            'payment_type_array'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->except('_token');


            $expense_data = [
                'grand_total' => $data['amount'],
                'final_total' => $data['amount'],
                'type' => 'expense',
                'expense_category_id' => $data['expense_category_id'],
                'expense_beneficiary_id' => $data['expense_beneficiary_id'],
                'next_payment_date' => !empty($data['next_payment_date']) ? $data['next_payment_date'] : null,
                'details' => !empty($data['details']) ? $data['details'] : null,
                'notify_me' => !empty($data['notify_me']) ? 1 : 0,
                'notify_before_days' => !empty($data['notify_before_days']) ? $data['notify_before_days'] : null
            ];
            $expense_data['created_by'] = Auth::user()->id;

            DB::beginTransaction();
            $expense = Transaction::create($expense_data);

            if ($request->has('upload_documents')) {
                foreach ($request->file('upload_documents', []) as $key => $file) {
                    $expense->addMedia($file)->toMediaCollection('expense');
                }
            }

            $payment_data = [
                'transaction_payment_id' =>  !empty($request->transaction_payment_id) ? $request->transaction_payment_id : null,
                'transaction_id' =>  $expense->id,
                'amount' => $this->commonUtil->num_uf($request->amount),
                'method' => $request->method,
                'paid_on' => $data['paid_on'],
                'ref_number' => $request->ref_number,
                'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $data['bank_deposit_date'] : null, //Test It not saving
                'bank_name' => $request->bank_name,
            ];

            $this->transactionUtil->createOrUpdateTransactionPayment($expense, $payment_data);
            $this->transactionUtil->updateTransactionPaymentStatus($expense->id);
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang.expense_added')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $expense = Transaction::find($id);
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();
        $expense_categories = ExpenseCategory::pluck('name', 'id');
        $expense_beneficiaries = ExpenseBeneficiary::where('expense_category_id', $expense->expense_category_id)->pluck('name', 'id');

        return view('expense.edit')->with(compact(
            'expense',
            'payment_type_array',
            'expense_beneficiaries',
            'expense_categories'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method', 'submit');

            $expense = Transaction::where('id', $id)->first();


            $expense_data = [
                'grand_total' => $data['amount'],
                'final_total' => $data['amount'],
                'type' => 'expense',
                'expense_category_id' => $data['expense_category_id'],
                'expense_beneficiary_id' => $data['expense_beneficiary_id'],
                'next_payment_date' => !empty($data['next_payment_date']) ? $data['next_payment_date'] : null,
                'details' => !empty($data['details']) ? $data['details'] : null,
                'notify_me' => !empty($data['notify_me']) ? 1 : 0,
                'notify_before_days' => !empty($data['notify_before_days']) ? $data['notify_before_days'] : null
            ];
            $expense_data['created_by'] = Auth::user()->id;
            DB::beginTransaction();
            $expense->update($expense_data);

            if ($request->has('upload_documents')) {
                foreach ($request->file('upload_documents', []) as $key => $file) {
                    $expense->addMedia($file)->toMediaCollection('expense');
                }
            }

            $payment_data = [
                'transaction_payment_id' =>  !empty($request->transaction_payment_id) ? $request->transaction_payment_id : null,
                'transaction_id' =>  $expense->id,
                'amount' => $this->commonUtil->num_uf($request->amount),
                'method' => $request->method,
                'paid_on' => $data['paid_on'],
                'ref_number' => $request->ref_number,
                'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $data['bank_deposit_date'] : null,
                'bank_name' => $request->bank_name,
            ];

            $this->transactionUtil->createOrUpdateTransactionPayment($expense, $payment_data);
            $this->transactionUtil->updateTransactionPaymentStatus($expense->id);
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang.expense_updated')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            Transaction::where('id', $id)->delete();
            TransactionPayment::where('transaction_id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('lang.expense_deleted')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return $output;
    }
}
