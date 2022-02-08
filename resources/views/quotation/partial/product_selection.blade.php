<button type="button" class="btn btn-success" data-toggle="modal" data-target="#select_products_modal"
    style="margin-top: 15px;">
    @lang('lang.select_products')
</button>
<div class="modal fade" id="select_products_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true" style="width: 100%;">
    <div class="modal-dialog modal-lg" role="document" id="select_products_modal">
        <div class="modal-content">
            <div class="modal-header">

                <h4 class="modal-title">@lang( 'lang.select_products' )</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="col-md-12">
                    <div class="card mt-3">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_product_class_id', __('lang.product_class') . ':', []) !!}
                                        {!! Form::select('filter_product_class_id', $product_classes,
                                        request()->filter_product_class_id,
                                        ['class'
                                        => 'form-control filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_category_id', __('lang.category') . ':', []) !!}
                                        {!! Form::select('filter_category_id', $categories, request()->category_id, ['class' =>
                                        'form-control filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_sub_category_id', __('lang.sub_category') . ':', []) !!}
                                        {!! Form::select('filter_sub_category_id', $sub_categories, request()->sub_category_id,
                                        ['class' =>
                                        'form-control filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_brand_id', __('lang.brand') . ':', []) !!}
                                        {!! Form::select('filter_brand_id', $brands, request()->brand_id, ['class' =>
                                        'form-control
                                        filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_unit_id', __('lang.unit') . ':', []) !!}
                                        {!! Form::select('filter_unit_id', $units, request()->unit_id, ['class' =>
                                        'form-control
                                        filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_color_id', __('lang.color') . ':', []) !!}
                                        {!! Form::select('filter_color_id', $colors, request()->color_id, ['class' =>
                                        'form-control
                                        filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_size_id', __('lang.size') . ':', []) !!}
                                        {!! Form::select('filter_size_id', $sizes, request()->size_id, ['class' =>
                                        'form-control
                                        filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_grade_id', __('lang.grade') . ':', []) !!}
                                        {!! Form::select('filter_grade_id', $grades, request()->grade_id, ['class' =>
                                        'form-control
                                        filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_tax_id', __('lang.tax') . ':', []) !!}
                                        {!! Form::select('filter_tax_id', $taxes_array, request()->tax_id, ['class' =>
                                        'form-control
                                        filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_store_id', __('lang.store'), []) !!}
                                        {!! Form::select('filter_store_id', $stores, request()->store_id, ['class' =>
                                        'form-control filter_product', 'placeholder' =>
                                        __('lang.all'),'data-live-search'=>"true"])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_customer_type_id', __('lang.customer_type') . ':', []) !!}
                                        {!! Form::select('filter_customer_type_id', $customer_types,
                                        request()->customer_type_id,
                                        ['class'
                                        => 'form-control filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        {!! Form::label('filter_created_by', __('lang.created_by') . ':', []) !!}
                                        {!! Form::select('filter_created_by', $users, request()->created_by, ['class'
                                        => 'form-control filter_product
                                        selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')])
                                        !!}
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <button type="button"
                                        class="btn btn-danger mt-4 clear_filters">@lang('lang.clear_filters')</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" value="0"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.image')</button>
                            <button type="button" value="3"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.class')</button>
                            <button type="button" value="4"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.category')</button>
                            <button type="button" value="5"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.sub_category')</button>
                            <button type="button" value="6"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.purchase_history')</button>
                            <button type="button" value="7"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.batch_number')</button>
                            <button type="button" value="8"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.selling_price')</button>
                            <button type="button" value="9"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.tax')</button>
                            <button type="button" value="10"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.brand')</button>
                            <button type="button" value="11"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.unit')</button>
                            <button type="button" value="12"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.color')</button>
                            <button type="button" value="13"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.size')</button>
                            <button type="button" value="14"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.grade')</button>
                            @if(empty($page))
                            <button type="button" value="15"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.current_stock')</button>
                            @endif
                            <button type="button" value="16"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.customer_type')</button>
                            <button type="button" value="17"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.expiry_date')</button>
                            <button type="button" value="18"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.manufacturing_date')</button>
                            <button type="button" value="19"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.discount')</button>
                            @can('product_module.purchase_price.view')
                            <button type="button" value="20"
                                class="badge badge-pill badge-primary column-toggle">@lang('lang.purchase_price')</button>
                            @endcan
                        </div>
                    </div>


                </div>
                <div class="table-responsive">
                    <table id="product_selection_table" class="table" style="width: auto">
                        <thead>
                            <tr>
                                <th>@lang('lang.select')</th>
                                <th>@lang('lang.image')</th>
                                <th>@lang('lang.name')</th>
                                <th>@lang('lang.product_code')</th>
                                <th>@lang('lang.class')</th>
                                <th>@lang('lang.category')</th>
                                <th>@lang('lang.sub_category')</th>
                                <th>@lang('lang.purchase_history')</th>
                                <th>@lang('lang.batch_number')</th>
                                <th>@lang('lang.selling_price')</th>
                                <th>@lang('lang.tax')</th>
                                <th>@lang('lang.brand')</th>
                                <th>@lang('lang.unit')</th>
                                <th>@lang('lang.color')</th>
                                <th>@lang('lang.size')</th>
                                <th>@lang('lang.grade')</th>
                                <th class="sum">@lang('lang.current_stock')</th>
                                <th>@lang('lang.customer_type')</th>
                                <th>@lang('lang.expiry_date')</th>
                                <th>@lang('lang.manufacturing_date')</th>
                                <th>@lang('lang.discount')</th>
                                @can('product_module.purchase_price.view')
                                <th>@lang('lang.purchase_price')</th>
                                @endcan
                                <th>@lang('lang.supplier')</th>
                                <th>@lang('lang.created_by')</th>
                                <th>@lang('lang.edited_by')</th>
                                <th class="notexport">@lang('lang.action')</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="16" style="text-align: right">@lang('lang.total')</th>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="add-selected-btn">@lang( 'lang.add' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
            </div>

        </div>
    </div>
</div>
