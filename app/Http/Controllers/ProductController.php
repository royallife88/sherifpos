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
use App\Models\Tax;
use App\Models\Unit;
use App\Models\Variation;
use App\Utils\ProductUtil;
use App\Utils\Util;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id');

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

        if (!empty(request()->batch_number)) {
            $products->where('batch_number', request()->batch_number);
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


        $products = $products->select(
            'products.*',
            DB::raw('SUM(product_stores.qty_available) as current_stock'),
        )
            ->groupBy('products.id')
            ->get();
        $product_classes = ProductClass::pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = Unit::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        $customers = Customer::pluck('name', 'id');
        $customer_types = CustomerType::pluck('name', 'id');
        $customers_tree_arry = Customer::getCustomerTreeArray();
        $stores  = Store::pluck('name', 'id');
        $batch_numbers = Product::distinct('batch_number')->pluck('batch_number', 'batch_number');

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
            'stores',
            'batch_numbers',
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product_classes = ProductClass::pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = Unit::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        $customers = Customer::pluck('name', 'id');
        $customer_types = CustomerType::pluck('name', 'id');
        $customers_tree_arry = Customer::getCustomerTreeArray();
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

        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
            ['product_class_id' => ['required', 'max:20']],
            ['category_id' => ['required', 'max:20']],
            ['sub_category_id' => ['required', 'max:20']],
            ['brand_id' => ['required', 'max:20']],
            ['barcode_type' => ['required', 'max:20']],
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
                'batch_number' => $request->batch_number,
                'barcode_type' => $request->barcode_type,
                'manufacturing_date' => !empty($request->manufacturing_date) ? $this->commonUtil->uf_date($request->manufacturing_date) : null,
                'expiry_date' => !empty($request->expiry_date) ? $this->commonUtil->uf_date($request->expiry_date) : null,
                'expiry_warning' => $request->expiry_warning,
                'convert_status_expire' => $request->convert_status_expire,
                'alert_quantity' => $request->alert_quantity,
                'purchase_price' => $request->purchase_price,
                'sell_price' => $request->sell_price,
                'tax_id' => $request->tax_id,
                'tax_method' => $request->tax_method,
                'discount_type' => $request->discount_type,
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
        $product = Product::find($id);

        $product_classes = ProductClass::pluck('name', 'id');
        $categories = Category::whereNull('parent_id')->pluck('name', 'id');
        $sub_categories = Category::whereNotNull('parent_id')->pluck('name', 'id');
        $brands = Brand::pluck('name', 'id');
        $units = Unit::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');
        $taxes = Tax::pluck('name', 'id');
        $customers = Customer::pluck('name', 'id');
        $customer_types = CustomerType::pluck('name', 'id');
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
        $this->validate(
            $request,
            ['name' => ['required', 'max:255']],
            ['product_class_id' => ['required', 'max:20']],
            ['category_id' => ['required', 'max:20']],
            ['sub_category_id' => ['required', 'max:20']],
            ['brand_id' => ['required', 'max:20']],
            ['barcode_type' => ['required', 'max:20']],
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
                'batch_number' => $request->batch_number,
                'barcode_type' => $request->barcode_type,
                'manufacturing_date' => !empty($request->manufacturing_date) ? $this->commonUtil->uf_date($request->manufacturing_date) : null,
                'expiry_date' => !empty($request->expiry_date) ? $this->commonUtil->uf_date($request->expiry_date) : null,
                'expiry_warning' => $request->expiry_warning,
                'convert_status_expire' => $request->convert_status_expire,
                'alert_quantity' => $request->alert_quantity,
                'purchase_price' => $this->commonUtil->num_uf($request->purchase_price),
                'sell_price' => $this->commonUtil->num_uf($request->sell_price),
                'tax_id' => $request->tax_id,
                'tax_method' => $request->tax_method,
                'discount_type' => $request->discount_type,
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
                foreach ($request->file('images', []) as $key => $image) {
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
            Product::where('id', $id)->delete();
            Variation::where('product_id', $id)->delete();
            ProductStore::where('product_id', $id)->delete();

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

    public function getVariationRow()
    {
        $row_id = request()->row_id;

        $units = Unit::pluck('name', 'id');
        $colors = Color::pluck('name', 'id');
        $sizes = Size::pluck('name', 'id');
        $grades = Grade::pluck('name', 'id');
        $stores = Store::all();

        return view('product.partial.variation_row')->with(compact(
            'units',
            'colors',
            'sizes',
            'grades',
            'stores',
            'row_id',
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
}
