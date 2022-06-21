<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Tax;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaxController extends Controller
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
        $query = Tax::where('id', '>', 0);
        $type = request()->type ?? 'product_tax';

        if (!empty(request()->type)) {
            $query->where('type', request()->type);
        }
        $taxes = $query->get();

        return view('tax.index')->with(compact('taxes', 'type'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $quick_add = request()->quick_add ?? null;
        $type = request()->type ?? 'product_tax';

        $taxes = Tax::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::orderBy('name', 'asc')->pluck('name', 'id');

        return view('tax.create')->with(compact(
            'quick_add',
            'taxes',
            'stores',
            'type'
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
            ['rate' => ['required', 'max:255']],
            ['type' => ['required', 'max:255']]
        );

        try {
            $data = $request->except('_token', 'quick_add');

            DB::beginTransaction();
            if ($data['type'] == 'general_tax') {
                $data['status'] = !empty($data['status']) ? 1 : 0;
                $data['store_ids'] = !empty($data['store_ids']) ? $data['store_ids'] : [];
            } else {
                $data['status'] = 1;
                $data['store_ids'] = [];
            }
            $tax = Tax::create($data);

            $tax_id = $tax->id;

            DB::commit();
            $output = [
                'success' => true,
                'tax_id' => $tax_id,
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
        $tax = Tax::find($id);
        $stores = Store::orderBy('name', 'asc')->pluck('name', 'id');

        return view('tax.edit')->with(compact(
            'tax',
            'stores'
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
            ['name' => ['required', 'max:255']],
            ['rate' => ['required', 'max:255']],
            ['type' => ['required', 'max:255']],
        );

        try {
            $data = $request->except('_token', '_method');

            DB::beginTransaction();
            if ($data['type'] == 'general_tax') {
                $data['status'] = !empty($data['status']) ? 1 : 0;
                $data['store_ids'] = !empty($data['store_ids']) ? $data['store_ids'] : [];
            } else {
                $data['status'] = 1;
                $data['store_ids'] = [];
            }
            $tax = Tax::where('id', $id)->update($data);

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
            Tax::find($id)->delete();
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
     * get dropdown html by store
     *
     * @return void
     */
    public function getDropdownHtmlByStore()
    {
        $store_id = request()->store_id;

        $taxes = Tax::getDropdown($store_id);
        $tax_dp = '<option value="">No Tax</option>';
        foreach ($taxes as $tax) {
            $tax_dp .= '<option data-rate="' . $tax['rate'] . '" value="' . $tax['id'] . '">' . $tax['name'] . '</option>';
        }

        return $tax_dp;
    }
    /**
     * get dropdown html
     *
     * @return void
     */
    public function getDropdown()
    {
        $type = request()->type ?? 'product_tax';
        $query = Tax::orderBy('name', 'asc');
        if (!empty($type)) {
            $query->where('type', $type);
        }
        $tax = $query->pluck('name', 'id');
        $tax_dp = $this->commonUtil->createDropdownHtml($tax, 'Please Select');

        return $tax_dp;
    }
    public function getDetails($id)
    {
        $tax = Tax::find($id);
        return $tax;
    }
}
