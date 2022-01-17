<?php

namespace App\Http\Controllers;

use App\Models\CustomerSize;
use App\Models\TransactionCustomerSize;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerSizeController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_sizes = CustomerSize::get();

        return view('customer_size.index')->with(compact(
            'customer_sizes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function add($customer_id)
    {
        $quick_add = request()->quick_add ?? null;

        $customer_sizes = CustomerSize::orderBy('name', 'asc')->pluck('name', 'id');
        $getAttributeListArray = CustomerSize::getAttributeListArray();

        return view('customer_size.create')->with(compact(
            'quick_add',
            'customer_id',
            'customer_sizes',
            'getAttributeListArray'
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

        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
            ['customer_id' => ['required', 'max:255']]
        );

        $customer_id = $request->cusotmer_id;

        $customer_size_exist = CustomerSize::where('name', $request->name)->where('customer_id', $customer_id)->first();

        if (!empty($customer_size_exist)) {
            if ($request->ajax()) {
                return response()->json(array(
                    'success' => false,
                    'message' => 'There are incorect values in the form!',
                    'msg' => 'Size name already taken'
                ));
            }
        }

        try {
            $data = $request->except('_token', 'quick_add');
            $data['created_by'] = auth()->user()->id;

            DB::beginTransaction();
            $customer_size = CustomerSize::create($data);

            $customer_size_id = $customer_size->id;

            DB::commit();
            $output = [
                'success' => true,
                'customer_size_id' => $customer_size_id,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }


        if ($request->quick_add) {
            return $output;
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
        $customer_size = CustomerSize::find($id);
        $getAttributeListArray = CustomerSize::getAttributeListArray();

        return view('customer_size.show')->with(compact(
            'customer_size',
            'getAttributeListArray',
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
        $customer_size = CustomerSize::find($id);
        $getAttributeListArray = CustomerSize::getAttributeListArray();

        return view('customer_size.edit')->with(compact(
            'customer_size',
            'getAttributeListArray',
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
        $this->validate(
            $request,
            ['name' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', '_method');

            DB::beginTransaction();
            $customer_size = CustomerSize::find($id);

            $customer_size->update($data);

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
            CustomerSize::find($id)->delete();
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
     * get dropdown list for customer size
     *
     * @return void
     */
    public function getDropdown()
    {
        if (!empty(request()->customer_id)) {
            $customer_sizes = CustomerSize::where('customer_id', request()->customer_id)->orderBy('name', 'asc')->pluck('name', 'id');
        } else {
            $customer_sizes = CustomerSize::orderBy('name', 'asc')->pluck('name', 'id');
        }

        $customer_sizes_dp = $this->commonUtil->createDropdownHtml($customer_sizes, __('lang.please_select'));

        return $customer_sizes_dp;
    }

    /**
     * print the transaction
     *
     * @param int $id
     * @return html
     */
    public function print($id)
    {
        try {
            $customer_size = CustomerSize::find($id);
            $getAttributeListArray = CustomerSize::getAttributeListArray();

            $html_content = view('customer_size.print')->with(compact(
                'customer_size',
                'getAttributeListArray',
            ))->render();


            $output = [
                'success' => true,
                'html_content' => $html_content,
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

    public function getCustomerSizeDetailsForm($customer_size_id)
    {
        $customer_size = CustomerSize::find($customer_size_id);
        if (!empty(request()->transaction_id)) {
            $transaction_customer_size = TransactionCustomerSize::leftjoin('transactions', 'transaction_customer_sizes.transaction_id', 'transactions.id')
                ->where('customer_size_id', $customer_size_id)
                ->where('transaction_id', request()->transaction_id)
                ->first();
            if (!empty($transaction_customer_size)) {
                $customer_size = $transaction_customer_size;
            }
        }
        $getAttributeListArray = CustomerSize::getAttributeListArray();

        $html_content = view('customer_size.partial.size_details_form')->with(compact(
            'customer_size',
            'getAttributeListArray',
        ))->render();


        $output = [
            'success' => true,
            'html_content' => $html_content,
            'msg' => __('lang.success')
        ];

        return $output;
    }
}
