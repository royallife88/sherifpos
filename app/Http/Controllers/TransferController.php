<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransferLine;
use App\Utils\CashRegisterUtil;
use App\Utils\NotificationUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransferController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $transactionUtil;
    protected $productUtil;
    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, NotificationUtil $notificationUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->notificationUtil = $notificationUtil;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = Transaction::where('type', 'transfer');

        if (!empty(request()->sender_store)) {
            $query->where('sender_store', request()->sender_store);
        }
        if (!empty(request()->receiver_store)) {
            $query->where('receiver_store', request()->receiver_store);
        }
        if (!empty(request()->start_date)) {
            $query->where('transaction_date', '>=', request()->start_date);
        }
        if (!empty(request()->end_date)) {
            $query->whereDate('transaction_date', '<=', request()->end_date);
        }

        $transfers = $query->orderBy('invoice_no', 'desc')->get();
        $stores = Store::getDropdown();


        return view('transfer.index')->with(compact(
            'transfers',
            'stores'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $stores = Store::getDropdown();

        return view('transfer.create')->with(compact(
            'stores'
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

            $transaction_data = [
                'sender_store_id' => $data['sender_store_id'],
                'receiver_store_id' => $data['receiver_store_id'],
                'type' => 'transfer',
                'status' => 'final',
                'transaction_date' => Carbon::now(),
                'final_total' => $this->productUtil->num_uf($data['final_total']),
                'notes' => !empty($data['notes']) ? $data['notes'] : null,
                'details' => !empty($data['details']) ? $data['details'] : null,
                'invoice_no' => $this->productUtil->getNumberByType('transfer'),
                'created_by' => Auth::user()->id
            ];

            DB::beginTransaction();
            $transaction = Transaction::create($transaction_data);

            $this->productUtil->createOrUpdateTransferLines($request->add_stock_lines, $transaction);

            if ($request->files) {
                foreach ($request->file('files', []) as $file) {

                    $transaction->addMedia($file)->toMediaCollection('transfer');
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $transfer = Transaction::find($id);
        $stores = Store::getDropdown();

        return view('transfer.show')->with(compact(
            'stores',
            'transfer'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $transfer = Transaction::find($id);
        $stores = Store::getDropdown();

        return view('transfer.edit')->with(compact(
            'stores',
            'transfer'
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

            $transaction_data = [
                'sender_store_id' => $data['sender_store_id'],
                'receiver_store_id' => $data['receiver_store_id'],
                'type' => 'transfer',
                'status' => 'final',
                'transaction_date' => Carbon::now(),
                'final_total' => $this->productUtil->num_uf($data['final_total']),
                'notes' => !empty($data['notes']) ? $data['notes'] : null,
                'details' => !empty($data['details']) ? $data['details'] : null,
                'invoice_no' => $this->productUtil->getNumberByType('transfer'),
                'created_by' => Auth::user()->id
            ];

            DB::beginTransaction();
            $transaction = Transaction::find($id);
            $transaction->update($transaction_data);

            $this->productUtil->createOrUpdateTransferLines($request->transfer_lines, $transaction);

            if ($request->files) {
                foreach ($request->file('files', []) as $file) {

                    $transaction->addMedia($file)->toMediaCollection('transfer');
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        try {
            $transfer = Transaction::find($id);

            $transfer_lines = TransferLine::where('transaction_id', $id)->get();
            foreach ($transfer_lines as $line) {
                $this->productUtil->decreaseProductQuantity($line->product_id, $line->variation_id, $transfer->receiver_store_id, $line->quantity, 0);
                $this->productUtil->updateProductQuantityStore($line->product_id, $line->variation_id, $transfer->sender_store_id,  $line->quantity, 0);
                $line->delete();
            }
            $transfer->delete();

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
     * Returns the html for product row
     *
     * @return \Illuminate\Http\Response
     */
    public function addProductRow(Request $request)
    {
        if ($request->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $sender_store_id = $request->input('sender_store_id');

            if (!empty($product_id)) {
                $index = $request->input('row_count');
                $products = $this->productUtil->getDetailsFromProductTransfer($sender_store_id, $product_id, $variation_id);

                return view('transfer.partials.product_row')
                    ->with(compact('products', 'index'));
            }
        }
    }
}
