<?php

namespace App\Utils;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\ProductStore;
use App\Models\PurchaseOrderLine;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\Variation;
use App\Utils\Util;
use Carbon\Carbon;
use GuzzleHttp\Client;
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
    public function generateSubSku($sku, $c, $barcode_type)
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
    public function getNumberByTye($type, $store_id = null)
    {
        $number = '';
        $store_string = '';
        if(!empty($store_id)){
            $store_string = $this->getStoreNameFirstLetters($store_id);
        }
        if($type == 'purchase_order'){
            $po_count = Transaction::where('type', 'purchase_order')->count() + 1;

            $number = 'PO'. $store_string . $po_count;
        }


        return $number;
    }

    public function getStoreNameFirstLetters($store_id){
        $string = '';
        $store = Store::find($store_id);

        if(!empty($store)){
            $name = explode(" ", $store->name);

            foreach ($name as $w) {
              $string .= $w[0];
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

    //create or update product variation data
    public function createOrUpdateVariations($product, $request)
    {
        $variations = $request->variations;
        $keey_variations = [];
        if (!empty($variations)) {
            foreach ($variations as $v) {
                if (!empty($v['id'])) {
                    $variation = Variation::find($v['id']);
                    $variation->name = $v['name'];
                    $variation->color_id = $v['color_id'];
                    $variation->size_id = $v['size_id'];
                    $variation->grade_id = $v['grade_id'];
                    $variation->unit_id = $v['unit_id'];
                    $variation->default_purchase_price = $this->num_uf($v['default_purchase_price']);
                    $variation->default_sell_price = $this->num_uf($v['default_sell_price']);
                    $variation->save();
                    $variation_array[] = ['variation' => $variation, 'variant_stores' => $v['variant_stores']];
                    $keey_variations[] = $v['id'];
                } else {
                    $c = Variation::where('product_id', $product->id)
                        ->count() + 1;
                    $variation_data['name'] = $v['name'];
                    $variation_data['product_id'] = $product->id;
                    $variation_data['sub_sku'] = !empty($v['sub_sku']) ? $v['sub_sku'] : $this->generateSubSku($product->sku, $c, $product->barcode_type);
                    $variation_data['color_id'] = $v['color_id'];
                    $variation_data['size_id'] = $v['size_id'];
                    $variation_data['grade_id'] = $v['grade_id'];
                    $variation_data['unit_id'] = $v['unit_id'];
                    $variation_data['default_purchase_price'] = $this->num_uf($v['default_purchase_price']);
                    $variation_data['default_sell_price'] = $this->num_uf($v['default_sell_price']);
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
            $variation_data['color_id'] = null;
            $variation_data['size_id'] = null;
            $variation_data['grade_id'] = null;
            $variation_data['unit_id'] = null;
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
                            $tree .= 'text: "' . $product->name .'",';
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
     * @param int $business_id
     * @param int $product_id
     * @param int $variation_id = null
     *
     * @return Obj
     */
    public function getDetailsFromProduct($product_id, $variation_id = null)
    {
        $product = Product::leftjoin('variations as v', 'products.id', '=', 'v.product_id')
            ->whereNull('v.deleted_at');

        if (!is_null($variation_id) && $variation_id !== '0') {
            $product->where('v.id', $variation_id);
        }

        $product->where('products.id', $product_id);

        $products = $product->select(
            'products.id as product_id',
            'products.name as product_name',
            'v.id as variation_id',
            'v.name as variation_name',
            'v.default_purchase_price',
            'v.sub_sku'
        )
            ->get();

        return $products;
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

            ->where('variations.id', $variation_id);


        if (!empty($store_id) && $check_qty) {
            //Check for enable stock, if enabled check for location id.
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
            'variations.default_sell_price',
            'variations.id as variation_id',
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
                    'quantity' =>$this->num_uf($line['quantity']),
                    'purchase_price' => $this->num_uf($line['purchase_price']),
                    'sub_total' => $this->num_uf($line['sub_total']),
                ];

                $purchase_order_line = PurchaseOrderLine::create($purchase_order_line_data);

                $keep_lines_ids[] = $purchase_order_line->id;
            }
        }

        if(!empty($keep_lines_ids)){
            PurchaseOrderLine::where('transaction_id', $transaction->id)->whereNotIn('id', $keep_lines_ids)->delete();
        }

        return true;
    }


    // public function updateProductStock($status_before, $transaction, $product_id, $variation_id, $new_quantity, $old_quantity, $currency_details, $store_id = null)
    // {
    //     $new_quantity_f = $this->num_f($new_quantity);
    //     $old_qty = $this->num_f($old_quantity);
    //     //Update quantity for existing products

    //     if ($status_before == 'received' && $transaction->status == 'received') {
    //         //if status received update existing quantity
    //         $this->updateProductQuantity($transaction->location_id, $product_id, $variation_id, $new_quantity_f, $old_qty, $currency_details);
    //         $this->updateProductQuantityStore($transaction->location_id, $product_id, $variation_id, $new_quantity_f, $store_id, $old_qty, $currency_details);
    //     } elseif ($status_before == 'received' && $transaction->status != 'received') {
    //         //decrease quantity only if status changed from received to not received
    //         $this->decreaseProductQuantity(
    //             $product_id,
    //             $variation_id,
    //             $transaction->location_id,
    //             $old_quantity
    //         );
    //         $this->decreaseProductQuantityStore(
    //             $product_id,
    //             $variation_id,
    //             $transaction->location_id,
    //             $old_quantity,
    //             $store_id
    //         );
    //     } elseif ($status_before != 'received' && $transaction->status == 'received') {
    //         $this->updateProductQuantity($transaction->location_id, $product_id, $variation_id, $new_quantity_f, 0, $currency_details);
    //     }
    // }

    // public function decreaseProductQuantity($product_id, $variation_id, $location_id, $new_quantity, $old_quantity = 0, $adjustment_type = null) //$adjustment_type for stock adjustment
    // {
    //     $qty_difference = $new_quantity - $old_quantity;

    //     $product = Product::find($product_id);

    //     //Check if stock is enabled or not.
    //     if ($product->enable_stock == 1) {
    //         //Decrement Quantity in variations location table
    //         $details = VariationLocationDetails::where('variation_id', $variation_id)
    //             ->where('product_id', $product_id)
    //             ->where('location_id', $location_id)
    //             ->first();

    //         //If location details not exists create new one
    //         if (empty($details)) {
    //             $variation = Variation::find($variation_id);
    //             $details = VariationLocationDetails::create([
    //                 'product_id' => $product_id,
    //                 'location_id' => $location_id,
    //                 'variation_id' => $variation_id,
    //                 'product_variation_id' => $variation->product_variation_id,
    //                 'qty_available' => 0
    //             ]);
    //         }
    //         if (!empty($adjustment_type)) {
    //             if ($adjustment_type == 'increase') {
    //                 $details->increment('qty_available', $qty_difference);
    //             }
    //             if ($adjustment_type == 'decrease') {
    //                 $details->decrement('qty_available', $qty_difference);
    //             }
    //         } else {
    //             $details->decrement('qty_available', $qty_difference);
    //         }
    //     }

    //     return true;
    // }
}
