@extends('layouts.app')
@section('title', __('lang.print_barcode'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.print_barcode')</h4>
                    </div>
                    {!! Form::open(['url' => '#', 'method' => 'post', 'id' => 'preview_setting_form', 'onsubmit' =>
                    'return false']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-6">
                                <div class="search-box input-group">
                                    <button type="button" class="btn btn-secondary btn-lg"><i
                                            class="fa fa-search"></i></button>
                                    <input type="text" name="search_product" id="search_product_for_label"
                                        placeholder="@lang('lang.enter_product_name_to_print_labels')"
                                        class="form-control ui-autocomplete-input" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <table class="table table-bordered table-striped table-condensed" id="product_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 33%" class="col-sm-8">@lang( 'lang.products' )</th>
                                            <th style="width: 33%" class="col-sm-4">@lang( 'lang.sku' )</th>
                                            <th style="width: 33%" class="col-sm-4">@lang( 'lang.no_of_labels' )</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="i-checks">
                                        <input id="product_name" name="product_name" type="checkbox" checked value="1"
                                            class="form-control-custom">
                                        <label for="product_name"><strong>@lang('lang.product_name')</strong></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="i-checks">
                                        <input id="price" name="price" type="checkbox" checked value="1"
                                            class="form-control-custom">
                                        <label for="price"><strong>@lang('lang.price')</strong></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="i-checks">
                                        <input id="variations" name="variations" type="checkbox" checked value="1"
                                            class="form-control-custom">
                                        <label for="variations"><strong>@lang('lang.variations')</strong></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <br>

                        <div class="col-md-12">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">@lang('lang.paper_size'):</label>
                                    <select class="form-control" name="paper_size" required id="paper-size"
                                        tabindex="-98">
                                        <option value="0">Select paper size...</option>
                                        <option value="36">36 mm (1.4 inch)</option>
                                        <option value="24">24 mm (0.94 inch)</option>
                                        <option value="18">18 mm (0.7 inch)</option>
                                    </select>
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="col-sm-12">
                        <button type="button" id="labels_preview" style="margin: 10px"
                            class="btn btn-primary pull-right btn-flat">@lang( 'lang.submit' )</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script src="{{asset('js/barcode.js')}}"></script>
<script type="text/javascript">

</script>
@endsection
