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
                        {!! Form::open(['url' => '#', 'method' => 'post', 'id' => 'preview_setting_form', 'onsubmit' => 'return false']) !!}
                        <div class="card-body">
                            <input type="hidden" name="is_add_stock" id="is_add_stock" value="1">
                            <input type="hidden" name="row_count" id="row_count" value="0">
                            <div class="row">
                                <div class="col-md-2"></div>
                                <div class="col-md-6">
                                    <div class="search-box input-group">
                                        <button type="button" class="btn btn-secondary btn-lg"><i
                                                class="fa fa-search"></i></button>
                                        <input type="text" name="search_product" id="search_product_for_label"
                                            placeholder="@lang('lang.enter_product_name_to_print_labels')" class="form-control ui-autocomplete-input"
                                            autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    @include('quotation.partial.product_selection')
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-8 offset-md-2">
                                    <table class="table table-bordered table-striped table-condensed" id="product_table">
                                        <thead>
                                            <tr>
                                                <th style="width: 33%" class="col-sm-8">@lang('lang.products')</th>
                                                <th style="width: 33%" class="col-sm-4">@lang('lang.sku')</th>
                                                <th style="width: 33%" class="col-sm-4">@lang('lang.no_of_labels')</th>
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
                                            <input id="size" name="size" type="checkbox" checked value="1"
                                                class="form-control-custom">
                                            <label for="size"><strong>@lang('lang.size')</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="i-checks">
                                            <input id="color" name="color" type="checkbox" checked value="1"
                                                class="form-control-custom">
                                            <label for="color"><strong>@lang('lang.color')</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="i-checks">
                                            <input id="grade" name="grade" type="checkbox" checked value="1"
                                                class="form-control-custom">
                                            <label for="grade"><strong>@lang('lang.grade')</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="i-checks">
                                            <input id="unit" name="unit" type="checkbox" checked value="1"
                                                class="form-control-custom">
                                            <label for="unit"><strong>@lang('lang.unit')</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="i-checks">
                                            <input id="size_variations" name="size_variations" type="checkbox" checked value="1"
                                                class="form-control-custom">
                                            <label for="size_variations"><strong>@lang('lang.size') @lang('lang.variations')</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="i-checks">
                                            <input id="color_variations" name="color_variations" type="checkbox" checked value="1"
                                                class="form-control-custom">
                                            <label for="color_variations"><strong>@lang('lang.color') @lang('lang.variations')</strong></label>
                                        </div>
                                    </div>
                                    @foreach ($stores as $key => $store)
                                        <div class="col-md-4">
                                            <div class="i-checks">
                                                <input id="store{{ $key }}" name="store[{{ $key }}]" type="checkbox" value="{{ $key }}" @if($loop->index == 0 ) checked @endif
                                                    class="form-control-custom">
                                                <label for="store{{ $key }}"><strong>{{ $store }}</strong></label>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-md-4">
                                        <div class="i-checks">
                                            <input id="site_title" name="site_title" type="checkbox" checked value="1"
                                                class="form-control-custom">
                                            <label
                                                for="site_title"><strong>{{ App\Models\System::getProperty('site_title') }}</strong></label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">@lang('lang.text')</label>
                                            <input class="form-control" type="text" name="free_text" id="free_text"
                                                value="">
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
                                class="btn btn-primary pull-right btn-flat">@lang('lang.submit')</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    <script src="{{ asset('js/barcode.js') }}"></script>
    <script src="{{ asset('js/product_selection.js') }}"></script>
    <script type="text/javascript">
        $(document).on('click', '#add-selected-btn', function() {
            $('#select_products_modal').modal('hide');
            $.each(product_selected, function(index, value) {
                get_label_product_row(value.product_id, value.variation_id);
            });
            product_selected = [];
            product_table.ajax.reload();
        });

        $(document).on('click', '.remove_row', function() {
            $(this).closest('tr').remove();
        });
    </script>
@endsection
