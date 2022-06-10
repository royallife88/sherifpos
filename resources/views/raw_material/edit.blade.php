@extends('layouts.app')
@section('title', __('lang.raw_materials'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.edit_raw_material')</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                        {!! Form::open(['url' => action('RawMaterialController@update', $raw_material->id), 'id' =>
                        'product-edit-form', 'method'
                        =>
                        'PUT', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                {!! Form::label('brand_id', __('lang.brand'), []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('brand_id', $brands,
                                    !empty($raw_material) ? $raw_material->brand_id : false, ['class' => 'selectpicker
                                    form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.brand.create_and_edit')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('BrandController@create')}}?quick_add=1"
                                            data-container=".view_modal"><i
                                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                        @endcan
                                    </span>
                                </div>
                                <div class="error-msg text-red"></div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('name', __('lang.name') . ' *', []) !!}
                                    {!! Form::text('name', !empty($raw_material) ? $raw_material->name : null, ['class'
                                    => 'form-control',
                                    'required', 'placeholder'
                                    => __('lang.name')]) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group supplier_div">
                                    {!! Form::label('supplier_id', __('lang.supplier'), []) !!}
                                    <div class="input-group my-group">
                                        {!! Form::select('supplier_id', $suppliers, !empty($raw_material->supplier) ? $raw_material->supplier->id : false, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                                        <span class="input-group-btn">
                                            @can('supplier_module.supplier.create_and_edit')
                                                <button type="button" class="btn-modal btn btn-default bg-white btn-flat"
                                                    data-href="{{ action('SupplierController@create') }}?quick_add=1"
                                                    data-container=".view_modal"><i class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                            @endcan
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('sku', __('lang.sku') , []) !!}
                                    {!! Form::text('sku', !empty($raw_material) ? $raw_material->sku : null, ['class' =>
                                    'form-control', 'placeholder'
                                    => __('lang.sku')]) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                {!! Form::label('multiple_units', __('lang.unit'), []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('multiple_units[]', $units,
                                    !empty($raw_material) ? $raw_material->multiple_units : false, ['class' =>
                                    'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select'), 'id' =>
                                    'multiple_units']) !!}
                                    <span class="input-group-btn">
                                        @can('product_module.unit.create_and_edit')
                                        <button class="btn-modal btn btn-default bg-white btn-flat"
                                            data-href="{{action('UnitController@create')}}?quick_add=1&is_raw_material_unit=1"
                                            data-container=".view_modal"><i
                                                class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                        @endcan
                                    </span>
                                </div>
                            </div>

                            <div class="col-md-12 mt-3">
                                @if(!empty($raw_material->getFirstMediaUrl('product')))
                                <div style="width: 120px;" class="images_div">
                                    <button type="button" class="delete-image btn btn-danger btn-xs"
                                        data-href="{{action('ProductController@deleteProductImage', $raw_material->id)}}"
                                        style="margin-left: 100px; border-radius: 50%"><i
                                            class="fa fa-times"></i></button>
                                    <img src="@if(!empty($raw_material->getFirstMediaUrl('product'))){{$raw_material->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
                                        alt="photo" style="width: 120px;">
                                </div>
                                @endif
                            </div>

                            <div class="col-md-12 " style="margin-top: 10px;">
                                <div class="dropzone" id="my-dropzone">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('lang.product_details')</label>
                                    <textarea name="product_details" id="product_details" class="form-control"
                                        rows="3">{{!empty($raw_material) ? $raw_material->product_details : ''}}</textarea>
                                </div>
                            </div>

                            <div class="col-md-4 hide">
                                <div class="form-group">
                                    {!! Form::label('barcode_type', __('lang.barcode_type'), []) !!}
                                    {!! Form::select('barcode_type', ['C128' => 'Code 128' , 'C39' => 'Code 39', 'UPCA'
                                    => 'UPC-A', 'UPCE' => 'UPC-E', 'EAN8' => 'EAN-8', 'EAN13' => 'EAN-13'],
                                    !empty($raw_material) ?
                                    $raw_material->barcode_type : false,
                                    ['class' => 'form-control', 'required']) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('alert_quantity', __('lang.alert_quantity'), []) !!}
                                            {!! Form::text('alert_quantity', !empty($raw_material) ?
                                            @num_format($raw_material->alert_quantity)
                                            : 3,
                                            ['class' => 'form-control', 'placeholder' =>
                                            __('lang.alert_quantity')]) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="" class="unit_label" style="margin-top: 37px;">{{$raw_material->alert_quantity_unit->name??''}}</label>
                                    </div>
                                    <div class="col-md-6 hide">
                                        <div class="form-group">
                                            {!! Form::label('alert_quantity_unit_id', __('lang.unit'), []) !!}
                                            {!! Form::select('alert_quantity_unit_id', $units,
                                            !empty($raw_material) ? $raw_material->alert_quantity_unit_id : false,
                                            ['class' => 'selectpicker
                                            form-control',
                                            'data-live-search'=>"true",
                                            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @can('product_module.purchase_price.create_and_edit')
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('purchase_price', __('lang.cost') . ' *', []) !!}
                                    {!! Form::text('purchase_price', !empty($raw_material) ?
                                    @num_format($raw_material->purchase_price) :
                                    null, ['class' => 'form-control', 'placeholder' =>
                                    session('system_mode') == 'pos' || session('system_mode') == 'garments' ||
                                    session('system_mode') ==
                                    'supermarket' ? __('lang.purchase_price') : __('lang.cost'), 'required']) !!}
                                </div>
                            </div>
                            @endcan

                            <div class="col-md-12 hide">
                                <table class="table table-bordered" id="consumption_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 30%;">@lang('lang.used_in')</th>
                                            <th style="width: 30%;">@lang('lang.used_amount')</th>
                                            <th style="width: 30%;">@lang('lang.unit')</th>
                                            <th style="width: 10%;"><button
                                                    class="btn btn-xs btn-success add_product_row" type="button"><i
                                                        class="fa fa-plus"></i></button></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($raw_material->consumption_products as $consumption_product)
                                        @include('raw_material.partial.product_row', ['row_id' => $loop->index,
                                        'consumption_product' => $consumption_product])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <input type="hidden" name="is_raw_material" id="is_raw_material" value="1">
                            <input type="hidden" name="row_id" id="row_id"
                                value="{{$raw_material->consumption_products->count()}}">
                        </div>

                        <div class="col-md-12 hide">
                            <table class="table" id="variation_table">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.name')</th>
                                        <th>@lang('lang.sku')</th>
                                        <th>@lang('lang.color')</th>
                                        <th>@lang('lang.size')</th>
                                        <th>@lang('lang.grade')</th>
                                        <th>@lang('lang.unit')</th>
                                        <th>@lang('lang.purchase_price')</th>
                                        <th>@lang('lang.sell_price')</th>
                                        <th><button type="button" class="btn btn-success btn-xs add_row mt-2"><i
                                                    class="dripicons-plus"></i></button></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($raw_material->variations as $item_v)
                                    @include('product.partial.edit_variation_row', ['row_id' => $loop->index, 'item' =>
                                    $item_v])

                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <input type="hidden" name="active" value="1">
                        <div class="row">
                            <div class="col-md-4 mt-5">
                                <div class="form-group">
                                    <input type="button" value="{{trans('lang.submit')}}" id="submit-btn"
                                        class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="product_cropper_modal" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('lang.crop_image_before_upload')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <div class="row">
                        <div class="col-md-8">
                            <img src="" id="product_sample_image" />
                        </div>
                        <div class="col-md-4">
                            <div class="product_preview_div"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="product_crop" class="btn btn-primary">@lang('lang.crop')</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script src="{{asset('js/product_edit.js')}}"></script>
<script src="{{asset('js/raw_material.js')}}"></script>
<script type="text/javascript">

</script>
@endsection
