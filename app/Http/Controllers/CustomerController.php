<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
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
        $customers = Customer::all();

        return view('customer.index')->with(compact(
            'customers'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $customer_types = CustomerType::pluck('name', 'id');

        $quick_add = request()->quick_add ?? null;

        if ($quick_add) {
            return view('customer.quick_add')->with(compact(
                'customer_types',
                'quick_add'
            ));
        }

        return view('customer.create')->with(compact(
            'customer_types',
            'quick_add'
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
            ['mobile_number' => ['required', 'max:255']],
            ['customer_type_id' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', 'quick_add');
            $data['created_by'] = Auth::user()->id;

            DB::beginTransaction();
            $customer = Customer::create($data);

            if ($request->has('image')) {
                $customer->addMedia($request->image)->toMediaCollection('customer_photo');
            }

            $customer_id = $customer->id;

            DB::commit();
            $output = [
                'success' => true,
                'customer_id' => $customer_id,
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

        return redirect()->to('customer')->with('status', $output);
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
        $customer = Customer::find($id);
        $customer_types = CustomerType::pluck('name', 'id');

        return view('customer.edit')->with(compact(
            'customer',
            'customer_types',
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
            ['mobile_number' => ['required', 'max:255']],
            ['customer_type_id' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', '_method');

            DB::beginTransaction();
            $customer = Customer::find($id);
            $customer->update($data);

            if ($request->has('image')) {
                if ($customer->getFirstMedia('customer_photo')) {
                    $customer->getFirstMedia('customer_photo')->delete();
                }
                $customer->addMedia($request->image)->toMediaCollection('customer_photo');
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

        return redirect()->to('customer')->with('status', $output);
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
            Customer::find($id)->delete();
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

    public function getDropdown()
    {
        $customer = Customer::get();
        $html = '';
        if (!empty($append_text)) {
            $html = '<option value="">Please Select</option>';
        }
        foreach ($customer as $value) {
            $html .= '<option value="' . $value->id . '">' . $value->name  . ' ' . $value->mobile_number . '</option>';
        }

        return $html;
    }

    public function getDetailsByTransactionType($customer_id, $type)
    {
        $query = Customer::join('transactions as t', 'customers.id', 't.customer_id')
            ->leftjoin('customer_types', 'customers.customer_type_id', 'customer_types.id')
            ->where('customers.id', $customer_id);
        if ($type == 'sell') {
            $query->select(
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                'customers.name',
                'customers.address',
                'customers.id as customer_id',
                'customer_types.name as customer_type'
            );
        }

        $customer_details = $query->first();
        $customer_details->due = $customer_details->total_invoice - $customer_details->total_paid;

        return $customer_details;
    }
}
