@extends('layouts.app')
@section('title', __('lang.purchase_order'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.purchase_order')</h4>
                    </div>
                    {!! Form::open(['url' => action('PurchaseOrderController@update', $purchase_order->id), 'method' =>
                    'put', 'id' =>
                    'purchase_order_form']) !!}
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'). ':*', []) !!}
                                    {!! Form::select('store_id', $stores,
                                    $purchase_order->store_id, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true",
                                    'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('supplier_id', __('lang.supplier'). ':*', []) !!}
                                    {!! Form::select('supplier_id', $suppliers,
                                    $purchase_order->supplier_id, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('status', __('lang.status'). ':*', []) !!}
                                    {!! Form::select('status', $status_array,
                                    $purchase_order->status, ['class' => 'selectpicker form-control',
                                    'data-live-search'=>"true", 'required',
                                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('po_no', __('lang.po_no'). ':*', []) !!}
                                    {!! Form::text('po_no', $purchase_order->po_no, ['class' =>
                                    'form-control','required', 'readonly',
                                    'placeholder' => __('lang.po_no')]) !!}
                                </div>
                            </div>

                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <div class="search-box input-group">
                                    <button type="button" class="btn btn-secondary btn-lg"><i
                                            class="fa fa-search"></i></button>
                                    <input type="text" name="search_product" id="search_product"
                                        placeholder="@lang('lang.enter_product_name_to_print_labels')"
                                        class="form-control ui-autocomplete-input" autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-10 offset-md-1">
                                <table class="table table-bordered table-striped table-condensed" id="product_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%" class="col-sm-8">@lang( 'lang.products' )</th>
                                            @if(session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                                            <th style="width: 25%" class="col-sm-4">@lang( 'lang.sku' )</th>
                                            @endif
                                            <th style="width: 25%" class="col-sm-4">@lang( 'lang.quantity' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.purchase_price' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.sub_total' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.action' )</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($purchase_order->purchase_order_lines as $product)
                                        <tr>
                                            <td>
                                                {{$product->product->name}}

                                                @if($product->variation->name != "Default")
                                                <b>{{$product->variation->name}}</b>
                                                @endif
                                                <input type="hidden"
                                                    name="purchase_order_lines[{{$loop->index}}][purchase_order_line_id]"
                                                    value="{{$product->id}}">
                                                <input type="hidden"
                                                    name="purchase_order_lines[{{$loop->index}}][product_id]"
                                                    value="{{$product->product_id}}">
                                                <input type="hidden"
                                                    name="purchase_order_lines[{{$loop->index}}][variation_id]"
                                                    value="{{$product->variation_id}}">
                                            </td>
                                            @if(session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                                            <td>
                                                {{$product->variation->sub_sku}}
                                            </td>
                                            @endif
                                            <td>
                                                <input type="text" class="form-control quantity" min=1
                                                    name="purchase_order_lines[{{$loop->index}}][quantity]" required
                                                    value="@if(isset($product->quantity)){{$product->quantity}}@else{{1}}@endif">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control purchase_price"
                                                    name="purchase_order_lines[{{$loop->index}}][purchase_price]"
                                                    required
                                                    value="@if(isset($product->purchase_price)){{@num_format($product->purchase_price)}}@else{{0}}@endif">
                                            </td>
                                            <td>
                                                <span class="sub_total_span">{{@num_format($product->sub_total)}}</span>
                                                <input type="hidden" class="form-control sub_total"
                                                    name="purchase_order_lines[{{$loop->index}}][sub_total]"
                                                    value="{{$product->sub_total}}">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sx remove_row"><i
                                                        class="fa fa-times"></i></button>
                                            </td>
                                        </tr>
                                        @empty

                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <br>
                        <div class="col-md-12">
                            <div class="col-md-3 offset-md-8 text-right">
                                <h3> @lang('lang.total'): <span
                                        class="final_total_span">{{@num_format($purchase_order->final_total)}}</span>
                                </h3>
                                <input type="hidden" name="final_total" id="final_total"
                                    value="{{$purchase_order->final_total}}">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('details', __('lang.details'), []) !!}
                                    {!! Form::textarea('details', $purchase_order->details, ['class' => 'form-control',
                                    'rows' => 3]) !!}
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
                        <button type="submit" name="submit" id="send_to_admin" style="margin: 10px" value="sent_admin"
                            class="btn btn-primary pull-right btn-flat submit">@lang(
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
    $(document).ready(function(){
        $('#supplier_id').change();
    })

    $('#supplier_id').change(function () {
        let supplier_id = $(this).val();

        if(supplier_id){
            $.ajax({
                method: 'get',
                url: '/supplier/get-details/'+supplier_id+'?is_purchase_order=1',
                data: {  },
                contentType: 'html',
                success: function(result) {
                    $('.supplier_modal').empty().append(result);
                    $('#send_to_supplier').attr('disabled', false);
                },
            });
        }
    })
</script>
@endsection
