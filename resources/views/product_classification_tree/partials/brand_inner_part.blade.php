@foreach ($brands as $brand)
    <div class="accordion"
        id="{{ @replace_space($product_class_id . '_' . $category_id . '_brand_' . $brand->name . '_' . $i) }}"
        style="margin-left: 20px;">
        <div class="accordion-group">
            <div class="accordion-heading">
                <a class="accordion-toggle" data-toggle="collapse"
                    data-id="{{ @replace_space($product_class_id . '_' . $category_id . '_brand_' . $brand->name . '_' . $i) }}"
                    data-parent="#{{ @replace_space($product_class_id . '_' . $category_id . '_brand_' . $brand->name . '_' . $i) }}"
                    href="#collapse{{ @replace_space($product_class_id . '_' . $category_id . '_brand_' . $brand->name . '_' . $i) }}">
                    <i
                        class="fa fa-angle-right angle-class-{{ @replace_space($product_class_id . '_brand_' . $brand->name . '_' . $i) }}"></i>
                    {{ $brand->name }}
                    <div class="btn-group pull-right">
                        <button data-container=".view_modal"
                            data-href="{{ action('BrandController@edit', $brand->id) }}"
                            class="btn btn-modal btn-primary btn-xs"><i class="dripicons-document-edit"></i>
                        </button>
                        <button data-href="{{ action('BrandController@destroy', $brand->id) }}"
                            data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                            class="btn delete_item btn-danger btn-xs"><i class="dripicons-trash"></i></button>
                    </div>
                </a>
            </div>
            <div id="collapse{{ @replace_space($product_class_id . '_' . $category_id . '_brand_' . $brand->name . '_' . $i) }}"
                class="accordion-body collapse in">
                <div class="accordion-inner">
                    @php
                        $query = App\Models\Product::leftjoin('variations', 'products.id', 'variations.product_id')->where('brand_id', $brand->id);
                        if (!empty($category_id)) {
                            $query->where('category_id', $category_id);
                        }
                        if (!empty($sub_category_id)) {
                            $query->where('sub_category_id', $sub_category_id);
                        }
                        $products = $query
                            ->select('products.id', 'products.name', 'variations.name as variation_name', 'variations.sub_sku as sku', 'variations.default_sell_price as sell_price')
                            ->groupBy('variations.id')
                            ->get();
                    @endphp
                    @foreach ($products as $product)
                        @include('product_classification_tree.partials.product_inner_part', [
                            'product' => $product,
                        ])

                        @php
                            $i++;
                        @endphp
                    @endforeach
                </div>
            </div>
        </div>

    </div>
    @php
        $i++;
    @endphp
@endforeach
