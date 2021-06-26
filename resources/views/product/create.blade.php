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
                        {!! Form::open(['url' => action('ProductController@store'), 'id' => 'product-form', 'method' =>
                        'POST', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                        @include('product.partial.create_product_form')
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
@endsection

@section('javascript')
<script src="{{asset('js/product.js')}}"></script>
<script type="text/javascript">

</script>
@endsection
