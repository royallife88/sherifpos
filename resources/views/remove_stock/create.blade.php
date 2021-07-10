@extends('layouts.app')
@section('title', __('lang.remove_stock'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.remove_stock')</h4>
                    </div>
                    {!! Form::open(['url' => action('RemoveStockController@store'), 'method' => 'post', 'id' =>
                    'remove_stock_form', 'enctype' => 'multipart/form-data' ]) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'). ':*', []) !!}
                                    {!! Form::select('store_id', $stores,
                                    null, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                                    'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('supplier_id', __('lang.supplier'). ':*', []) !!}
                                    {!! Form::select('supplier_id', $suppliers,
                                    null, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('invoice_id', __('lang.invoice_no'). ':*', []) !!} <i class="dripicons-question"
                                        data-toggle="tooltip" title="@lang('lang.invoice_no_remove_stock_info')"></i>
                                    {!! Form::select('invoice_id', $invoice_nos,
                                    null, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-condensed" id="product_table">
                                    <thead>
                                        <tr>
                                            <th>@lang( 'lang.image' )</th>
                                            <th>@lang( 'lang.products' )</th>
                                            <th>@lang( 'lang.sku' )</th>
                                            <th>@lang( 'lang.class' )</th>
                                            <th>@lang( 'lang.category' )</th>
                                            <th>@lang( 'lang.sub_category' )</th>
                                            <th>@lang( 'lang.color' )</th>
                                            <th>@lang( 'lang.size' )</th>
                                            <th>@lang( 'lang.grade' )</th>
                                            <th>@lang( 'lang.unit' )</th>
                                            <th>@lang( 'lang.quantity' )</th>
                                            <th>@lang( 'lang.remove_quantity' )</th>
                                            <th>@lang( 'lang.purchase_price' )</th>
                                            <th>@lang( 'lang.sell_price' )</th>
                                            <th>@lang( 'lang.action' )</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <input type="hidden" name="final_total" id="final_total" value="0">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('files', __('lang.files'), []) !!} <br>
                                    {!! Form::file('files[]', null, ['class' => '']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('payment_status', __('lang.payment_status'), []) !!}: <span class="payment_status_span"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('reason', __('lang.reason'). ':', []) !!} <br>
                                    {!! Form::textarea('reason', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('notes', __('lang.notes'). ':', []) !!} <br>
                                    {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>

                        </div>


                    </div>

                    <div class="col-sm-12">
                        <button type="submit" name="submit" id="print" style="margin: 10px" value="save"
                            class="btn btn-primary pull-right btn-flat submit">@lang( 'lang.save' )</button>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


</section>
@endsection

@section('javascript')
<script src="{{asset('js/add_stock.js')}}"></script>
<script type="text/javascript">
    $('#invoice_id').change(function () {
        let invoice_id = $(this).val();

        if(invoice_id){
            $.ajax({
                method: 'get',
                url: '/remove-stock/get-invoice-details/'+invoice_id,
                data: {  },
                success: function(result) {
                    $("table#product_table tbody").empty().append(result.html);
                    $('.payment_status_span').text(result.payment_status);
                    calculate_sub_totals();
                },
            });
        }
    });
</script>
@endsection
