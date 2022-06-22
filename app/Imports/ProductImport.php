<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Color;
use App\Models\Grade;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\Size;
use App\Models\Tax;
use App\Models\Unit;
use App\Utils\ProductUtil;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class ProductImport implements ToCollection, WithHeadingRow, WithValidation
{
    protected $productUtil;
    protected $request;

    /**
     * Constructor
     *
     * @param ProductUtil $productUtil
     * @return void
     */
    public function __construct(ProductUtil $productUtil, $request)
    {
        $this->productUtil = $productUtil;
        $this->request = $request;
    }


    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $unit = null;
            $color = null;
            $size = null;
            $grade = null;
            $class = null;
            $category = null;
            $sub_category = null;
            $brand = null;
            $tax = null;
            if (!empty($row['units'])) {
                $unit = Unit::firstOrCreate(['name' => $row['units']]);
            }
            if (!empty($row['colors'])) {
                $color = Color::firstOrCreate(['name' => $row['colors']]);
            }
            if (!empty($row['sizes'])) {
                $size = Size::firstOrCreate(['name' => $row['sizes']]);
            }
            if (!empty($row['grades'])) {
                $grade = Grade::firstOrCreate(['name' => $row['grades']]);
            }

            if (!empty($row['class'])) {
                $class = ProductClass::where('name', $row['class'])->first();
            }
            if (!empty($row['category'])) {
                $category = Category::firstOrCreate(['name' => $row['category']]);
            }
            if (!empty($row['sub_category'])) {
                $sub_category = Category::firstOrCreate(['name' => $row['sub_category']])->first();
            }
            if (!empty($row['brand'])) {
                $brand = Brand::firstOrCreate(['name' => $row['brand']])->first();
            }
            if (!empty($row['tax'])) {
                $tax = Tax::firstOrCreate(['name' => $row['tax']])->first();
            }

            $product_data = [
                'name' => $row['product_name'],
                'product_class_id' => !empty($class) ? $class->id : null,
                'category_id' => !empty($category) ? $category->id : null,
                'sub_category_id' => !empty($sub_category) ? $sub_category->id : null,
                'brand_id' => !empty($brand) ? $brand->id : null,
                'sku' => $row['sku'] ?? $this->productUtil->generateSku($row['product_name']),
                'multiple_units' => !empty($unit) ? [(string)$unit->id] : [],
                'multiple_colors' => !empty($color) ? [(string)$color->id] : [],
                'multiple_sizes' => !empty($size) ? [(string)$size->id] : [],
                'multiple_grades' => !empty($grade) ? [(string)$grade->id] : [],
                'is_service' => !empty($row['is_service']) ? 1 : 0,
                'product_details' => $row['product_details'],
                'batch_number' => $row['batch_number'],
                'barcode_type' => 'C128',
                'manufacturing_date' => !empty($row['manufacturing_date']) ? $row['manufacturing_date'] : null,
                'expiry_date' => !empty($row['expiry_date']) ? $row['expiry_date'] : null,
                'expiry_warning' => $row['expiry_warning'],
                'convert_status_expire' => $row['convert_status_expire'],
                'alert_quantity' => $row['alert_quantity'],
                'purchase_price' => $row['purchase_price'],
                'sell_price' => $row['sell_price'],
                'tax_id' => !empty($tax) ? $tax->id : null,
                'tax_method' => $row['tax_method'],
                'discount_type' => $row['discount_type'],
                'discount_customers' => [],
                'discount' => $row['discount'],
                'discount_start_date' => !empty($row['discount_start_date']) ? $row['discount_start_date'] : null,
                'discount_end_date' => !empty($row['discount_end_date']) ? $row['discount_end_date'] : null,
                'show_to_customer' => 1,
                'show_to_customer_types' => [],
                'different_prices_for_stores' => 0,
                'this_product_have_variant' => 0,
                'type' => 'single',
                'active' => 1,
                'created_by' => Auth::user()->id
            ];

            $product = Product::create($product_data);

            $this->productUtil->createOrUpdateVariations($product, $this->request);
        }
    }

    public function rules(): array
    {
        return [
            'product_name' => 'required',
            'class' => 'required',
            'sku' => 'sometimes|unique:products',
            'sell_price' => 'required|numeric',
            'purchase_price' => 'required|numeric',
        ];
    }
}
