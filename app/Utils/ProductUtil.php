<?php

namespace App\Utils;

use App\Models\AddStockLine;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ConsumptionProduct;
use App\Models\Customer;
use App\Models\EarningOfPoint;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\ProductStore;
use App\Models\PurchaseOrderLine;
use App\Models\PurchaseReturnLine;
use App\Models\RedemptionOfPoint;
use App\Models\RemoveStockLine;
use App\Models\SalesPromotion;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransferLine;
use App\Models\Variation;
use App\Utils\Util;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductUtil extends Util
{

    /**
     * Generates product sku
     *
     * @param string $string
     *
     * @return generated sku (string)
     */
    public function generateProductSku($string)
    {
        $sku_prefix = '';

        return $sku_prefix . str_pad($string, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generated SKU based on the barcode type.
     *
     * @param string $sku
     * @param string $c
     * @param string $barcode_type
     *
     * @return void
     */
    public function generateSubSku($sku, $c, $barcode_type = 'C128')
    {
        $sub_sku = $sku . $c;

        if (in_array($barcode_type, ['C128', 'C39'])) {
            $sub_sku = $sku . '-' . $c;
        }

        return $sub_sku;
    }

    /**
     * Generated unique ref numbers
     *
     * @param string $type
     *
     * @return void
     */
    public function getNumberByType($type, $store_id = null, $i = 1)
    {
        $number = '';
        $store_string = '';
        $day = Carbon::now()->day;
        $month = Carbon::now()->month;
        $year = Carbon::now()->year;
        if (!empty($store_id)) {
            $store_string = $this->getStoreNameFirstLetters($store_id);
        }
        if ($type == 'purchase_order') {
            $po_count = Transaction::where('type', $type)->count() + $i;

            $number = 'PO' . $store_string . $po_count;
        }
        if ($type == 'sell') {
            $inv_count = Transaction::where('type', $type)->count() + $i;

            $number = 'Inv' . $year . $month . $inv_count;
        }
        if ($type == 'expense') {
            $inv_count = Transaction::where('type', $type)->count() + $i;

            $number = 'Exp' . $year . $month . $inv_count;
        }
        if ($type == 'sell_return') {
            $count = Transaction::where('type', $type)->whereMonth('transaction_date', $month)->count() + $i;

            $number = 'Rets' . $year . $month . $count;
        }
        if ($type == 'purchase_return') {
            $count = Transaction::where('type', $type)->whereMonth('transaction_date', $month)->count() + $i;

            $number = 'RetP' . $year . $month . $count;
        }
        if ($type == 'remove_stock') {
            $count = Transaction::where('type', $type)->whereMonth('transaction_date', $month)->count() + $i;

            $number = 'RST' . $year . $month . $count;
        }
        if ($type == 'transfer') {
            $count = Transaction::where('type', $type)->whereMonth('transaction_date', $month)->count() + $i;

            $number = 'tras' . $year . $month . $count;
        }
        if ($type == 'quotation') {
            $count = Transaction::where('type', 'sell')->where('is_quotation', 1)->whereMonth('transaction_date', $month)->count() + $i;

            $number = 'Qu' . $year . $month . $count;
        }

        $number_exists = Transaction::where('type', $type)->where('invoice_no', $number)->exists();
        if ($number_exists) {
            return $this->getNumberByType($type, $store_id, $i + 1);
        }

        if ($type == 'internal_stock_request') {
            $count = Transaction::where('type', 'transfer')->where('is_internal_stock_transfer', 1)->distinct('invoice_no')->count() + $i;

            $number = 'ISRQ' . $year . $month . $day .  $count;
        }
        if ($type == 'internal_stock_return') {
            $count = Transaction::where('type', 'internal_stock_return')->where('is_internal_stock_transfer', 1)->where('is_return', 1)->distinct('invoice_no')->count() + $i;

            $number = 'ISRT' . $year . $month . $day .  $count;
        }

        if ($type == 'earning_of_point') {
            $count = EarningOfPoint::count() + $i;

            $number = 'LPE' . $year . $month . $day .  $count;
        }
        if ($type == 'redemption_of_point') {
            $count = RedemptionOfPoint::count() + $i;

            $number = 'LPR' . $year . $month . $day .  $count;
        }

        return $number;
    }

    public function generateSku($name, $number = 1)
    {
        $name_array = explode(" ", $name);
        $sku = '';
        foreach ($name_array as $w) {
            if (!empty($w)) {
                if (!preg_match('/[^A-Za-z0-9]/', $w)) {
                    $sku .= $w[0];
                }
            }
        }
        $sku = $sku . '-' . $number;
        $sku_exist = Product::where('sku', $sku)->exists();

        if ($sku_exist) {
            return $this->generateSku($name, $number + 1);
        } else {
            return $sku;
        }
    }
    public function getStoreNameFirstLetters($store_id)
    {
        $string = '';
        $store = Store::find($store_id);

        if (!empty($store)) {
            $name = explode(" ", $store->name);

            foreach ($name as $w) {
                if (!preg_match('/[^A-Za-z0-9]/', $w)) {
                    $string .= $w[0];
                }
            }
        }

        return $string;
    }

    //create or update product stores data
    public function createOrUpdateProductStore($product, $variation, $request, $variant_stores = [])
    {
        $stores = Store::all();

        $product_stores = $request->product_stores;
        //veriation is default
        if ($variation->name == 'Default') {
            foreach ($stores as $store) {
                ProductStore::updateOrcreate(
                    [
                        'product_id' => $product->id,
                        'variation_id' => $variation->id,
                        'store_id' => $store->id
                    ],
                    [
                        'product_id' => $product->id,
                        'variation_id' => $variation->id,
                        'store_id' => $store->id,
                        'qty' => 0,
                        'price' => !empty($product_stores[$store->id]['price']) ? $product_stores[$store->id]['price'] : $product->sell_price // if variation is default save the different price for store else save the default sell price
                    ]
                );
            }
        } else {
            foreach ($stores as $store) {
                ProductStore::updateOrcreate([
                    'product_id' => $product->id,
                    'variation_id' => $variation->id,
                    'store_id' => $store->id
                ], [
                    'product_id' => $product->id,
                    'variation_id' => $variation->id,
                    'store_id' => $store->id,
                    'qty' => 0,
                    'price' => !empty($variant_stores[$store->id]['price']) ? $variant_stores[$store->id]['price'] : $variation->default_sell_price //if other then default variation save the variation price
                ]);
            }
        }
    }

    /**
     * create or update product variation data
     *
     * @param object $product
     * @param object $request
     * @return boolean
     */
    public function createOrUpdateVariations($product, $request)
    {
        $variations = $request->variations;
        $keey_variations = [];
        if (!empty($variations)) {
            foreach ($variations as $v) {
                $c = Variation::where('product_id', $product->id)
                    ->count() + 1;
                if ($v['name'] == 'Default') {
                    $sub_sku = $product->sku;
                } else {
                    $sub_sku = !empty($v['sub_sku']) ? $v['sub_sku'] : $this->generateSubSku($product->sku, $c, $product->barcode_type);
                }

                if (!empty($v['id'])) {
                    $v['default_purchase_price'] = (float)$this->num_uf($v['default_purchase_price']);
                    $v['default_sell_price'] = (float)$this->num_uf($v['default_sell_price']);
                    $variation = Variation::find($v['id']);
                    $variation->name = $v['name'];
                    $variation->sub_sku = $sub_sku;
                    $variation->color_id = $v['color_id'] ?? null;
                    $variation->size_id = $v['size_id'] ?? null;
                    $variation->grade_id = $v['grade_id'] ?? null;
                    $variation->unit_id = $v['unit_id'] ?? null;
                    $variation->default_purchase_price = !empty($v['default_purchase_price']) ? $this->num_uf($v['default_purchase_price']) : $this->num_uf($product->purchase_price);
                    $variation->default_sell_price = !empty($v['default_sell_price']) ? $this->num_uf($v['default_sell_price']) : $this->num_uf($product->sell_price);

                    $variation->save();
                    $variation_array[] = ['variation' => $variation, 'variant_stores' => $v['variant_stores']];
                    $keey_variations[] = $v['id'];
                } else {
                    $variation_data['name'] = $v['name'];
                    $variation_data['product_id'] = $product->id;
                    $variation_data['sub_sku'] = !empty($v['sub_sku']) ? $v['sub_sku'] : $this->generateSubSku($product->sku, $c, $product->barcode_type);
                    $variation_data['color_id'] = $v['color_id'] ?? null;
                    $variation_data['size_id'] = $v['size_id'] ?? null;
                    $variation_data['grade_id'] = $v['grade_id'] ?? null;
                    $variation_data['unit_id'] = $v['unit_id'] ?? null;
                    $variation_data['default_purchase_price'] = !empty($v['default_purchase_price']) ? $this->num_uf($v['default_purchase_price']) : $this->num_uf($product->purchase_price);
                    $variation_data['default_sell_price'] = !empty($v['default_sell_price']) ? $this->num_uf($v['default_sell_price']) : $this->num_uf($product->sell_price);
                    $variation_data['is_dummy'] = 0;

                    $variation = Variation::create($variation_data);
                    $variation_array[] = ['variation' => $variation, 'variant_stores' => $v['variant_stores']];
                    $keey_variations[] = $variation->id;
                }
            }
        } else {
            $variation_data['name'] = 'Default';
            $variation_data['product_id'] = $product->id;
            $variation_data['sub_sku'] = $product->sku;
            $variation_data['color_id'] = !empty($request->multiple_colors) ? $request->multiple_colors[0] : null;
            $variation_data['size_id'] = !empty($request->multiple_sizes) ? $request->multiple_sizes[0] : null;
            $variation_data['grade_id'] = !empty($request->multiple_grades) ? $request->multiple_grades[0] : null;
            $variation_data['unit_id'] = !empty($request->multiple_units) ? $request->multiple_units[0] : null;
            $variation_data['is_dummy'] = 1;
            $variation_data['default_purchase_price'] = $this->num_uf($product->purchase_price);
            $variation_data['default_sell_price'] = $this->num_uf($product->sell_price);

            $variation = Variation::create($variation_data);
            $variation_array[] = ['variation' => $variation, 'variant_stores' =>  []];
            $keey_variations[] = $variation->id;
        }

        if (!empty($keey_variations)) {
            //delete the variation removed by user
            Variation::where('product_id', $product->id)->whereNotIn('id', $keey_variations)->delete();
            ProductStore::where('product_id', $product->id)->whereNotIn('variation_id', $keey_variations)->delete();
        }
        foreach ($variation_array as $array) {
            $this->createOrUpdateProductStore($product, $array['variation'], $request, $array['variant_stores']);
        }

        return true;
    }

    /**
     * create or update product consumption data
     *
     * @param int $variation_id
     * @param array $consumption_details
     * @return boolean
     */
    public function createOrUpdateRawMaterialToProduct($variation_id, $consumption_details)
    {
        $keep_consumption_product = [];
        if (!empty($consumption_details)) {
            foreach ($consumption_details as $v) {
                if (!empty($v['raw_material_id'])) {
                    if (!empty($v['id'])) {
                        $consumtion_product = ConsumptionProduct::find($v['id']);
                        $consumtion_product->raw_material_id = $v['raw_material_id'];
                        $consumtion_product->variation_id = $variation_id;
                        $consumtion_product->amount_used = $this->num_uf($v['amount_used']);
                        $consumtion_product->unit_id = $v['unit_id'];

                        $consumtion_product->save();
                        $keep_consumption_product[] = $v['id'];
                    } else {
                        $consumtion_product_data['raw_material_id'] = $v['raw_material_id'];
                        $consumtion_product_data['variation_id'] = $variation_id;
                        $consumtion_product_data['amount_used'] = $v['amount_used'];
                        $consumtion_product_data['unit_id'] = $v['unit_id'];
                        $consumtion_product = ConsumptionProduct::create($consumtion_product_data);
                        $keep_consumption_product[] = $consumtion_product->id;
                    }
                }
            }
        }

        if (!empty($keep_consumption_product)) {
            //delete the consumption product removed by user
            ConsumptionProduct::where('variation_id', $variation_id)->whereNotIn('id', $keep_consumption_product)->delete();
        }

        return true;
    }
    /**
     * create or update product consumption data
     *
     * @param object $raw_material
     * @param array $consumption_details
     * @return boolean
     */
    public function createOrUpdateConsumptionProducts($raw_material, $consumption_details = [])
    {
        $keep_consumption_product = [];
        if (!empty($consumption_details)) {
            foreach ($consumption_details as $v) {
                if (!empty($v['id'])) {
                    $consumtion_product = ConsumptionProduct::find($v['id']);
                    $consumtion_product->raw_material_id = $raw_material->id;
                    $consumtion_product->variation_id = $v['variation_id'];
                    $consumtion_product->amount_used = $this->num_uf($v['amount_used']);
                    $consumtion_product->unit_id = $v['unit_id'];

                    $consumtion_product->save();
                    $keep_consumption_product[] = $v['id'];
                } else {
                    $consumtion_product_data['raw_material_id'] = $raw_material->id;
                    $consumtion_product_data['variation_id'] = $v['variation_id'];
                    $consumtion_product_data['amount_used'] = $v['amount_used'];
                    $consumtion_product_data['unit_id'] = $v['unit_id'];
                    $consumtion_product = ConsumptionProduct::create($consumtion_product_data);
                    $keep_consumption_product[] = $consumtion_product->id;
                }
            }
        } else {
            ConsumptionProduct::where('raw_material_id', $raw_material->id)->delete();
        }

        if (!empty($keep_consumption_product)) {
            //delete the consumption product removed by user
            ConsumptionProduct::where('raw_material_id', $raw_material->id)->whereNotIn('id', $keep_consumption_product)->delete();
        }

        return true;
    }

    public function getProductClassificationTreeObject()
    {
        $classes = ProductClass::select('name', 'id')->get();

        $tree = '';
        foreach ($classes as $class) {
            $tree .= '{text: "' . $class->name . '",';
            $tree .= $this->treeConfigString('red');
            $tree .= 'nodes: [';

            $categories = Product::where(
                'product_class_id',
                $class->id
            )->select('category_id')->groupBy('category_id')->get();

            foreach ($categories as $category) {
                $tree .= '{';
                $category = Category::find($category->category_id);
                $tree .= 'text: "' . $category->name . '",';
                $tree .= $this->treeConfigString('lightblue');
                $tree .= 'nodes: [';

                $sub_categories = Product::where(
                    'category_id',
                    $category->id
                )->select('sub_category_id')->groupBy('sub_category_id')->get();
                foreach ($sub_categories as $sub_category) {
                    $tree .= '{';
                    $sub_category = Category::find($sub_category->sub_category_id);
                    $tree .= 'text: "' . $sub_category->name . '",';
                    $tree .= $this->treeConfigString('maroon');
                    $tree .= 'nodes: [';
                    $brands = Product::where(
                        'sub_category_id',
                        $sub_category->id
                    )->select('brand_id')->groupBy('brand_id')->get();
                    foreach ($brands as $brand) {
                        $tree .= '{';
                        $brand = Brand::find($brand->brand_id);
                        $tree .= 'text: "' . $brand->name . '",';
                        $tree .= $this->treeConfigString('gray');
                        $tree .= 'nodes: [';
                        $products = Product::where(
                            'brand_id',
                            $brand->id
                        )->select('id')->get();
                        foreach ($products as $product) {
                            $tree .= '{';
                            $product = Product::find($product->id);
                            $action = action('ProductController@edit', $product->id);
                            $tree .= 'text: "' . $product->name . '",';
                            // $tree .= 'href:  "'.$action.'",';
                            $tree .= 'color: "green",';
                            $tree .= 'nodes: [';

                            $tree .= ']},';
                        }

                        $tree .= ']},';
                    }
                    $tree .= ']},';
                }
                $tree .= ']},';
            }
            $tree .= ']},';
        }

        return $tree;
    }

    public function treeConfigString($color)
    {
        $config = 'icon: "fa fa-angle-right",
            selectedIcon: "fa fa-angle-down",
            expandIcon: "fa fa-angle-down",
            color: "' . $color . '",
            showBorder: false,';

        return $config;
    }

    /**
     * Gives list of products based on products id and variation id
     *
     * @param int $product_id
     * @param int $variation_id = null
     * @param int $store_id = null
     *
     * @return Obj
     */
    public function getDetailsFromProduct($product_id, $variation_id = null, $store_id = null)
    {
        $product = Product::leftjoin('variations as v', 'products.id', '=', 'v.product_id')
            ->leftjoin('product_stores', 'v.id', '=', 'product_stores.variation_id')
            ->whereNull('v.deleted_at');

        if (!is_null($variation_id) && $variation_id !== '0') {
            $product->where('v.id', $variation_id);
        }
        if (!is_null($store_id) && $store_id !== '0') {
            $product->where('product_stores.store_id', $store_id);
        }
        $product->where('products.id', $product_id)->groupBy('v.id');

        $products = $product->select(
            'products.*',
            'products.id as product_id',
            'products.name as product_name',
            'product_stores.qty_available',
            'v.id as variation_id',
            'v.name as variation_name',
            'v.default_purchase_price',
            'v.default_sell_price',
            'v.sub_sku'
        )
            ->get();

        return $products;
    }

    /**
     * Gives list of products based on products id and variation id
     *
     * @param int $sender_store_id
     * @param int $product_id
     * @param int $variation_id = null
     *
     * @return object
     */
    public function getDetailsFromProductTransfer($sender_store_id, $product_id, $variation_id = null)
    {
        $product = Product::leftjoin('variations as v', 'products.id', '=', 'v.product_id')
            ->leftjoin('product_stores', 'v.id', '=', 'product_stores.variation_id')
            ->whereNull('v.deleted_at');

        if (!is_null($sender_store_id) && $sender_store_id !== '0') {
            $product->where('product_stores.store_id', $sender_store_id);
        }

        if (!is_null($variation_id) && $variation_id !== '0') {
            $product->where('v.id', $variation_id);
        }

        $product->where('products.id', $product_id);

        $products = $product->select(
            'products.id as product_id',
            'products.name as product_name',
            'product_stores.qty_available',
            'v.id as variation_id',
            'v.name as variation_name',
            'v.default_purchase_price',
            'v.default_sell_price',
            'v.sub_sku'
        )
            ->get();

        return $products;
    }

    /**
     * Gives list of products based on products id and variation id
     *
     * @param int $product_id
     * @param int $variation_id = null
     * @param int $store_id = null
     *
     * @return Obj
     */
    public function getDetailsFromProductByStore($product_id, $variation_id = null, $store_id = null)
    {
        $product = Product::leftjoin('variations as v', 'products.id', '=', 'v.product_id')
            ->leftjoin('taxes', 'products.tax_id', '=', 'taxes.id')
            ->leftjoin('product_stores', 'v.id', '=', 'product_stores.variation_id')

            ->whereNull('v.deleted_at');

        if (!is_null($variation_id) && $variation_id !== '0') {
            $product->where('v.id', $variation_id);
        }
        if (!is_null($store_id) && $store_id !== '0') {
            $product->where('product_stores.store_id', $store_id);
        }

        $product->where('products.id', $product_id);

        $products = $product->select(
            'products.id as product_id',
            'products.name as product_name',
            'products.is_service',
            'products.alert_quantity',
            'products.tax_id',
            'products.tax_method',
            'product_stores.qty_available',
            'products.sell_price',
            'taxes.rate as tax_rate',
            'v.id as variation_id',
            'v.name as variation_name',
            'v.default_purchase_price',
            'v.default_sell_price',
            'v.sub_sku'
        )->groupBy('v.id')
            ->get();

        return $products;
    }

    /**
     * get the product discount details for product if exist
     *
     * @param int $product_id
     * @param int $customer_id
     * @return mix
     */
    public function getProductDiscountDetails($product_id, $customer_id)
    {
        $customer = Customer::find($customer_id);
        $customer_type_id = null;
        if (!empty($customer)) {
            $customer_type_id = (string) $customer->customer_type_id;
        }
        if (!empty($customer_type_id)) {
            $product = Product::whereJsonContains('discount_customer_types', $customer_type_id)
                ->where('id', $product_id)
                ->select(
                    'products.discount_type',
                    'products.discount',
                    'products.discount_start_date',
                    'products.discount_end_date',
                )
                ->first();

            if (!empty($product)) {
                if (!empty($product->discount_start_date) && !empty($product->discount_end_date)) {
                    //if end date set then check for expiry
                    if ($product->discount_start_date <= date('Y-m-d') && $product->discount_end_date >= date('Y-m-d')) {
                        return $product;
                    } else {
                        return false;
                    }
                }
                return $product;
            }
        }
        return null;
    }
    /**
     * get the sales promotion details for product if exist
     *
     * @param int $product_id
     * @param int $store_id
     * @param int $customer_id
     * @return object
     */
    public function getSalesPromotionDetail($product_id, $store_id, $customer_id, $added_products = [])
    {
        $customer = Customer::find($customer_id);
        $store_id = (string) $store_id;
        $product_id = (int) $product_id;
        $added_products = (array)$added_products;

        $customer_type_id = (string) $customer->customer_type_id;
        if (!empty($customer_type_id)) {
            $sales_promotions = SalesPromotion::whereJsonContains('customer_type_ids', $customer_type_id)
                ->whereJsonContains('store_ids', $store_id)
                ->whereJsonContains('product_ids', $product_id)
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>=', date('Y-m-d'))
                ->get();
            foreach ($sales_promotions as $sales_promotion) {
                if ($sales_promotion->type == 'item_discount') {
                    if (!$sales_promotion->product_condition) {
                        return $sales_promotion;
                    } else {
                        $com = $this->compareArray($sales_promotion->condition_product_ids, $added_products);
                        if ($com) {
                            return $sales_promotion;
                        }
                    }
                }
            }
        }
    }
    /**
     * get the sales promotion details for product if valid for this sale
     *
     * @param int $product_id
     * @param int $store_id
     * @param int $customer_id
     * @return object
     */
    public function getSalePromotionDetailsIfValidForThisSale($store_id, $customer_id, $added_products = [], $qty_array = [])
    {
        $customer = Customer::find($customer_id);
        $store_id = (string) $store_id;
        $added_products = (array)$added_products;

        $customer_type_id = (string) $customer->customer_type_id;
        if (!empty($customer_type_id)) {
            $sales_promotions = SalesPromotion::whereJsonContains('customer_type_ids', $customer_type_id)
                ->whereJsonContains('store_ids', $store_id)
                ->whereDate('start_date', '<=', date('Y-m-d'))
                ->whereDate('end_date', '>=', date('Y-m-d'))
                ->get();
            foreach ($sales_promotions as $sales_promotion) {
                if ($sales_promotion->type == 'item_discount') {
                    if (!$sales_promotion->product_condition) {
                        return $sales_promotion;
                    } else {
                        $is_valid = $this->compareArray($sales_promotion->condition_product_ids, $added_products);
                        if ($is_valid) {
                            return $sales_promotion;
                        }
                    }
                }
                if ($sales_promotion->type == 'package_promotion') {
                    $package_promotion_qty = $sales_promotion->package_promotion_qty;

                    $is_valid = $this->comparePackagePromotionData($package_promotion_qty, $qty_array);
                    if ($is_valid) {
                        return $sales_promotion;
                    }
                }
            }
        }
        return null;
    }

    /**
     * compare package promotion data with add product data
     *
     * @param array $package_promotion_qty
     * @param array $qty_array
     * @return boolean
     */
    public function comparePackagePromotionData($package_promotion_qty, $qty_array)
    {
        foreach ($package_promotion_qty as $product_id => $qty) {
            if (!isset($qty_array[$product_id])) {
                return false;
            }
            if ($qty_array[$product_id] < $qty) {
                return false;
            }
        }
        return true;
    }

    //function to compare two array have equal value or not
    public function compareArray($array1, $array2)
    {
        foreach ($array1 as $value) {
            if (!in_array($value, $array2)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get all details for a product from its variation id
     *
     * @param int $variation_id
     * @param int $store_id
     * @param bool $check_qty (If false qty_available is not checked)
     *
     * @return object
     */
    public function getDetailsFromVariation($variation_id,  $store_id = null, $check_qty = true)
    {
        $query = Variation::join('products AS p', 'variations.product_id', '=', 'p.id')
            ->leftjoin('product_stores AS ps', 'variations.id', '=', 'ps.variation_id')
            ->leftjoin('units', 'variations.unit_id', '=', 'units.id')
            ->leftjoin('grades', 'variations.grade_id', '=', 'grades.id')
            ->leftjoin('sizes', 'variations.size_id', '=', 'sizes.id')
            ->leftjoin('colors', 'variations.color_id', '=', 'colors.id')

            ->where('variations.id', $variation_id);


        if (!empty($store_id) && $check_qty) {
            //Check for enable stock, if enabled check for store id.
            $query->where(function ($query) use ($store_id) {
                $query->where('ps.store_id', $store_id);
            });
        }

        $product = $query->select(
            DB::raw("IF(variations.is_dummy = 0, CONCAT(p.name,
                    ' (', variations.name, ':',variations.name, ')'), p.name) AS product_name"),
            'p.id as product_id',
            'p.sell_price',
            'p.type as product_type',
            'p.name as product_actual_name',
            'variations.name as product_variation_name',
            'variations.is_dummy as is_dummy',
            'variations.name as variation_name',
            'variations.sub_sku',
            'p.barcode_type',
            'ps.qty_available',
            'units.name as unit_name',
            'grades.name as grade_name',
            'sizes.name as size_name',
            'colors.name as color_name',
            'variations.default_sell_price',
            'variations.id as variation_id'
        )
            ->first();

        return $product;
    }

    /**
     * createOrUpdatePurchaseOrderLines
     *
     * @param [mix] $purchase_order_lines
     * @param [mix] $transaction
     * @return void
     */
    public function createOrUpdatePurchaseOrderLines($purchase_order_lines, $transaction)
    {

        $keep_lines_ids = [];

        foreach ($purchase_order_lines as $line) {
            if (!empty($line['purchase_order_line_id'])) {
                $purchase_order_line = PurchaseOrderLine::find($line['purchase_order_line_id']);

                $purchase_order_line->product_id = $line['product_id'];
                $purchase_order_line->variation_id = $line['variation_id'];
                $purchase_order_line->quantity = $this->num_uf($line['quantity']);
                $purchase_order_line->purchase_price = $this->num_uf($line['purchase_price']);
                $purchase_order_line->sub_total = $this->num_uf($line['sub_total']);

                $purchase_order_line->save();
                $keep_lines_ids[] = $line['purchase_order_line_id'];
            } else {
                $purchase_order_line_data = [
                    'transaction_id' => $transaction->id,
                    'product_id' => $line['product_id'],
                    'variation_id' => $line['variation_id'],
                    'quantity' => $this->num_uf($line['quantity']),
                    'purchase_price' => $this->num_uf($line['purchase_price']),
                    'sub_total' => $this->num_uf($line['sub_total']),
                ];

                $purchase_order_line = PurchaseOrderLine::create($purchase_order_line_data);

                $keep_lines_ids[] = $purchase_order_line->id;
            }
        }

        if (!empty($keep_lines_ids)) {
            PurchaseOrderLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_lines_ids)->delete();
        }

        return true;
    }

    /**
     * create or update remove stock lines
     *
     * @param [mix] $remove_stock_lines
     * @param [mix] $transaction
     * @return void
     */
    public function createOrUpdateRemoveStockLines($remove_stock_lines, $transaction)
    {

        $keep_lines_ids = [];

        foreach ($remove_stock_lines as $line) {
            if (!empty($line['remove_stock_line_id'])) {
                $remove_stock_line = RemoveStockLine::find($line['remove_stock_line_id']);

                $remove_stock_line->product_id = $line['product_id'];
                $remove_stock_line->variation_id = $line['variation_id'];
                $remove_stock_line->quantity = $this->num_uf($line['quantity']);
                $remove_stock_line->purchase_price = $this->num_uf($line['purchase_price']);
                $remove_stock_line->sub_total = $this->num_uf($line['sub_total']);
                $remove_stock_line->notes = !empty($line['notes']) ? $line['notes'] : null;

                $remove_stock_line->save();
                if ($line['quantity'] > 0) {
                    $this->decreaseProductQuantity($line['product_id'], $line['variation_id'], $transaction->store_id, $line['quantity'], $remove_stock_line->quantity);
                }

                $keep_lines_ids[] = $line['remove_stock_line_id'];
            } else {
                $remove_stock_line_data = [
                    'transaction_id' => $transaction->id,
                    'product_id' => $line['product_id'],
                    'variation_id' => $line['variation_id'],
                    'quantity' => $this->num_uf($line['quantity']),
                    'purchase_price' => $this->num_uf($line['purchase_price']),
                    'sub_total' => $this->num_uf($line['sub_total']),
                    'notes' => !empty($line['notes']) ? $line['notes'] : null,
                ];

                if ($line['quantity'] > 0) {
                    $this->decreaseProductQuantity($line['product_id'], $line['variation_id'], $transaction->store_id, $line['quantity'], 0);
                }

                $remove_stock_line = RemoveStockLine::create($remove_stock_line_data);

                $keep_lines_ids[] = $remove_stock_line->id;
            }
        }

        if (!empty($keep_lines_ids)) {
            $deleted_lines = RemoveStockLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_lines_ids)->get();
            foreach ($deleted_lines as $deleted_line) {
                if ($deleted_line->quantity > 0) {
                    $this->updateProductQuantityStore($deleted_line->product_id, $deleted_line->variation_id, $transaction->store_id, $deleted_line->quantity, 0);
                }
                $deleted_line->delete();
            }
        }

        return true;
    }
    /**
     * createOrUpdateAddStockLines
     *
     * @param [mix] $add_stocks
     * @param [mix] $transaction
     * @return void
     */
    public function createOrUpdateAddStockLines($add_stocks, $transaction)
    {

        $keep_lines_ids = [];

        foreach ($add_stocks as $line) {
            if (!empty($line['add_stock_line_id'])) {
                $add_stock = AddStockLine::find($line['add_stock_line_id']);

                $add_stock->product_id = $line['product_id'];
                $add_stock->variation_id = $line['variation_id'];
                $old_qty = $add_stock->quantity;
                $add_stock->quantity = $this->num_uf($line['quantity']);
                $add_stock->purchase_price = $this->num_uf($line['purchase_price']);
                $add_stock->final_cost = $this->num_uf($line['final_cost']);
                $add_stock->sub_total = $this->num_uf($line['sub_total']);
                $add_stock->batch_number = $line['batch_number'];
                $add_stock->manufacturing_date = !empty($line['manufacturing_date']) ? $this->uf_date($line['manufacturing_date']) : null;
                $add_stock->expiry_date = !empty($line['expiry_date']) ? $this->uf_date($line['expiry_date']) : null;
                $add_stock->expiry_warning = $line['expiry_warning'];
                $add_stock->convert_status_expire = $line['convert_status_expire'];
                $add_stock->save();
                $keep_lines_ids[] = $line['add_stock_line_id'];
                $qty =  $this->num_uf($line['quantity']);
                $this->updateProductQuantityStore($line['product_id'], $line['variation_id'], $transaction->store_id,  $qty, $old_qty);
            } else {
                $add_stock_data = [
                    'transaction_id' => $transaction->id,
                    'product_id' => $line['product_id'],
                    'variation_id' => $line['variation_id'],
                    'quantity' => $this->num_uf($line['quantity']),
                    'purchase_price' => $this->num_uf($line['purchase_price']),
                    'final_cost' => $this->num_uf($line['final_cost']),
                    'sub_total' => $this->num_uf($line['sub_total']),
                    'batch_number' => $line['batch_number'],
                    'manufacturing_date' => !empty($line['manufacturing_date']) ? $this->uf_date($line['manufacturing_date']) : null,
                    'expiry_date' => !empty($line['expiry_date']) ? $this->uf_date($line['expiry_date']) : null,
                    'expiry_warning' => $line['expiry_warning'],
                    'convert_status_expire' => $line['convert_status_expire'],
                ];

                $add_stock = AddStockLine::create($add_stock_data);
                $qty =  $this->num_uf($line['quantity']);
                $keep_lines_ids[] = $add_stock->id;
                $this->updateProductQuantityStore($line['product_id'], $line['variation_id'], $transaction->store_id,  $qty, 0);
            }
        }

        if (!empty($keep_lines_ids)) {
            $deleted_lines = AddStockLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_lines_ids)->get();
            foreach ($deleted_lines as $deleted_line) {
                if ($deleted_line->quantity_sold != 0) {
                    $product_name = Product::find($deleted_line->product_id)->name ?? '';
                    return ['mismatch' => true, 'product_name' => $product_name, 'quantity' => 0];
                }
                $this->decreaseProductQuantity($deleted_line['product_id'], $deleted_line['variation_id'], $transaction->store_id, $deleted_line['quantity'], 0);
                $deleted_line->delete();
            }
        }
        return true;
    }

    /**
     * check if there is any quantity mismatch in sold and purchase quantity
     *
     * @param array $add_stock_lines
     * @return mixed
     */
    public function checkSoldAndPurchaseQtyMismatch($add_stock_lines, $transaction)
    {
        $keep_lines_ids = [];

        foreach ($add_stock_lines as $line) {
            if (!empty($line['add_stock_line_id'])) {
                $add_stock_line = AddStockLine::find($line['add_stock_line_id']);
                $keep_lines_ids[] = $add_stock_line->id;
                if (!empty($add_stock_line)) {
                    if ($line['quantity'] < $add_stock_line->quantity_sold) {
                        $product_name = Product::find($add_stock_line->product_id)->name ?? '';
                        return ['mismatch' => true, 'product_name' => $product_name, 'quantity' => $line['quantity']];
                    }
                }
            }
        }

        $deleted_lines = AddStockLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_lines_ids)->get();
        foreach ($deleted_lines as $deleted_line) {
            if ($deleted_line->quantity_sold != 0) {
                $product_name = Product::find($deleted_line->product_id)->name ?? '';
                return ['mismatch' => true, 'product_name' => $product_name, 'quantity' => 0];
            }
        }


        return false;
    }

    public function sendQunatityMismacthResponse($product_name, $qty)
    {
        return redirect()->back()->with('status', ['success' => false, 'msg' => __('lang.sold_qty_mismatch_purchase_qty') . '\n Porduct: ' . $product_name . '\n Quantity: ' . $qty]);
    }

    /**
     * createOrUpdateInternalStockRequestLines
     *
     * @param [mix] $transfer_lines
     * @param [mix] $transaction
     * @return float
     */
    public function createOrUpdateInternalStockRequestLines($transfer_lines, $transaction)
    {
        $keep_lines_ids = [];
        $final_total = 0;
        foreach ($transfer_lines as $line) {
            if (!empty($line['transfer_line_id'])) {
                $transfer_line = TransferLine::find($line['transfer_line_id']);

                $transfer_line->product_id = $line['product_id'];
                $transfer_line->variation_id = $line['variation_id'];
                $transfer_line->quantity = $this->num_uf($line['quantity']);
                $transfer_line->purchase_price = $this->num_uf($line['purchase_price']);
                $transfer_line->sub_total = $this->num_uf($line['sub_total']);
                $transfer_line->save();
                $final_total += $line['sub_total'];
                $keep_lines_ids[] = $line['transfer_line_id'];
            } else {
                $transfer_line_data = [
                    'transaction_id' => $transaction->id,
                    'product_id' => $line['product_id'],
                    'variation_id' => $line['variation_id'],
                    'quantity' => $this->num_uf($line['quantity']),
                    'purchase_price' => $this->num_uf($line['purchase_price']),
                    'sub_total' => $this->num_uf($line['sub_total']),
                ];
                $final_total += $line['sub_total'];
                $transfer_line = TransferLine::create($transfer_line_data);
                $keep_lines_ids[] = $transfer_line->id;
            }
        }

        if (!empty($keep_lines_ids)) {
            $deleted_lines = TransferLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_lines_ids)->get();
            foreach ($deleted_lines as $deleted_line) {
                $deleted_line->delete();
            }
        }


        return $final_total;
    }
    /**
     * createOrUpdateAddStockLines
     *
     * @param [mix] $transfer_lines
     * @param [mix] $transaction
     * @return void
     */
    public function createOrUpdateTransferLines($transfer_lines, $transaction)
    {

        $keep_lines_ids = [];

        foreach ($transfer_lines as $line) {
            if (!empty($line['transfer_line_id'])) {
                $transfer_line = TransferLine::find($line['transfer_line_id']);

                $transfer_line->product_id = $line['product_id'];
                $transfer_line->variation_id = $line['variation_id'];
                $old_qty = $transfer_line->quantity;
                $transfer_line->quantity = $this->num_uf($line['quantity']);
                $transfer_line->purchase_price = $this->num_uf($line['purchase_price']);
                $transfer_line->sub_total = $this->num_uf($line['sub_total']);
                $transfer_line->save();
                $keep_lines_ids[] = $line['transfer_line_id'];
                $this->decreaseProductQuantity($line['product_id'], $line['variation_id'], $transaction->sender_store_id, $line['quantity'], $old_qty);
                $this->updateProductQuantityStore($line['product_id'], $line['variation_id'], $transaction->receiver_store_id,  $line['quantity'], $old_qty);
            } else {
                $transfer_line_data = [
                    'transaction_id' => $transaction->id,
                    'product_id' => $line['product_id'],
                    'variation_id' => $line['variation_id'],
                    'quantity' => $this->num_uf($line['quantity']),
                    'purchase_price' => $this->num_uf($line['purchase_price']),
                    'sub_total' => $this->num_uf($line['sub_total']),
                ];

                $transfer_line = TransferLine::create($transfer_line_data);

                $keep_lines_ids[] = $transfer_line->id;
                $this->decreaseProductQuantity($line['product_id'], $line['variation_id'], $transaction->sender_store_id, $line['quantity'], 0);
                $this->updateProductQuantityStore($line['product_id'], $line['variation_id'], $transaction->receiver_store_id,  $line['quantity'], 0);
            }
        }

        if (!empty($keep_lines_ids)) {
            $deleted_lines = TransferLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_lines_ids)->get();
            foreach ($deleted_lines as $deleted_line) {
                $this->decreaseProductQuantity($deleted_line['product_id'], $deleted_line['variation_id'], $transaction->receiver_store_id, $deleted_line['quantity'], 0);
                $this->updateProductQuantityStore($deleted_line['product_id'], $deleted_line['variation_id'], $transaction->sender_store_id,  $deleted_line['quantity'], 0);
                $deleted_line->delete();
            }
        }


        return true;
    }

    /**
     * createOrUpdatePurchaseReturnLine
     *
     * @param [mix] $purchase_return_lines
     * @param [mix] $transaction
     * @return void
     */
    public function createOrUpdatePurchaseReturnLine($purchase_return_lines, $transaction)
    {

        $keep_lines_ids = [];

        foreach ($purchase_return_lines as $line) {
            if (!empty($line['add_stock_line_id'])) {
                $purchase_return_line = PurchaseReturnLine::find($line['purchase_return_line_id']);

                $purchase_return_line->product_id = $line['product_id'];
                $purchase_return_line->variation_id = $line['variation_id'];
                $purchase_return_line->quantity = $this->num_uf($line['quantity']);
                $purchase_return_line->purchase_price = $this->num_uf($line['purchase_price']);
                $purchase_return_line->sub_total = $this->num_uf($line['sub_total']);

                $purchase_return_line->save();
                if ($transaction->status != 'pending') {
                    $this->decreaseProductQuantity($line['product_id'], $line['variation_id'], $transaction->store_id, $line['quantity'], $purchase_return_line->quantity);
                }
                $keep_lines_ids[] = $line['purchase_return_line_id'];
            } else {
                $purchase_return_line_data = [
                    'transaction_id' => $transaction->id,
                    'product_id' => $line['product_id'],
                    'variation_id' => $line['variation_id'],
                    'quantity' => $this->num_uf($line['quantity']),
                    'purchase_price' => $this->num_uf($line['purchase_price']),
                    'sub_total' => $this->num_uf($line['sub_total']),
                ];

                if ($transaction->status != 'pending') {
                    $this->decreaseProductQuantity($line['product_id'], $line['variation_id'], $transaction->store_id, $line['quantity'], 0);
                }
                $purchase_return_line = PurchaseReturnLine::create($purchase_return_line_data);

                $keep_lines_ids[] = $purchase_return_line->id;
            }
        }

        if (!empty($keep_lines_ids)) {
            $deleted_lines = PurchaseReturnLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_lines_ids)->get();
            foreach ($deleted_lines as $deleted_line) {
                $this->updateProductQuantityStore($deleted_line->product_id, $deleted_line->variation_id, $transaction->store_id, $deleted_line->quantity, 0);
                $deleted_line->delete();
            }
        }

        return true;
    }

    /**
     * Checks if products has manage stock enabled then Updates quantity for product and its
     * variations
     *
     * @param $product_id
     * @param $variation_id
     * @param $store_id
     * @param $new_quantity
     * @param $old_quantity = 0
     *
     * @return boolean
     */
    public function updateProductQuantityStore($product_id, $variation_id, $store_id, $new_quantity, $old_quantity = 0)
    {
        $qty_difference = $new_quantity - $old_quantity;

        if ($qty_difference != 0) {
            $product_store = ProductStore::where('variation_id', $variation_id)
                ->where('product_id', $product_id)
                ->where('store_id', $store_id)
                ->first();

            if (empty($product_store)) {
                $product_store = new ProductStore();
                $product_store->variation_id = $variation_id;
                $product_store->product_id = $product_id;
                $product_store->store_id = $store_id;
                $product_store->qty_available = 0;
            }

            $product_store->qty_available += $qty_difference;
            $product_store->save();
        }

        return true;
    }


    /**
     * Checks if products has manage stock enabled then Decrease quantity for product and its variations
     *
     * @param $product_id
     * @param $variation_id
     * @param $store_id
     * @param $new_quantity
     * @param $old_quantity = 0
     *
     * @return boolean
     */
    public function decreaseProductQuantity($product_id, $variation_id, $store_id, $new_quantity, $old_quantity = 0)
    {
        $qty_difference = $new_quantity - $old_quantity;
        $product = Product::find($product_id);

        //Check if stock is enabled or not.
        if ($product->is_service != 1) {
            //Decrement Quantity in variations store table
            $details = ProductStore::where('variation_id', $variation_id)
                ->where('product_id', $product_id)
                ->where('store_id', $store_id)
                ->first();

            //If store details not exists create new one
            if (empty($details)) {
                $details = ProductStore::create([
                    'product_id' => $product_id,
                    'store_id' => $store_id,
                    'variation_id' => $variation_id,
                    'qty_available' => 0
                ]);
            }

            $details->decrement('qty_available', $qty_difference);
        }

        return true;
    }

    /**
     * update the block quantity for quotations
     *
     * @param int $product_id
     * @param int $variation_id
     * @param int $store_id
     * @param int $new_quantity
     * @param string $old_quantity
     * @return void
     */
    public function updateBlockQuantity($product_id, $variation_id, $store_id, $qty, $type = 'add')
    {
        if ($type == 'add') {
            ProductStore::where('product_id', $product_id)->where('variation_id', $variation_id)->where('store_id', $store_id)->increment('block_qty', $qty);
        }
        if ($type == 'subtract') {
            ProductStore::where('product_id', $product_id)->where('variation_id', $variation_id)->where('store_id', $store_id)->decrement('block_qty', $qty);
        }
    }

    /**
     * extract products using product tree selection
     *
     * @param array $data_selected
     * @return array
     */
    public function extractProductIdsfromProductTree($data_selected)
    {
        $product_ids = [];

        // if (!empty($data_selected['product_class_selected'])) {
        //     $pcp = Product::whereIn('product_class_id', $data_selected['product_class_selected'])->select('id')->pluck('id')->toArray();
        //     $product_ids = array_merge($product_ids, $pcp);
        // }
        // if (!empty($data_selected['category_selected'])) {
        //     $cp = array_values(Product::whereIn('category_id', $data_selected['category_selected'])->select('id')->pluck('id')->toArray());
        //     $product_ids = array_merge($product_ids, $cp);
        // }
        // if (!empty($data_selected['sub_category_selected'])) {
        //     $scp = array_values(Product::whereIn('sub_category_id', $data_selected['sub_category_selected'])->select('id')->pluck('id')->toArray());
        //     $product_ids = array_merge($product_ids, $scp);
        // }
        // if (!empty($data_selected['brand_selected'])) {
        //     $bp = array_values(Product::whereIn('brand_id', $data_selected['brand_selected'])->select('id')->pluck('id')->toArray());
        //     $product_ids = array_merge($product_ids, $bp);
        // }
        if (!empty($data_selected['product_selected'])) {
            $p = array_values(Product::whereIn('id', $data_selected['product_selected'])->select('id')->pluck('id')->toArray());
            $product_ids = array_merge($product_ids, $p);
        }

        $product_ids  = array_unique($product_ids);

        return (array)$product_ids;
    }

    public function getProductDetailsUsingArrayIds($array, $store_ids = null)
    {
        $query = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id');

        if (!empty($store_ids)) {
            $query->whereIn('product_stores.store_id', $store_ids);
        }
        $query->whereIn('products.id', $array)
            ->select(
                'products.*',
                DB::raw('SUM(product_stores.qty_available) as current_stock'),
                DB::raw("(SELECT transaction_date FROM transactions LEFT JOIN add_stock_lines ON transactions.id=add_stock_lines.transaction_id WHERE add_stock_lines.product_id=products.id ORDER BY transaction_date DESC LIMIT 1) as date_of_purchase")
            )
            ->groupBy('products.id');

        $products = $query->get();

        return $products;
    }

    /**
     * get the stock value by store id
     *
     * @param int $store_id
     * @return void
     */
    public function getCurrentStockValueByStore($store_id = null)
    {
        $query = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id')
            ->where('is_service', 0);
        if (!empty($store_id)) {
            $query->where('product_stores.store_id', $store_id);
        }
        $query->select(
            DB::raw('SUM(product_stores.qty_available * products.purchase_price) as current_stock_value'),
        );

        $current_stock_value = $query->first();

        return $current_stock_value ? $current_stock_value->current_stock_value : 0;
    }


    /**
     * get product list for product tree
     *
     * @return void
     */
    public function getProductList($store_array = [], $sender_store_id = null)
    {
        $products = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->leftjoin('product_stores', 'variations.id', 'product_stores.variation_id')
            ->leftjoin('stores', 'product_stores.store_id', 'stores.id');

        if (!empty($store_array)) {
            $products->whereIn('product_stores.store_id', $store_array);
        }
        if (!empty(request()->store_id)) {
            $products->where('product_stores.store_id', request()->store_id);
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
        if (!empty(request()->is_raw_material)) {
            $products->where('is_raw_material', 1);
        } else {
            $products->where('is_raw_material', 0);
        }

        $products->where('is_service', 0);

        if (empty($sender_store_id)) {
            $products = $products->select(
                'products.*',
                'variations.id as variation_id',
                'stores.name as store_name',
                'stores.id as store_id',
                DB::raw('SUM(product_stores.qty_available) as current_stock'),
            )->having('current_stock', '>', 0)
                ->groupBy('products.id', 'product_stores.id')
                ->get();
        } else {
            $products = $products->select(
                'products.*',
                'variations.id as variation_id',
                'stores.name as store_name',
                'stores.id as store_id',
                // DB::raw('SUM(product_stores.qty_available) as current_stock'),
                DB::raw('(SELECT SUM(product_stores.qty_available) FROM product_stores WHERE product_stores.product_id=products.id AND product_stores.store_id=' . $sender_store_id . ') as current_stock')
            )->having('current_stock', '>', 0)
                ->groupBy('products.id', 'product_stores.id')
                ->get();
        }

        return $products;
    }

    /**
     * Filters product as per the given inputs and return the details.
     *
     * @param string $search_type (like or exact)
     *
     * @return object
     */
    public function filterProduct($search_term, $search_fields = [], $search_type = 'like')
    {

        $query = Product::join('variations', 'products.id', '=', 'variations.product_id')
            ->active()
            ->whereNull('variations.deleted_at');


        if (!empty($product_types)) {
            $query->whereIn('products.type', $product_types);
        }

        //Include search
        if (!empty($search_term)) {

            //Search with like condition
            if ($search_type == 'like') {
                $query->where(function ($query) use ($search_term, $search_fields) {

                    if (in_array('name', $search_fields)) {
                        $query->where('products.name', 'like', '%' . $search_term . '%');
                    }

                    if (in_array('sku', $search_fields)) {
                        $query->orWhere('sku', 'like', '%' . $search_term . '%');
                    }

                    if (in_array('sub_sku', $search_fields)) {
                        $query->orWhere('sub_sku', 'like', '%' . $search_term . '%');
                    }

                    if (in_array('lot', $search_fields)) {
                        $query->orWhere('pl.lot_number', 'like', '%' . $search_term . '%');
                    }
                });
            }

            //Search with exact condition
            if ($search_type == 'exact') {
                $query->where(function ($query) use ($search_term, $search_fields) {

                    if (in_array('name', $search_fields)) {
                        $query->where('products.name', $search_term);
                    }

                    if (in_array('sku', $search_fields)) {
                        $query->orWhere('sku', $search_term);
                    }

                    if (in_array('sub_sku', $search_fields)) {
                        $query->orWhere('sub_sku', $search_term);
                    }

                    if (in_array('lot', $search_fields)) {
                        $query->orWhere('pl.lot_number', $search_term);
                    }
                });
            }
        }

        $query->select(
            'products.id as product_id',
            'products.name',
            'products.type',
            'variations.id as variation_id',
            'variations.name as variation',
            'variations.default_sell_price',
            'variations.sub_sku',
        );

        $query->groupBy('variations.id');
        return $query
            ->get();
    }

    public function getNonIdentifiableProductDetails($name, $sell_price, $purchase_price, $request)
    {
        $product_exist = Product::where('name', $name)->first();

        if (!empty($product_exist)) {
            $product_exist->purchase_price = $purchase_price;
            $product_exist->sell_price = $sell_price;

            $product_exist->save();
            $variation = Variation::where('product_id', $product_exist->id)->first();
            $variation->default_purchase_price = $purchase_price;
            $variation->default_sell_price = $sell_price;
            $variation->save();
        } else {
            $product_data = [
                'name' => $name,
                'sku' => !empty($sku) ? $sku : $this->generateSku($name),
                'multiple_units' => [],
                'multiple_colors' => [],
                'multiple_sizes' => [],
                'multiple_grades' => [],
                'is_service' => 1,
                'product_details' => null,
                'barcode_type' => 'C128',
                'alert_quantity' => 0,
                'purchase_price' => $purchase_price,
                'sell_price' => $sell_price,
                'tax_id' => null,
                'tax_method' => null,
                'discount_type' => 'fixed',
                'discount_customer_types' => [],
                'discount_customers' => [],
                'discount' => 0,
                'discount_start_date' => null,
                'discount_end_date' => null,
                'show_to_customer' => 0,
                'show_to_customer_types' => 0,
                'different_prices_for_stores' => 0,
                'this_product_have_variant' => 0,
                'type' => 'single',
                'active' => 0,
                'created_by' => Auth::user()->id
            ];
            $product = Product::create($product_data);

            $this->createOrUpdateVariations($product, $request);
        }

        $query = Product::join('variations', 'products.id', '=', 'variations.product_id')
            ->whereNull('variations.deleted_at')
            ->where('products.name', $name);

        $query->select(
            'products.id as product_id',
            'products.name',
            'products.type',
            'variations.id as variation_id',
            'variations.name as variation',
            'variations.default_sell_price',
            'variations.sub_sku',
        );

        $query->groupBy('variations.id');
        return $query
            ->first();
    }

    public function getCurrentStockDataByProduct($product_id, $store_id = null)
    {
        $query = ProductStore::where('product_id', $product_id);

        if (!empty($store_id)) {
            $query->where('store_id', $store_id);
        }

        $current_Stock = $query->sum('qty_available');
        return ['current_stock' => $current_Stock];
    }

    public function createProductStoreForThisStoreIfNotExist($store_id)
    {
        $variations = Variation::whereNull('deleted_at')->get();

        foreach ($variations as $variation) {
            $product_store = ProductStore::where('product_id', $variation->product_id)->where('variation_id', $variation->id)->where('store_id', $store_id)->first();

            if (empty($product_store)) {
                $product_store = new ProductStore();
                $product_store->product_id = $variation->product_id;
                $product_store->variation_id = $variation->id;
                $product_store->store_id = $store_id;
                $product_store->qty_available = 0;
                $product_store->save();
            }
        }
    }
}
