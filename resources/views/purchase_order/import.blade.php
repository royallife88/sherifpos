@extends('layouts.app')
@section('title', __('lang.purchase_order'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.import_purchase_order')</h4>
                    </div>
                    {!! Form::open(['url' => action('PurchaseOrderController@saveImport'), 'method' => 'post', 'id' =>
                    'import_purchase_order_form', 'files' => true]) !!}
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
                                    {!! Form::label('po_no', __('lang.po_no'). ':*', []) !!}
                                    {!! Form::text('po_no', null, ['class' => 'form-control','required', 'readonly',
                                    'placeholder' => __('lang.po_no')]) !!}
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        {!! Form::label('file', __('lang.file'), []) !!} <br>
                                        {!! Form::file('file', []) !!}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <a class="btn btn-block btn-primary" href="{{asset('sample_files/purchase_order_import.csv')}}"><i class="fa fa-download"></i>@lang('lang.download_sample_file')</a>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('details', __('lang.details'), []) !!}
                                    {!! Form::textarea('details', null, ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>
                        </div>


                    </div>

                    <div class="col-sm-12">
                        <button type="submit" name="submit" id="print" style="margin: 10px" value="print"
                            class="btn btn-primary pull-right btn-flat submit">@lang( 'lang.print' )</button>
                        @can('purchase_order.send_to_supplier.create_and_edit')
                        <button type="button" id="send_to_supplier" style="margin: 10px" disabled
                            class="btn btn-warning pull-right btn-flat submit" data-toggle="modal"
                            data-target="#supplier_modal">@lang(
                            'lang.send_to_supplier' )</button>
                        @endcan
                        @can('purchase_order.send_to_admin.create_and_edit')
                        <button type="submit" name="submit" id="send_to_admin" style="margin: 10px"
                            value="sent_admin" class="btn btn-primary pull-right btn-flat submit">@lang(
                            'lang.send_to_admin' )</button>
                        @endcan
                        <div class="modal fade supplier_modal" id="supplier_modal" role="dialog" aria-hidden="true">
                        </div>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>


</section>
@endsection

@section('javascript')
<script src="{{asset('js/purchase.js')}}"></script>
<script type="text/javascript">
    $('#store_id').change(function () {
        let store_id = $(this).val();

        $.ajax({
            method: 'get',
            url: '/purchase-order/get-po-number',
            data: { store_id },
            success: function(result) {
                $('#po_no').val(result);
            },
        });
    })

</script>
@endsection
