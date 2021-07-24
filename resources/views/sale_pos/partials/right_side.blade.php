<div class="row">
    <br>
    <div class="col-md-12">
        <div class="filter-checkbox card">
            <div class="card-header"  style="padding: 5px 20px; color: #7c5cc4">
                <i class="fa fa-filter" ></i> @lang('lang.filter')
            </div>
            <div class="card-body" style="padding: 5px 20px">
                <div class="row">
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
                    <table id="filter-product-table" class="table no-shadow product-list" style="width: 100%; border: 0px">
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
