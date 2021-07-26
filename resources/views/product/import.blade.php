@extends('layouts.app')
@section('title', __('lang.import_products'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.import_products')</h4>
                </div>
                <div class="card-body">
                    {!! Form::open(['url' => action('ProductController@saveImport'), 'method' => 'post', 'files' =>
                    true, 'class' => 'pos-form', 'id' => 'import_sale_form']) !!}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('file', __('lang.file'), []) !!} <br>
                                            {!! Form::file('file', []) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <a class="btn btn-block btn-primary"
                                            href="{{asset('sample_files/product_import.csv')}}"><i
                                                class="fa fa-download"></i>@lang('lang.download_sample_file')</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-12 ml-3">
                            <br>
                            <button type="submit" class="btn btn-primary">@lang('lang.import')</button>
                        </div>

                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- This will be printed -->
<section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
<script src="{{asset('js/pos.js')}}"></script>
<script>

</script>
@endsection
