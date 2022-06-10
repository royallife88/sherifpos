<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Grade;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\ProductStore;
use App\Models\Size;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\Unit;
use App\Models\User;
use App\Models\Variation;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

class RawMaterialController extends Controller
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
        if (!auth()->user()->can('raw_material_module.raw_material.view')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $products = Product::leftjoin('variations', function ($join) {
                $join->on('products.id', 'variations.product_id')->whereNull('variations.deleted_at');
            })->leftjoin('consumption_products', 'products.id', '=', 'consumption_products.raw_material_id')
                ->leftjoin('add_stock_lines', function ($join) {
                    $join->on('variations.id', 'add_stock_lines.variation_id')->where('add_stock_lines.expiry_date', '>=', date('Y-m-d'));
                })
                ->leftjoin('units', 'variations.unit_id', 'units.id')
                ->leftjoin('brands', 'products.brand_id', 'brands.id')
                ->leftjoin('users', 'products.created_by', 'users.id')
                ->leftjoin('users as edited', 'products.edited_by', 'users.id')
                ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id');

            $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

            $store_query = '';
            if (!empty($store_id)) {
                $products->where('product_stores.store_id', $store_id);
                $store_query = 'AND store_id=' . $store_id;
            }
            if (!empty(request()->variation_id)) {
                $products->where('consumption_products.variation_id', request()->variation_id);
            }
            if (!empty(request()->brand_id)) {
                $products->where('products.brand_id', request()->brand_id);
            }
            if (!empty(request()->created_by)) {
                $products->where('products.created_by', request()->created_by);
            }

            $products->where('is_raw_material', 1);
            $products = $products->select(
                'products.*',
                'add_stock_lines.batch_number',
                'variations.sub_sku',
                'brands.name as brand',
                'units.name as unit',
                'variations.id as variation_id',
                'variations.name as variation_name',
                'variations.default_purchase_price',
                'add_stock_lines.expiry_date as exp_date',
                'add_stock_lines.manufacturing_date',
                'users.name as created_by_name',
                'edited.name as edited_by_name',
                DB::raw('(SELECT SUM(product_stores.qty_available) FROM product_stores JOIN variations as v ON product_stores.variation_id=v.id WHERE v.id=variations.id ' . $store_query . ') as current_stock'),
            )->with(['supplier'])
                ->groupBy('variations.id');

            return DataTables::of($products)
                ->addColumn('image', function ($row) {
                    $image = $row->getFirstMediaUrl('product');
                    if (!empty($image)) {
                        return '<img src="' . $image . '" height="50px" width="50px">';
                    } else {
                        return '<img src="' . asset('/uploads/' . session('logo')) . '" height="50px" width="50px">';
                    }
                })
                ->editColumn('variation_name', '@if($variation_name != "Default"){{$variation_name}} @else {{$name}}
                @endif')
                ->editColumn('unit', function ($row) {
                    return $row->units->pluck('name')->implode(', ');
                })
                ->editColumn('sub_sku', '{{$sub_sku}}')
                ->addColumn('purchase_history', function ($row) {
                    $html = '<a data-href="' . action('ProductController@getPurchaseHistory', $row->id) . '"
                    data-container=".view_modal" class="btn btn-modal">' . __('lang.view') . '</a>';
                    return $html;
                })
                ->editColumn('supplier_name', function ($row) {
                    return $row->supplier->name ?? '';
                })
                ->editColumn('batch_number', '{{$batch_number}}')
                ->editColumn('brand', '{{$brand}}')
                ->editColumn('current_stock', '@if($is_service)-@else{{@num_format($current_stock)}}@endif')
                ->editColumn('exp_date', '@if(!empty($exp_date)){{@format_date($exp_date)}}@endif')
                ->addColumn('manufacturing_date', '@if(!empty($manufacturing_date)){{@format_date($manufacturing_date)}}@endif')
                ->editColumn('default_purchase_price', '{{@num_format($default_purchase_price)}}')
                ->editColumn('created_by', '{{$created_by_name}}')
                ->addColumn('products_view', function ($row) {
                    return '<a data-href="' . action('RawMaterialController@show', $row->id) . '"
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

                        if (auth()->user()->can('raw_material_module.raw_material.view')) {
                            $html .=
                                '<li><a data-href="' . action('RawMaterialController@show', $row->id) . '"
                                data-container=".view_modal" class="btn btn-modal"><i class="fa fa-eye"></i>
                                ' . __('lang.view') . '</a></li>';
                        }
                        if (auth()->user()->can('raw_material_module.consumption.view')) {
                            $html .=
                                '<li><a href="' . action('ConsumptionController@create', ['raw_material_id' => $row->id]) . '"
                                class="btn "><i class="fa fa-plus"></i>
                                ' . __('lang.add_manual_consumption') . '</a></li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('raw_material_module.raw_material.create_and_edit')) {
                            $html .=
                                '<li><a href="' . action('RawMaterialController@edit', $row->id) . '" class="btn"
                            target="_blank"><i class="dripicons-document-edit"></i> ' . __('lang.edit') . '</a></li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('raw_material_module.add_stock_for_raw_material.create_and_edit')) {
                            $html .=
                                '<li><a target="_blank" href="' . url('/raw-material/add-stock/create?variation_id=' . $row->variation_id . '&product_id=' . $row->id) . '" class="btn"
                            target="_blank"><i class="fa fa-plus"></i> ' . __('lang.add_new_stock') . '</a></li>';
                        }
                        $html .= '<li class="divider"></li>';
                        if (auth()->user()->can('raw_material_module.raw_material.delete')) {
                            $html .=
                                '<li>
                            <a data-href="' . action('ProductController@destroy', $row->variation_id) . '"
                                data-check_password="' . action('UserController@checkPassword', Auth::user()->id) . '"
                                class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                ' . __('lang.delete') . '</a>
                        </li>';
                        }

                        $html .= '</ul></div>';

                        return $html;
                    }
                )
                ->rawColumns([
                    'image',
                    'variation_name',
                    'sku',
                    'purchase_history',
                    'batch_number',
                    'sell_price',
                    'brand',
                    'unit',
                    'expiry',
                    'manufacturing_date',
                    'purchase_price',
                    'products_view',
                    'created_by',
                    'action',
                ])
                ->make(true);
        }

        $stores  = Store::orderBy('name', 'asc')->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $users  = User::orderBy('name', 'asc')->pluck('name', 'id');
        $products = Product::getProductVariationDropDown(true);

        return view('raw_material.index')->with(compact(
            'products',
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
        if (!auth()->user()->can('raw_material_module.raw_material.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $products = Product::getProductVariationDropDown(true);
        $units = Unit::getUnitDropdown(false, true);
        $units_all = Unit::getUnitDropdown(false, false);

        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $quick_add = request()->quick_add;
        $suppliers = Supplier::pluck('name', 'id');

        if ($quick_add) {
            return view('raw_material.create_quick_add')->with(compact(
                'quick_add',
                'products',
                'brands',
                'units',
                'suppliers',
            ));
        }

        return view('raw_material.create')->with(compact(
            'products',
            'brands',
            'units',
            'units_all',
            'suppliers',
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
        if (!auth()->user()->can('raw_material_module.raw_material.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }
        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
            ['purchase_price' => ['required', 'max:25', 'decimal']],
        );
        try {

            $raw_material_data = [
                'name' => $request->name,
                'brand_id' => $request->brand_id,
                'sku' => !empty($request->sku) ? $request->sku : $this->productUtil->generateSku($request->name),
                'multiple_units' => $request->multiple_units,
                'multiple_colors' => $request->multiple_colors ?? [],
                'multiple_sizes' => $request->multiple_sizes ?? [],
                'multiple_grades' => $request->multiple_grades ?? [],
                'is_service' => !empty($request->is_service) ? 1 : 0,
                'product_details' => $request->product_details,
                'barcode_type' => $request->barcode_type ?? 'C128',
                'alert_quantity' => $request->alert_quantity,
                'purchase_price' => $request->purchase_price,
                'sell_price' => $request->sell_price ?? 0,
                'alert_quantity_unit_id' => $request->alert_quantity_unit_id ?? null,
                'is_raw_material' => 1,
                'type' => 'single',
                'active' => !empty($request->active) ? 1 : 0,
                'created_by' => Auth::user()->id
            ];


            DB::beginTransaction();

            $raw_material = Product::create($raw_material_data);

            $this->productUtil->createOrUpdateVariations($raw_material, $request);
            $this->productUtil->createOrUpdateConsumptionProducts($raw_material, $request->consumption_details);


            if ($request->images) {
                foreach ($request->images as $image) {
                    $raw_material->addMedia($image)->toMediaCollection('product');
                }
            }
            if (!empty($request->supplier_id)) {
                SupplierProduct::updateOrCreate(
                    ['product_id' => $raw_material->id, 'supplier_id' => $request->supplier_id]
                );
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

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('raw_material_module.raw_material.view')) {
            abort(403, 'Unauthorized action.');
        }

        $product = Product::find($id);

        $stock_detials = ProductStore::where('product_id', $id)->get();

        return view('raw_material.show')->with(compact(
            'product',
            'stock_detials'
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
        if (!auth()->user()->can('raw_material_module.raw_material.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }
        $raw_material = Product::findOrFail($id);

        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $products = Product::getProductVariationDropDown(true);
        $units = Unit::getUnitDropdown(false, true);
        $units_all = Unit::getUnitDropdown(false, false);

        $users = User::orderBy('name', 'asc')->pluck('name', 'id');

        $colors = Color::orderBy('name', 'asc')->pluck('name', 'id');
        $sizes = Size::orderBy('name', 'asc')->pluck('name', 'id');
        $grades = Grade::orderBy('name', 'asc')->pluck('name', 'id');
        $stores  = Store::all();
        $suppliers = Supplier::pluck('name', 'id');


        return view('raw_material.edit')->with(compact(
            'raw_material',
            'products',
            'brands',
            'units',
            'units_all',
            'colors',
            'sizes',
            'grades',
            'stores',
            'suppliers',
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
        if (!auth()->user()->can('raw_material_module.raw_material.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }
        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
            ['purchase_price' => ['required', 'max:25', 'decimal']],
        );
        try {

            $raw_material_data = [
                'name' => $request->name,
                'brand_id' => $request->brand_id,
                'sku' => !empty($request->sku) ? $request->sku : $this->productUtil->generateSku($request->name),
                'multiple_units' => $request->multiple_units,
                'multiple_colors' => $request->multiple_colors ?? [],
                'multiple_sizes' => $request->multiple_sizes ?? [],
                'multiple_grades' => $request->multiple_grades ?? [],
                'is_service' => !empty($request->is_service) ? 1 : 0,
                'product_details' => $request->product_details,
                'barcode_type' => $request->barcode_type ?? 'C128',
                'alert_quantity' => $request->alert_quantity,
                'purchase_price' => $request->purchase_price,
                'sell_price' => $request->sell_price ?? 0,
                'alert_quantity_unit_id' => $request->alert_quantity_unit_id ?? null,
                'is_raw_material' => 1,
                'type' => 'single',
                'active' => !empty($request->active) ? 1 : 0,
                'edited_by' => Auth::user()->id
            ];


            DB::beginTransaction();

            $raw_material = Product::where('id', $id)->first();
            $raw_material->update($raw_material_data);
            $this->productUtil->createOrUpdateVariations($raw_material, $request);
            $this->productUtil->createOrUpdateConsumptionProducts($raw_material, $request->consumption_details);


            if ($request->images) {
                $raw_material->clearMediaCollection('product');
                foreach ($request->images as $image) {
                    $raw_material->addMedia($image)->toMediaCollection('product');
                }
            }

            if (!empty($request->supplier_id)) {
                SupplierProduct::updateOrCreate(
                    ['product_id' => $raw_material->id],
                    ['supplier_id' => $request->supplier_id]
                );
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

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function addProductRow()
    {
        $row_id = request()->row_id ?? 0;

        $products = Product::getProductVariationDropDown(true);
        $units = Unit::getUnitDropdown(true, false);
        $units_all = Unit::getUnitDropdown(false, false);

        return view('raw_material.partial.product_row')->with(compact(
            'products',
            'units',
            'units_all',
            'row_id'
        ));
    }
}
