@extends('layouts.app')
@section('title', __('lang.product'))

@section('content')
<div class="container-fluid">
    <a style="color: white" href="{{action('ProductController@create')}}" class="btn btn-info"><i
            class="dripicons-plus"></i>
        @lang('lang.add_product')</a>
    <a style="color: white" href="{{action('ProductController@getImport')}}" class="btn btn-primary"><i
            class="fa fa-arrow-down"></i>
        @lang('lang.import')</a>

    <div class="card mt-3">
        <div class="col-md-12">
            <form accept="" method="GET">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('product_class_id', __('lang.product_class') . ':', []) !!}
                            {!! Form::select('product_class_id', $product_classes, request()->product_class_id, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('category_id', __('lang.category') . ':', []) !!}
                            {!! Form::select('category_id', $categories, request()->category_id, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('sub_category_id', __('lang.sub_category') . ':', []) !!}
                            {!! Form::select('sub_category_id', $sub_categories, request()->sub_category_id, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('brand_id', __('lang.brand') . ':', []) !!}
                            {!! Form::select('brand_id', $brands, request()->brand_id, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('unit_id', __('lang.unit') . ':', []) !!}
                            {!! Form::select('unit_id', $units, request()->unit_id, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('color_id', __('lang.color') . ':', []) !!}
                            {!! Form::select('color_id', $colors, request()->color_id, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('size_id', __('lang.size') . ':', []) !!}
                            {!! Form::select('size_id', $sizes, request()->size_id, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('grade_id', __('lang.grade') . ':', []) !!}
                            {!! Form::select('grade_id', $grades, request()->grade_id, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('tax_id', __('lang.tax') . ':', []) !!}
                            {!! Form::select('tax_id', $taxes, request()->tax_id, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('customer_type_id', __('lang.customer_type') . ':', []) !!}
                            {!! Form::select('customer_type_id', $customer_types, request()->customer_type_id, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('batch_number', __('lang.batch_number') . ':', []) !!}
                            {!! Form::select('batch_number', $batch_numbers, false, ['class' => 'form-control
                            selectpicker', 'data-live-search' =>'true', 'placeholder' => __('lang.all')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <a class="btn btn-primary mt-4"
                            href="{{action('ProductController@index')}}">@lang('lang.clear_filters')</a>
                        <button type="submit" class="btn btn-danger mt-4">@lang('lang.filter')</button>

                    </div>
                </div>
            </form>
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
            <button type="button" value="15"
                class="badge badge-pill badge-primary column-toggle">@lang('lang.current_stock')</button>
            <button type="button" value="16"
                class="badge badge-pill badge-primary column-toggle">@lang('lang.customer_type')</button>
            <button type="button" value="17"
                class="badge badge-pill badge-primary column-toggle">@lang('lang.expiry_date')</button>
            <button type="button" value="18"
                class="badge badge-pill badge-primary column-toggle">@lang('lang.manufacturing_date')</button>
            <button type="button" value="19"
                class="badge badge-pill badge-primary column-toggle">@lang('lang.discount')</button>
            <button type="button" value="20"
                class="badge badge-pill badge-primary column-toggle">@lang('lang.purchase_price')</button>
            <button type="button" value="21"
                class="badge badge-pill badge-primary column-toggle">@lang('lang.barcode')</button>
        </div>
    </div>


</div>
<div class="table-responsive">
    <table id="product_table" class="table dataTable">
        <thead>
            <tr>
                <th>@lang('lang.image')</th>
                <th>@lang('lang.name')</th>
                <th>@lang('lang.barcode')</th>
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
                <th>@lang('lang.current_stock')</th>
                <th>@lang('lang.customer_type')</th>
                <th>@lang('lang.expiry_date')</th>
                <th>@lang('lang.manufacturing_date')</th>
                <th>@lang('lang.discount')</th>
                <th>@lang('lang.purchase_price')</th>

                <th class="notexport">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td><img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('images/default.jpg')}}@endif"
                        alt="photo" width="50" height="50"></td>
                <td>{{$product->name}}</td>
                <td><img class="center-block" style="width:150px; !important;height: {{2*0.24}}in !important;"
                        src="data:image/png;base64,{{DNS1D::getBarcodePNG($product->sku,$product->barcode_type, 3,30,array(39, 48, 54), true)}}">
                </td>
                <td>{{$product->product_class->name}}</td>
                <td>{{$product->category->name}}</td>
                <td>{{$product->sub_category->name}}</td>
                <td><a data-href="{{action('ProductController@getPurchaseHistory', $product->id)}}" data-container=".view_modal" class="btn btn-modal">@lang('lang.view')</a></td>
                <td>{{$product->batch_number}}</td>
                <td>{{@num_format($product->sell_price)}}</td>
                <td>@if(!empty($product->tax->name)){{$product->tax->name}}@endif</td>
                <td>{{$product->brand->name}}</td>
                <td>{{implode(', ', $product->units->pluck('name')->toArray())}}</td>
                <td>{{implode(', ', $product->colors->pluck('name')->toArray())}}</td>
                <td>{{implode(', ', $product->sizes->pluck('name')->toArray())}}</td>
                <td>{{implode(', ', $product->grades->pluck('name')->toArray())}}</td>
                <td>{{@num_format($product->current_stock)}}</td>
                <td>{{$product->customer_type}}</td>
                <td>@if(!empty($product->expiry_date)){{@format_date($product->expiry_date)}}@endif</td>
                <td>@if(!empty($product->manufacturing_date)){{@format_date($product->manufacturing_date)}}@endif
                </td>
                <td>{{@num_format($product->discount)}}</td>
                <td>{{@num_format($product->purchase_price)}}</td>

                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            @can('product_module.product.view')
                            <li>
                                <a data-href="{{action('ProductController@show', $product->id)}}"  data-container=".view_modal"
                                  class="btn btn-modal"><i
                                        class="fa fa-eye"></i> @lang('lang.view')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('product_module.product.create_and_edit')
                            <li>

                                <a href="{{action('ProductController@edit', $product->id)}}"
                                  class="btn"><i
                                        class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('product_module.product.delete')
                            <li>
                                <a data-href="{{action('ProductController@destroy', $product->id)}}"
                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                    class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                    @lang('lang.delete')</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </td>
            </tr>

            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('javascript')
<script>
    $(document).on('click', '.column-toggle', function(){
        let column_index = parseInt($(this).val());
        column = table.column(column_index);
        column.visible(! column.visible());

        if(column.visible()){
            console.log('tryuu');
            $(this).addClass('badge-primary')
            $(this).removeClass('badge-warning')
        }else{
            console.log('afaassfd');
            $(this).removeClass('badge-primary')
            $(this).addClass('badge-warning')

        }
    })
</script>
@endsection
