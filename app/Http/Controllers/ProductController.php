<?php

namespace App\Http\Controllers;

use App\Imports\ProductImport;
use App\Models\AddStockLine;
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
use App\Models\Tax;
use App\Models\Transaction;
use App\Models\TransactionSellLine;
use App\Models\Unit;
use App\Models\User;
use App\Models\Variation;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
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
    public function getProductStocks(Request $request)
    {
        $products = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('add_stock_lines', function ($join) {
                $join->on('variations.id', 'add_stock_lines.variation_id')->where('add_stock_lines.expiry_date', '>=', date('Y-m-d'));
            })
            ->leftjoin('colors', 'variations.color_id', 'colors.id')
            ->leftjoin('sizes', 'variations.size_id', 'sizes.id')
            ->leftjoin('grades', 'variations.grade_id', 'grades.id')
            ->leftjoin('units', 'variations.unit_id', 'units.id')
            ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id');

        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        if (!empty($store_id)) {
            $products->where('product_stores.store_id', $store_id);
        }

        if (!empty(request()->product_id)) {
            $products->where('products.id', request()->product_id);
        }

        if (!empty(request()->product_class_id)) {
            $products->where('product_class_id', request()->product_class_id);
        }

        if (!empty(request()->category_id)) {
            $products->where('category_id', request()->category_id);
        }

        if (!empty(request()->sub_category_id)) {
            $products->where('sub_category_id', request()->sub_category_id);
        }

        if (!empty(request()->tax_id)) {
            $products->where('tax_id', request()->tax_id);
        }

        if (!empty(request()->brand_id)) {
            $products->where('brand_id', request()->brand_id);
        }

        if (!empty(request()->unit_id)) {
            $products->whereJsonContains('multiple_units', request()->unit_id);
        }

        if (!empty(request()->color_id)) {
            $products->whereJsonContains('multiple_colors', request()->color_id);
        }

        if (!empty(request()->size_id)) {
            $products->whereJsonContains('multiple_sizes', request()->size_id);
        }

        if (!empty(request()->grade_id)) {
            $products->whereJsonContains('multiple_grades', request()->grade_id);
        }

        if (!empty(request()->customer_type_id)) {
            $products->whereJsonContains('show_to_customer_types', request()->customer_type_id);
        }

        if (!empty(request()->customer_type_id)) {
            $products->whereJsonContains('show_to_customer_types', request()->customer_type_id);
        }

        if (!empty(request()->created_by)) {
            $products->where('created_by', request()->created_by);
        }

        $products->where('active', 1);
        $products = $products->select(
            'products.*',
            'variations.sub_sku',
            'colors.name as color_name',
            'sizes.name as size_name',
            'grades.name as grade_name',
            'units.name as unit_name',
            'variations.name as variation_name',
            'variations.default_purchase_price',
            'variations.default_sell_price',
            'add_stock_lines.expiry_date as exp_date',
            DB::raw('SUM(product_stores.qty_available) as current_stock'),
        )
            ->groupBy('products.id')
            ->get();
        $product_classes = ProductClass::orderBy('name', 'asc')->pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $colors = Color::orderBy('name', 'asc')->pluck('name', 'id');
        $sizes = Size::orderBy('name', 'asc')->pluck('name', 'id');
        $grades = Grade::orderBy('name', 'asc')->pluck('name', 'id');
        $taxes = Tax::orderBy('name', 'asc')->pluck('name', 'id');
        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');
        $customer_types = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $customers_tree_arry = Customer::getCustomerTreeArray();
        $stores  = Store::getDropdown();
        $users  = User::orderBy('name', 'asc')->pluck('name', 'id');
        $page = 'product_stock';

        return view('product.index')->with(compact(
            'products',
            'users',
            'product_classes',
            'categories',
            'sub_categories',
            'brands',
            'units',
            'colors',
            'sizes',
            'grades',
            'taxes',
            'customers',
            'customer_types',
            'customers_tree_arry',
            'stores',
            'page'
        ));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $products = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('add_stock_lines', function ($join) {
                $join->on('variations.id', 'add_stock_lines.variation_id')->where('add_stock_lines.expiry_date', '>=', date('Y-m-d'));
            })
            ->leftjoin('colors', 'variations.color_id', 'colors.id')
            ->leftjoin('sizes', 'variations.size_id', 'sizes.id')
            ->leftjoin('grades', 'variations.grade_id', 'grades.id')
            ->leftjoin('units', 'variations.unit_id', 'units.id')
            ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id');

        $store_id = $this->transactionUtil->getFilterOptionValues($request)['store_id'];

        if (!empty($store_id)) {
            $products->where('product_stores.store_id', $store_id);
        }

        if (!empty(request()->product_id)) {
            $products->where('products.id', request()->product_id);
        }

        if (!empty(request()->product_class_id)) {
            $products->where('product_class_id', request()->product_class_id);
        }

        if (!empty(request()->category_id)) {
            $products->where('category_id', request()->category_id);
        }

        if (!empty(request()->sub_category_id)) {
            $products->where('sub_category_id', request()->sub_category_id);
        }

        if (!empty(request()->tax_id)) {
            $products->where('tax_id', request()->tax_id);
        }

        if (!empty(request()->brand_id)) {
            $products->where('brand_id', request()->brand_id);
        }

        if (!empty(request()->unit_id)) {
            $products->whereJsonContains('multiple_units', request()->unit_id);
        }

        if (!empty(request()->color_id)) {
            $products->whereJsonContains('multiple_colors', request()->color_id);
        }

        if (!empty(request()->size_id)) {
            $products->whereJsonContains('multiple_sizes', request()->size_id);
        }

        if (!empty(request()->grade_id)) {
            $products->whereJsonContains('multiple_grades', request()->grade_id);
        }

        if (!empty(request()->customer_type_id)) {
            $products->whereJsonContains('show_to_customer_types', request()->customer_type_id);
        }

        if (!empty(request()->customer_type_id)) {
            $products->whereJsonContains('show_to_customer_types', request()->customer_type_id);
        }

        if (!empty(request()->created_by)) {
            $products->where('created_by', request()->created_by);
        }

        $products->where('active', 1);
        $products = $products->select(
            'products.*',
            'variations.sub_sku',
            'colors.name as color_name',
            'sizes.name as size_name',
            'grades.name as grade_name',
            'units.name as unit_name',
            'variations.name as variation_name',
            'variations.default_purchase_price',
            'variations.default_sell_price',
            'add_stock_lines.expiry_date as exp_date',
            DB::raw('SUM(product_stores.qty_available) as current_stock'),
        )->with(['product_class', 'category', 'sub_category', 'brand', 'created_by_user'])
            ->groupBy('variations.id')
            ->get();

        $product_classes = ProductClass::orderBy('name', 'asc')->pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $colors = Color::orderBy('name', 'asc')->pluck('name', 'id');
        $sizes = Size::orderBy('name', 'asc')->pluck('name', 'id');
        $grades = Grade::orderBy('name', 'asc')->pluck('name', 'id');
        $taxes = Tax::orderBy('name', 'asc')->pluck('name', 'id');
        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');
        $customer_types = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $customers_tree_arry = Customer::getCustomerTreeArray();

        $stores  = Store::getDropdown();
        $users = User::pluck('name', 'id');

        return view('product.index')->with(compact(
            'products',
            'product_classes',
            'categories',
            'sub_categories',
            'brands',
            'units',
            'colors',
            'sizes',
            'grades',
            'taxes',
            'customers',
            'customer_types',
            'customers_tree_arry',
            'users',
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
        if (!auth()->user()->can('product_module.product.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $product_classes = ProductClass::orderBy('name', 'asc')->pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $colors = Color::orderBy('name', 'asc')->pluck('name', 'id');
        $sizes = Size::orderBy('name', 'asc')->pluck('name', 'id');
        $grades = Grade::orderBy('name', 'asc')->pluck('name', 'id');
        $taxes = Tax::orderBy('name', 'asc')->pluck('name', 'id');
        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');
        $customer_types = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $customers_tree_arry = Customer::getCustomerTreeArray();
        $users = User::orderBy('name', 'asc')->pluck('name', 'id');
        $stores  = Store::all();
        $quick_add = request()->quick_add;

        if ($quick_add) {
            return view('product.create_quick_add')->with(compact(
                'quick_add',
                'product_classes',
                'categories',
                'sub_categories',
                'brands',
                'units',
                'colors',
                'sizes',
                'grades',
                'taxes',
                'customers',
                'customer_types',
                'customers_tree_arry',
                'stores',
            ));
        }

        return view('product.create')->with(compact(
            'product_classes',
            'categories',
            'sub_categories',
            'brands',
            'units',
            'colors',
            'sizes',
            'grades',
            'taxes',
            'customers',
            'customer_types',
            'customers_tree_arry',
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
        if (!auth()->user()->can('product_module.product.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }
        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
            ['purchase_price' => ['required', 'max:25', 'decimal']],
            ['sell_price' => ['required', 'max:25', 'decimal']],
        );

        // try {
        $product_data = [
            'name' => $request->name,
            'product_class_id' => $request->product_class_id,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'brand_id' => $request->brand_id,
            'sku' => !empty($request->sku) ? $request->sku : $this->productUtil->generateSku($request->name),
            'multiple_units' => $request->multiple_units,
            'multiple_colors' => $request->multiple_colors,
            'multiple_sizes' => $request->multiple_sizes,
            'multiple_grades' => $request->multiple_grades,
            'is_service' => !empty($request->is_service) ? 1 : 0,
            'product_details' => $request->product_details,
            'barcode_type' => $request->barcode_type ?? 'C128',
            'alert_quantity' => $request->alert_quantity,
            'purchase_price' => $request->purchase_price,
            'sell_price' => $request->sell_price,
            'tax_id' => $request->tax_id,
            'tax_method' => $request->tax_method,
            'discount_type' => $request->discount_type,
            'discount_customers' => $request->discount_customers,
            'discount' => $request->discount,
            'discount_start_date' => !empty($request->discount_start_date) ? $this->commonUtil->uf_date($request->discount_start_date) : null,
            'discount_end_date' => !empty($request->discount_end_date) ? $this->commonUtil->uf_date($request->discount_end_date) : null,
            'show_to_customer' => !empty($request->show_to_customer) ? 1 : 0,
            'show_to_customer_types' => $request->show_to_customer_types,
            'different_prices_for_stores' => !empty($request->different_prices_for_stores) ? 1 : 0,
            'this_product_have_variant' => !empty($request->this_product_have_variant) ? 1 : 0,
            'type' => !empty($request->this_product_have_variant) ? 'variable' : 'single',
            'active' => !empty($request->active) ? 1 : 0,
            'created_by' => Auth::user()->id
        ];


        DB::beginTransaction();

        $product = Product::create($product_data);

        $this->productUtil->createOrUpdateVariations($product, $request);


        if ($request->images) {
            foreach ($request->images as $image) {
                $product->addMedia($image)->toMediaCollection('product');
            }
        }


        DB::commit();
        $output = [
            'success' => true,
            'msg' => __('lang.success')
        ];
        // } catch (\Exception $e) {
        //     Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
        //     $output = [
        //         'success' => false,
        //         'msg' => __('lang.something_went_wrong')
        //     ];
        // }

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
        if (!auth()->user()->can('product_module.product.view')) {
            abort(403, 'Unauthorized action.');
        }

        $product = Product::find($id);

        $stock_detials = ProductStore::where('product_id', $id)->get();

        return view('product.show')->with(compact(
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
        if (!auth()->user()->can('product_module.product.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }
        $product = Product::find($id);

        $product_classes = ProductClass::orderBy('name', 'asc')->pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->orderBy('name', 'asc')->pluck('name', 'id');
        $brands = Brand::orderBy('name', 'asc')->pluck('name', 'id');
        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $colors = Color::orderBy('name', 'asc')->pluck('name', 'id');
        $sizes = Size::orderBy('name', 'asc')->pluck('name', 'id');
        $grades = Grade::orderBy('name', 'asc')->pluck('name', 'id');
        $taxes = Tax::orderBy('name', 'asc')->pluck('name', 'id');
        $customers = Customer::orderBy('name', 'asc')->pluck('name', 'id');
        $customer_types = CustomerType::orderBy('name', 'asc')->pluck('name', 'id');
        $customers_tree_arry = Customer::getCustomerTreeArray();
        $stores  = Store::all();


        return view('product.edit')->with(compact(
            'product',
            'product_classes',
            'categories',
            'sub_categories',
            'brands',
            'units',
            'colors',
            'sizes',
            'grades',
            'taxes',
            'customers',
            'customer_types',
            'customers_tree_arry',
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
        if (!auth()->user()->can('product_module.product.create_and_edit')) {
            abort(403, 'Unauthorized action.');
        }

        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
            ['purchase_price' => ['required', 'max:25', 'decimal']],
            ['sell_price' => ['required', 'max:25', 'decimal']],
        );

        try {
            $product_data = [
                'name' => $request->name,
                'product_class_id' => $request->product_class_id,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->sub_category_id,
                'brand_id' => $request->brand_id,
                'sku' => $request->sku,
                'multiple_units' => $request->multiple_units,
                'multiple_colors' => $request->multiple_colors,
                'multiple_sizes' => $request->multiple_sizes,
                'multiple_grades' => $request->multiple_grades,
                'is_service' => !empty($request->is_service) ? 1 : 0,
                'product_details' => $request->product_details,
                'barcode_type' => $request->barcode_type ?? 'C128',
                'alert_quantity' => $request->alert_quantity,
                'purchase_price' => $this->commonUtil->num_uf($request->purchase_price),
                'sell_price' => $this->commonUtil->num_uf($request->sell_price),
                'tax_id' => $request->tax_id,
                'tax_method' => $request->tax_method,
                'discount_type' => $request->discount_type,
                'discount_customers' => $request->discount_customers,
                'discount' => $request->discount,
                'discount_start_date' => !empty($request->discount_start_date) ? $this->commonUtil->uf_date($request->discount_start_date) : null,
                'discount_end_date' => !empty($request->discount_end_date) ? $this->commonUtil->uf_date($request->discount_end_date) : null,
                'show_to_customer' => !empty($request->show_to_customer) ? 1 : 0,
                'show_to_customer_types' => $request->show_to_customer_types,
                'different_prices_for_stores' => !empty($request->different_prices_for_stores) ? 1 : 0,
                'this_product_have_variant' => !empty($request->this_product_have_variant) ? 1 : 0,
                'type' => !empty($request->this_product_have_variant) ? 'variable' : 'single',
            ];


            DB::beginTransaction();
            $product = Product::find($id);

            $product->update($product_data);

            $this->productUtil->createOrUpdateVariations($product, $request);

            if ($request->images) {
                $product->clearMediaCollection('product');
                foreach ($request->images as $image) {
                    $product->addMedia($image)->toMediaCollection('product');
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

        if ($request->ajax()) {
            return $output;
        } else {
            return redirect()->back()->with('status', $output);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('product_module.product.delete')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $sell_lines = TransactionSellLine::leftjoin('transactions', 'transaction_sell_lines.transaction_id', 'transactions.id')
                ->join('products', 'transaction_sell_lines.product_id', 'products.id')->groupBy('transaction_sell_lines.product_id')
                ->where('transactions.type', 'sell')
                ->where('transaction_sell_lines.product_id', $id)
                ->first();

            $add_stock_lines = AddStockLine::leftjoin('transactions', 'add_stock_lines.transaction_id', 'transactions.id')
                ->join('products', 'add_stock_lines.product_id', 'products.id')->groupBy('add_stock_lines.product_id')
                ->where('transactions.type', 'add_stock')
                ->where('add_stock_lines.product_id', $id)
                ->first();

            if (!empty($sell_lines)) {
                $output = [
                    'success' => false,
                    'msg' => __('lang.product_sell_transaction_exist')
                ];
            } else if (!empty($add_stock_lines)) {
                $output = [
                    'success' => false,
                    'msg' => __('lang.product_add_stock_transaction_exist')
                ];
            } else {
                $product = Product::where('id', $id)->first();
                $product->clearMediaCollection('product');
                $product->delete();
                Variation::where('product_id', $id)->delete();
                ProductStore::where('product_id', $id)->delete();
                $output = [
                    'success' => true,
                    'msg' => __('lang.success')
                ];
            }
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return $output;
    }

    public function getVariationRow()
    {
        $row_id = request()->row_id;

        $units = Unit::orderBy('name', 'asc')->pluck('name', 'id');
        $colors = Color::orderBy('name', 'asc')->pluck('name', 'id');
        $sizes = Size::orderBy('name', 'asc')->pluck('name', 'id');
        $grades = Grade::orderBy('name', 'asc')->pluck('name', 'id');
        $stores = Store::all();
        $name = request()->name;
        $purchase_price = request()->purchase_price;
        $sell_price = request()->sell_price;

        return view('product.partial.variation_row')->with(compact(
            'units',
            'colors',
            'sizes',
            'grades',
            'stores',
            'row_id',
            'name',
            'purchase_price',
            'sell_price'
        ));
    }

    public function getProducts()
    {
        if (request()->ajax()) {

            $term = request()->term;

            if (empty($term)) {
                return json_encode([]);
            }

            $q = Product::leftJoin(
                'variations',
                'products.id',
                '=',
                'variations.product_id'
            )
                ->where(function ($query) use ($term) {
                    $query->where('products.name', 'like', '%' . $term . '%');
                    $query->orWhere('sku', 'like', '%' . $term . '%');
                    $query->orWhere('sub_sku', 'like', '%' . $term . '%');
                })
                ->whereNull('variations.deleted_at')
                ->select(
                    'products.id as product_id',
                    'products.name',
                    'products.type',
                    // 'products.sku as sku',
                    'variations.id as variation_id',
                    'variations.name as variation',
                    'variations.sub_sku as sub_sku'
                );

            if (!empty(request()->store_id)) {
                $q->ForLocation(request()->store_id);
            }
            $products = $q->get();

            $products_array = [];
            foreach ($products as $product) {
                $products_array[$product->product_id]['name'] = $product->name;
                $products_array[$product->product_id]['sku'] = $product->sub_sku;
                $products_array[$product->product_id]['type'] = $product->type;
                $products_array[$product->product_id]['variations'][]
                    = [
                        'variation_id' => $product->variation_id,
                        'variation_name' => $product->variation,
                        'sub_sku' => $product->sub_sku
                    ];
            }

            $result = [];
            $i = 1;
            $no_of_records = $products->count();
            if (!empty($products_array)) {
                foreach ($products_array as $key => $value) {
                    if ($no_of_records > 1 && $value['type'] != 'single') {
                        $result[] = [
                            'id' => $i,
                            'text' => $value['name'] . ' - ' . $value['sku'],
                            'variation_id' => 0,
                            'product_id' => $key
                        ];
                    }
                    $name = $value['name'];
                    foreach ($value['variations'] as $variation) {
                        $text = $name;
                        if ($value['type'] == 'variable') {
                            if ($variation['variation_name'] != 'Default') {
                                $text = $text . ' (' . $variation['variation_name'] . ')';
                            }
                        }
                        $i++;
                        $result[] = [
                            'id' => $i,
                            'text' => $text . ' - ' . $variation['sub_sku'],
                            'product_id' => $key,
                            'variation_id' => $variation['variation_id'],
                        ];
                    }
                    $i++;
                }
            }

            return json_encode($result);
        }
    }

    /**
     * get the list of porduct purchases
     *
     * @param [type] $id
     * @return void
     */
    public function getPurchaseHistory($id)
    {
        $product = Product::find($id);
        $add_stocks = Transaction::leftjoin('add_stock_lines', 'transactions.id', 'add_stock_lines.transaction_id')
            ->where('add_stock_lines.product_id', $id)
            ->groupBy('transactions.id')
            ->select('transactions.*')
            ->get();

        return view('product.partial.purchase_history')->with(compact(
            'product',
            'add_stocks',
        ));
    }

    /**
     * get import page
     *
     */
    public function getImport()
    {
        return view('product.import');
    }

    /**
     * save import resource to stores
     *
     */
    public function saveImport(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,txt'
        ]);
        try {

            Excel::import(new ProductImport($this->productUtil, $request), $request->file);

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
     * check sku if already in use
     *
     * @param string $sku
     * @return array
     */
    public function checkSku($sku)
    {
        $product_sku = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->where('sub_sku', $sku)->first();

        if (!empty($product_sku)) {
            $output = [
                'success' => false,
                'msg' => __('lang.sku_already_in_use')
            ];
        } else {
            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];
        }

        return $output;
    }

    public function deleteProductImage($id)
    {
        try {
            $product = Product::find($id);
            $product->clearMediaCollection('product');

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
}
