@extends('layouts.app')
@section('title', __('lang.product'))

@section('content')
    <section class="forms">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h4>@lang('lang.add_new_product')</h4>
                        </div>
                        <div class="card-body">
                            <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                            {!! Form::open(['url' => action('ProductController@store'), 'id' => 'product-form', 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                            @include('product.partial.create_product_form')
                            <div class="row">
                                <div class="col-md-4 mt-5">
                                    <div class="form-group">
                                        <input type="button" value="{{ trans('lang.save') }}" id="submit-btn"
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
    <script src="{{ asset('js/product.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#discount_customer_types').selectpicker('selectAll');
            $('#category_id').change();

            if($('#is_service').prop('checked')){
                $('.supplier_div').removeClass('hide');
            }else{
                $('.supplier_div').addClass('hide');
            }
        });
    </script>
@endsection
