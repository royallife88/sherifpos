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
     * @param int $productUtil
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
        foreach($rows as $row){
            $unit_row = explode(',', $row['units']);
            $unit_ids = Unit::whereIn('name', $unit_row )->pluck('id')->toArray();
            $color_row = explode(',', $row['colors']);
            $color_ids = Color::whereIn('name', $color_row )->pluck('id')->toArray();
            $sizes_row = explode(',', $row['sizes']);
            $sizes_ids = Size::whereIn('name', $sizes_row )->pluck('id')->toArray();
            $grades_row = explode(',', $row['grades']);
            $grades_ids = Grade::whereIn('name', $grades_row )->pluck('id')->toArray();

            $class = ProductClass::where('name', $row['class'])->first();
            $category = Category::where('name', $row['category'])->first();
            $sub_category = Category::where('name', $row['sub_category'])->first();
            $brand = Brand::where('name', $row['brand'])->first();
            $tax = Tax::where('name', $row['tax'])->first();

            $product_data = [
                'name' => $row['product_name'],
                'product_class_id' => !empty($class) ? $class->id : null,
                'category_id' => !empty($category) ? $category->id : null,
                'sub_category_id' => !empty($sub_category) ? $sub_category->id : null,
                'brand_id' => !empty($brand) ? $brand->id : null,
                'sku' => $row['sku'],
                'multiple_units' => $unit_ids,
                'multiple_colors' => $color_ids,
                'multiple_sizes' => $sizes_ids,
                'multiple_grades' => $grades_ids,
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
            'category' => 'required',
            'sub_category' => 'required',
            'brand' => 'required',
            'sku' => 'required',
            'sell_price' => 'required|numeric',
            'purchase_price' => 'required|numeric',
            'alert_quantity' => 'required|numeric',
        ];
    }
}
