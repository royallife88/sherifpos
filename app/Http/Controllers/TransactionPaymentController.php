<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionPayment;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionPaymentController extends Controller
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
     * addPayment
     *
     * @param integer $transaction_id
     * @return void
     */
    public function addPayment($transaction_id)
    {
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();
        $transaction = Transaction::find($transaction_id);

        return view('transaction_payment.add_payment')->with(compact(
            'payment_type_array',
            'transaction_id',
            'transaction'
        ));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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

            $payment_data = [
                'transaction_payment_id' =>  !empty($request->transaction_payment_id) ? $request->transaction_payment_id : null,
                'transaction_id' =>  $request->transaction_id,
                'amount' => $this->commonUtil->num_uf($request->amount),
                'method' => $request->method,
                'paid_on' => $this->commonUtil->uf_date($data['paid_on']),
                'ref_number' => $request->ref_number,
                'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $this->commonUtil->uf_date($data['bank_deposit_date']) : null,
                'bank_name' => $request->bank_name,
                'card_number' => $request->card_number,
                'card_month' => $request->card_month,
                'card_year' => $request->card_year,
            ];
            $transaction = Transaction::find($request->transaction_id);

            $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);

            if ($request->upload_documents) {
                foreach ($request->file('upload_documents', []) as $key => $doc) {
                    $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                }
            }
            $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);
            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        if (request()->ajax()) {
            return $output;
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $transaction_id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transaction = Transaction::find($id);
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();

        return view('transaction_payment.show')->with(compact(
            'transaction',
            'payment_type_array'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $transaction_payment_id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $payment = TransactionPayment::find($id);
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();

        return view('transaction_payment.edit')->with(compact(
            'payment',
            'payment_type_array'
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
            $data = $request->except('_token');
            $transaction_payment = TransactionPayment::find($id);
            $transaction_id = $transaction_payment->transaction_id;

            $payment_data = [
                'transaction_payment_id' =>  $id,
                'amount' => $this->commonUtil->num_uf($request->amount),
                'method' => $request->method,
                'paid_on' => $data['paid_on'],
                'ref_number' => $request->ref_number,
                'bank_deposit_date' => !empty($data['bank_deposit_date']) ? $data['bank_deposit_date'] : null,
                'bank_name' => $request->bank_name,
            ];
            $transaction = Transaction::find($transaction_id);

            $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);

            if ($request->upload_documents) {
                foreach ($request->file('upload_documents', []) as $key => $doc) {
                    $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                }
            }
            $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);
            $output = [
                'success' => true,
                'msg' => __('lang.success')
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
            $transaction_payment = TransactionPayment::find($id);
            $transaction_id = $transaction_payment->transaction_id;
            $transaction_payment->delete();

            $this->transactionUtil->updateTransactionPaymentStatus($transaction_id);

            $output = [
                'success' => true,
                'msg' => __('lang.success')
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

    /**
     * get the modal of customer pay due
     *
     * @param int $customer_id
     * @return void
     */
    public function getCustomerDue($customer_id)
    {
        $customer = Customer::find($customer_id);

        $due = app('App\Http\Controllers\CustomerController')->getCustomerBalance($customer_id)['balance'];
        $payment_type_array = $this->commonUtil->getPaymentTypeArray();

        return view('transaction_payment.pay_customer_due')->with(compact(
            'payment_type_array',
            'due',
            'customer'
        ));
    }

    /**
     * pay the customer due amounts
     *
     * @param int $customer_id
     * @return void
     */
    public function payCustomerDue(Request $request, $customer_id)
    {
        try {
            $amount = $request->amount;
            $transactions = Transaction::where('customer_id', $customer_id)->where('type', 'sell')->whereIn('payment_status', ['pending', 'partial'])->get();

            DB::beginTransaction();

            foreach ($transactions as $transaction) {
                $due_for_transaction = $this->getDueForTransaction($transaction->id);
                $paid_amount = 0;
                if ($amount > 0) {
                    if ($amount >= $due_for_transaction) {
                        $paid_amount = $due_for_transaction;
                        $amount -= $due_for_transaction;
                    } else if ($amount < $due_for_transaction) {
                        $paid_amount = $amount;
                        $amount = 0;
                    }

                    $payment_data = [
                        'transaction_payment_id' =>  !empty($request->transaction_payment_id) ? $request->transaction_payment_id : null,
                        'transaction_id' =>  $transaction->id,
                        'amount' => $paid_amount,
                        'method' => $request->method,
                        'paid_on' => $this->commonUtil->uf_date($request->paid_on),
                        'ref_number' => $request->ref_number,
                        'bank_deposit_date' => !empty($request->bank_deposit_date) ? $this->commonUtil->uf_date($request->bank_deposit_date) : null,
                        'bank_name' => $request->bank_name,
                    ];

                    $transaction_payment = $this->transactionUtil->createOrUpdateTransactionPayment($transaction, $payment_data);
                    $this->transactionUtil->updateTransactionPaymentStatus($transaction->id);

                    if ($request->upload_documents) {
                        foreach ($request->file('upload_documents', []) as $key => $doc) {
                            $transaction_payment->addMedia($doc)->toMediaCollection('transaction_payment');
                        }
                    }
                }
            }
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang.success')
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
     * calculate the amount due for transaction
     *
     * @param int $transaction_id
     * @return float
     */
    public function getDueForTransaction($transaction_id)
    {
        $transaction = Transaction::find($transaction_id);
        $total_paid = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.id', $transaction_id)
            ->sum('amount');

        return $transaction->final_total - $total_paid;
    }
}
