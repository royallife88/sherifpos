<div class="row">
    <br>
    <div class="col-md-12">
        <div class="filter-checkbox card">
            <div class="card-header" style="padding: 5px 20px; color: #7c5cc4">
                <i class="fa fa-filter"></i> @lang('lang.filter')
            </div>
            <div class="card-body" style="padding: 5px 20px">
                <div class="row">
                    <div class="col-md-4">
                        <label class="checkbox-inline">
                            <input class="" type="checkbox" id="category-filter" />
                            @lang('lang.category')
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="checkbox-inline">
                            <input class="" type="checkbox" id="sub-category-filter" />
                            @lang('lang.sub_category')
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="checkbox-inline">
                            <input class="" type="checkbox" id="brand-filter" />
                            @lang('lang.brand')
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="selling_filter" value="best_selling">
                            @lang('lang.best_selling')
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" class="selling_filter" value="slow_moving_items">
                            @lang('lang.slow_moving_items')
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" class="selling_filter" value="product_in_last_transactions">
                            @lang('lang.product_in_last_transactions')
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="price_filter" value="highest_price">
                            @lang('lang.highest_price')
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" class="price_filter" value="lowest_price"> @lang('lang.lowest_price')
                        </label>
                    </div>
                    <div class="col-md-2">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="sorting_filter" value="a_to_z"> @lang('lang.a_to_z')
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" class="sorting_filter" value="z_to_a"> @lang('lang.z_to_a')
                        </label>
                    </div>
                    <div class="col-md-4">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="expiry_filter" value="nearest_expiry">
                            @lang('lang.nearest_expiry')
                        </label>
                        <label class="checkbox-inline">
                            <input type="checkbox" class="expiry_filter" value="longest_expiry">
                            @lang('lang.longest_expiry')
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="sale_promo_filter" value="items_in_sale_promotion">
                            @lang('lang.items_in_sale_promotion')
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12 mt-1 table-container">
                    <div class="filter-window" style="width: 100% !important; height: 100% !important">
                        <div class="category mt-3">
                            <div class="row ml-2 mr-2 px-2">
                                <div class="col-7">@lang('lang.choose_category')</div>
                                <div class="col-5 text-right">
                                    <span class="btn btn-default btn-sm">
                                        <i class="dripicons-cross"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="row ml-2 mt-3">
                                @foreach($categories as $category)
                                <div class="col-md-3 filter-by category-img text-center" data-id="{{$category->id}}"
                                    data-type="category">
                                    <img
                                        src="@if(!empty($category->getFirstMediaUrl('category'))){{$category->getFirstMediaUrl('category')}}@else{{asset('images/default.jpg')}}@endif" />
                                    <p class="text-center">{{$category->name}}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="sub_category mt-3">
                            <div class="row ml-2 mr-2 px-2">
                                <div class="col-7">@lang('lang.choose_sub_category')</div>
                                <div class="col-5 text-right">
                                    <span class="btn btn-default btn-sm">
                                        <i class="dripicons-cross"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="row ml-2 mt-3">
                                @foreach($sub_categories as $category)
                                <div class="col-md-3 filter-by category-img text-center" data-id="{{$category->id}}"
                                    data-type="sub_category">
                                    <img
                                        src="@if(!empty($category->getFirstMediaUrl('category'))){{$category->getFirstMediaUrl('category')}}@else{{asset('images/default.jpg')}}@endif" />
                                    <p class="text-center">{{$category->name}}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="brand mt-3">
                            <div class="row ml-2 mr-2 px-2">
                                <div class="col-7">@lang('lang.choose_brand')</div>
                                <div class="col-5 text-right">
                                    <span class="btn btn-default btn-sm">
                                        <i class="dripicons-cross"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="row ml-2 mt-3">
                                @foreach($brands as $brand)

                                <div class="col-md-3 filter-by brand-img text-center" data-id="{{$brand->id}}"
                                    data-type="brand">
                                    <img
                                        src="@if(!empty($brand->getFirstMediaUrl('brand'))){{$brand->getFirstMediaUrl('brand')}}@else{{asset('images/default.jpg')}}@endif" />
                                    <p class="text-center">{{$brand->name}}</p>
                                </div>

                                @endforeach
                            </div>
                        </div>
                    </div>
                    <table id="filter-product-table" class="table no-shadow product-list"
                        style="width: 100%; border: 0px">
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
