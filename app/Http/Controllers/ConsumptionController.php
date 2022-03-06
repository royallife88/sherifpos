<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Consumption;
use App\Models\ConsumptionDetail;
use App\Models\ConsumptionProduct;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductStore;
use App\Models\Store;
use App\Models\Unit;
use App\Models\User;
use App\Models\Variation;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class ConsumptionController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param transactionUtil $transactionUtil
     * @param Util $commonUtil
     * @param ProductUtils $productUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!auth()->user()->can('raw_material_module.consumption.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $consumptions = Consumption::leftjoin('consumption_details', 'consumptions.id', 'consumption_details.consumption_id')
                ->leftjoin('users', 'consumptions.created_by', 'users.id')
                ->leftjoin('users as edit_user', 'consumptions.edited_by', 'edit_user.id')
                ->leftjoin('stores as store', 'consumptions.store_id', 'store.id')
                ->leftjoin('transactions as transaction', 'consumptions.transaction_id', 'transaction.id')
                ->leftjoin('products as raw_material', 'consumptions.raw_material_id', 'raw_material.id')
                ->leftjoin('variations as rwv', 'raw_material.id', 'rwv.product_id')
                ->leftjoin('product_stores', 'consumptions.raw_material_id', 'raw_material.id');


            $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

            $store_query = '';
            if (!empty($store_id)) {
                $consumptions->where('product_stores.store_id', $store_id);
                $store_query = 'AND store_id=' . $store_id;
            }
            if (!empty(request()->start_date)) {
                $consumptions->whereDate('consumptions.date_and_time', '>=', request()->start_date);
            }
            if (!empty(request()->end_date)) {
                $consumptions->whereDate('consumptions.date_and_time', '<=', request()->end_date);
            }
            if (!empty(request()->start_time)) {
                $consumptions->where('consumptions.date_and_time', '>=', request()->start_date . ' ' . Carbon::parse(request()->start_time)->format('H:i:s'));
            }
            if (!empty(request()->end_time)) {
                $consumptions->where('consumptions.date_and_time', '<=', request()->end_date . ' ' . Carbon::parse(request()->end_time)->format('H:i:s'));
            }
            if (!empty(request()->raw_material_id)) {
                $consumptions->where('consumptions.raw_material_id', request()->raw_material_id);
            }
            if (!empty(request()->brand_id)) {
                $consumptions->where('raw_material.brand_id', request()->brand_id);
            }
            if (!empty(request()->variation_id)) {
                $consumptions->where('consumption_details.variation_id', request()->variation_id);
            }
            if (!empty(request()->store_id)) {
                $consumptions->where('consumptions.store_id', request()->store_id);
            }
            if (!empty(request()->created_by)) {
                $consumptions->where('consumptions.created_by', request()->created_by);
            }

            $consumptions = $consumptions->select([
                'consumptions.*',
                'users.name as chef',
                'edit_user.name as edited_by_name',
                'store.name as store_name',
                'raw_material.name as raw_material_name',
                'rwv.default_purchase_price',
                DB::raw('(SELECT SUM(product_stores.qty_available) FROM product_stores JOIN variations as v ON product_stores.variation_id=v.id WHERE v.id=rwv.id ' . $store_query . ') as product_current_stock'),
            ])->groupBy('consumptions.id');

            return DataTables::of($consumptions)
                ->editColumn('product_current_stock', function ($row) {
                    return $this->productUtil->num_f($row->product_current_stock);
                })
                ->addColumn('value_of_current_stock', function ($row) {
                    return $this->productUtil->num_f($row->product_current_stock * $row->default_purchase_price);
                })
                ->addColumn('products', function ($row) {
                    $html = '';
                    foreach ($row->consumption_details as $detail) {
                        if ($detail->quantity > 0) {
                            $product_name = $detail->product->name ? $detail->product->name : '';
                            $variation_name = $detail->variation->name ? $detail->variation->name : '';
                            $html .= $product_name;
                            if ($variation_name != 'Default') {
                                $html .= $variation_name;
                            }
                            $html .= '(' . $this->commonUtil->num_f($detail->quantity) . ')<br>';
                        }
                    }
                    return $html;
                })
                ->addColumn('remaining_qty_sufficient_for', function ($row) {
                    return '<a data-href="' . action('ConsumptionController@getSufficientSuggestions', $row->raw_material_id) . '?store_id=' . $row->store_id . '"
                            data-container=".view_modal" class="btn btn-modal">' . __('lang.view') . '</a>';
                })
                ->addColumn(
                    'action',
                    function ($row) {
                        $html =
                            '<div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">' . __('lang.action') .
                            '<span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">';

                        if (auth()->user()->can('raw_material_module.consumption.view')) {
                            $html .=
                                '<li><a data-href="' . action('ConsumptionController@show', $row->id) . '"
                                data-container=".view_modal" class="btn btn-modal"><i class="fa fa-eye"></i>
                                ' . __('lang.view') . '</a></li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('raw_material_module.consumption.create_and_edit')) {
                            $html .=
                                '<li><a href="' . action('ConsumptionController@edit', $row->id) . '" class="btn"
                            target="_blank"><i class="dripicons-document-edit"></i> ' . __('lang.edit') . '</a></li>';
                        }

                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('raw_material_module.consumption.delete')) {
                            $html .=
                                '<li>
                            <a data-href="' . action('ConsumptionController@destroy', $row->id) . '"
                                data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                                class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                ' . __('lang.delete') . '</a>
                        </li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )

                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("product.view")) {
                            return  action('ConsumptionController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ])
                ->rawColumns([
                    'products',
                    'created_by',
                    'remaining_qty_sufficient_for',
                    'action',
                ])
                ->make(true);
        }

        $stores  = Store::orderBy('name', 'asc')->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $users  = Employee::getDropdownChefs();
        $raw_materials  = Product::where('is_raw_material', 1)->orderBy('name', 'asc')->pluck('name', 'id');
        $products = Product::getProductVariationDropDown(true);

        return view('consumption.index')->with(compact(
            'products',
            'raw_materials',
            'stores',
            'users',
            'brands',
            'units',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('raw_material_module.consumption.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $raw_materials = Product::where('is_raw_material', 1)->active()->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $products = Product::getProductVariationDropDown(true);
        $units = Unit::where('is_raw_material_unit', 1)->orderBy('name', 'asc')->pluck('name', 'id');

        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::orderBy('name', 'asc')->pluck('name', 'id');
        $chefs = Employee::getDropdownChefs();


        return view('consumption.create')->with(compact(
            'raw_materials',
            'products',
            'brands',
            'chefs',
            'units',
            'stores',
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
            $consumption_data['store_id'] = $request->store_id;
            $consumption_data['created_by'] = $request->created_by;
            $consumption_data['consumption_no'] = uniqid('CONM');
            $consumption_data['date_and_time'] = $request->date_and_time;
            $consumption_data['raw_material_id'] = $request->consumption_raw_materials['raw_material_id'];
            $consumption_data['current_stock'] = $request->consumption_raw_materials['current_stock'];

            DB::beginTransaction();
            $consumption = Consumption::create($consumption_data);

            $total_quantity = $this->transactionUtil->createOrUpdateConsumptionDetail($request->consumption_details,  $consumption->id)['total_quantity'];

            $variation_rw = Variation::where('product_id', $consumption_data['raw_material_id'])->first();
            $this->productUtil->decreaseProductQuantity($consumption_data['raw_material_id'], $variation_rw->id, $consumption_data['store_id'], $total_quantity, 0);

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
        if (!auth()->user()->can('raw_material_module.consumption.view')) {
            abort(403, 'Unauthorized action.');
        }

        $raw_materials = Product::where('is_raw_material', 1)->active()->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $products = Product::getProductVariationDropDown(true);
        $units = Unit::where('is_raw_material_unit', 1)->orderBy('name', 'asc')->pluck('name', 'id');

        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::orderBy('name', 'asc')->pluck('name', 'id');
        $chefs = Employee::getDropdownChefs();

        $consumption = Consumption::findOrFail($id);

        $raw_material_details = Product::leftjoin('variations', function ($join) {
            $join->on('products.id', 'variations.product_id')->whereNull('variations.deleted_at');
        })
            ->leftjoin('add_stock_lines', function ($join) {
                $join->on('variations.id', 'add_stock_lines.variation_id')->where('add_stock_lines.expiry_date', '>=', date('Y-m-d'));
            })->where('products.id', $consumption->raw_material_id)
            ->select('batch_number', 'expiry_date')
            ->get();
        $current_stock = ProductStore::where('product_id', $consumption->raw_material_id)->where('store_id', $consumption->store_id)->sum('qty_available');

        return view('consumption.show')->with(compact(
            'consumption',
            'current_stock',
            'raw_material_details',
            'raw_materials',
            'products',
            'brands',
            'chefs',
            'units',
            'stores',
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
        if (!auth()->user()->can('raw_material_module.consumption.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $raw_materials = Product::where('is_raw_material', 1)->active()->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $products = Product::getProductVariationDropDown(true);
        $units = Unit::where('is_raw_material_unit', 1)->orderBy('name', 'asc')->pluck('name', 'id');

        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::orderBy('name', 'asc')->pluck('name', 'id');
        $chefs = Employee::getDropdownChefs();

        $consumption = Consumption::findOrFail($id);

        $raw_material_details = Product::leftjoin('variations', function ($join) {
            $join->on('products.id', 'variations.product_id')->whereNull('variations.deleted_at');
        })
            ->leftjoin('add_stock_lines', function ($join) {
                $join->on('variations.id', 'add_stock_lines.variation_id')->where('add_stock_lines.expiry_date', '>=', date('Y-m-d'));
            })->where('products.id', $consumption->raw_material_id)
            ->select('batch_number', 'expiry_date')
            ->get();
        $current_stock = ProductStore::where('product_id', $consumption->raw_material_id)->where('store_id', $consumption->store_id)->sum('qty_available');

        return view('consumption.edit')->with(compact(
            'consumption',
            'current_stock',
            'raw_material_details',
            'raw_materials',
            'products',
            'brands',
            'chefs',
            'units',
            'stores',
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
            $consumption_data['store_id'] = $request->store_id;
            $consumption_data['created_by'] = $request->created_by;
            $consumption_data['date_and_time'] = $request->date_and_time;
            $consumption_data['raw_material_id'] = $request->consumption_raw_materials['raw_material_id'];
            $consumption_data['current_stock'] = $request->consumption_raw_materials['current_stock'];

            DB::beginTransaction();
            $consumption = Consumption::findOrFail($id);
            $old_raw_material_id = $consumption->raw_material_id;
            $consumption->update($consumption_data);

            $qty_data = $this->transactionUtil->createOrUpdateConsumptionDetail($request->consumption_details,  $consumption->id);
            $total_qunatity =  $qty_data['total_quantity'];
            $old_qty = $qty_data['old_qty'];

            if ($consumption_data['raw_material_id'] == $old_raw_material_id) {
                $variation_rw = Variation::where('product_id', $consumption_data['raw_material_id'])->first();
                $this->productUtil->decreaseProductQuantity($consumption_data['raw_material_id'], $variation_rw->id, $consumption_data['store_id'], $total_qunatity, $old_qty);
            } else {
                $variation_rw = Variation::where('product_id', $old_raw_material_id)->first();
                $this->productUtil->updateProductQuantityStore($old_raw_material_id, $variation_rw->id, $consumption_data['store_id'], $old_qty, 0);

                $variation_rw = Variation::where('product_id', $consumption_data['raw_material_id'])->first();
                $this->productUtil->decreaseProductQuantity($consumption_data['raw_material_id'], $variation_rw->id, $consumption_data['store_id'], $total_qunatity, 0);
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
            $consumption = Consumption::findOrFail($id);
            $raw_material_id = $consumption->raw_material_id;

            $variation_rw = Variation::where('product_id', $raw_material_id)->first();
            $qty = ConsumptionDetail::where('consumption_id', $id)->sum('quantity');
            $this->productUtil->updateProductQuantityStore($raw_material_id, $variation_rw->id, $consumption->store_id, $qty, 0);

            $consumption->delete();
            ConsumptionDetail::where('consumption_id', $id)->delete();

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
    }

    /**
     * add row
     *
     * @return void
     */
    public function addRow()
    {
        $row_id = request()->row_id ?? 0;
        $raw_materials = Product::where('is_raw_material', 1)->active()->pluck('name', 'id');

        return view('consumption.partial.consumption_row')->with(compact(
            'row_id',
            'raw_materials'
        ));
    }

    /**
     * get consumption details row data
     *
     * @return void
     */
    public function getConsumptionDetailRow()
    {
        $this_row_id = request()->this_row_id ?? 0;
        $consumption_products = ConsumptionProduct::where('raw_material_id', request()->raw_material_id)->get();

        $current_stock = ProductStore::where('product_id', request()->raw_material_id)->where('store_id', request()->store_id)->sum('qty_available');

        $raw_material_details = Product::leftjoin('variations', function ($join) {
            $join->on('products.id', 'variations.product_id')->whereNull('variations.deleted_at');
        })
            ->leftjoin('add_stock_lines', function ($join) {
                $join->on('variations.id', 'add_stock_lines.variation_id')->where('add_stock_lines.expiry_date', '>=', date('Y-m-d'));
            })->where('products.id', request()->raw_material_id)
            ->select('batch_number', 'expiry_date')
            ->get();


        return view('consumption.partial.consumption_details_row')->with(compact(
            'this_row_id',
            'consumption_products',
            'raw_material_details',
            'current_stock'
        ));
    }

    public function getSufficientSuggestions($raw_material_id)
    {
        $raw_material = Product::findOrFail($raw_material_id);
        $current_stock = $this->productUtil->getCurrentStockDataByProduct($raw_material_id, request()->store_id)['current_stock'];

        $consumption_products = ConsumptionProduct::where('raw_material_id', $raw_material_id)->get();

        return view('consumption.sufficient_suggestions')->with(compact(
            'raw_material',
            'current_stock',
            'consumption_products'
        ));
    }
}
