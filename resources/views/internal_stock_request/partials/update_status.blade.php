<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('InternalStockRequestController@postUpdateStatus', $transaction->id), 'method'
        => 'post', 'id' =>
        'update_status_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.update_status' ) ({{$transaction->invoice_no}})</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('status', __('lang.receiver_store'), []) !!}: {{$transaction->receiver_store->name}}
                </div>
                <div class="col-md-6">
                    {!! Form::label('status', __('lang.sender_store'), []) !!}: {{$transaction->sender_store->name}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! Form::label('status', __('lang.status'). ':*', []) !!}
                    {!! Form::select('status', ['received' => __('lang.received'), 'approved' =>
                    __('lang.approved'), 'pending' => __('lang.pending'), 'declined' => __('lang.declined')],
                    $transaction->status, ['class' => 'selectpicker form-control',
                    'data-live-search'=>"true", 'required',
                    'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped table-condensed" id="product_table">
                        <thead>
                            <tr>
                                <th style="width: 25%" class="col-sm-8">@lang( 'lang.products' )</th>
                                <th style="width: 25%" class="col-sm-4">@lang( 'lang.sku' )</th>
                                <th style="width: 25%" class="col-sm-4">@lang( 'lang.quantity' )</th>
                                <th style="width: 12%" class="col-sm-4">@lang( 'lang.purchase_price' )</th>
                                <th style="width: 12%" class="col-sm-4">@lang( 'lang.sub_total' )</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->transfer_lines as $product)
                            <tr>
                                <td>
                                    {{$product->product->name}}

                                    @if($product->variation->name != "Default")
                                    <b>{{$product->variation->name}}</b>
                                    @endif
                                    <input type="hidden" name="transfer_lines[{{$loop->index}}][transfer_line_id]"
                                        value="{{$product->id}}">
                                    <input type="hidden" name="transfer_lines[{{$loop->index}}][product_id]"
                                        value="{{$product->product_id}}">
                                    <input type="hidden" name="transfer_lines[{{$loop->index}}][variation_id]"
                                        value="{{$product->variation_id}}">
                                </td>
                                <td>
                                    {{$product->variation->sub_sku}}
                                </td>
                                <td>
                                    <input type="text" class="form-control quantity" min=1
                                        max="{{$product->quantity}}"
                                        name="transfer_lines[{{$loop->index}}][quantity]" required
                                        value="@if(isset($product->quantity)){{$product->quantity}}@else{{1}}@endif">
                                </td>
                                <td>
                                    <input type="text" class="form-control purchase_price"
                                        name="transfer_lines[{{$loop->index}}][purchase_price]" required
                                        value="@if(isset($product->purchase_price)){{@num_format($product->purchase_price)}}@else{{0}}@endif">
                                </td>
                                <td>
                                    <span class="sub_total_span">{{@num_format($product->sub_total)}}</span>
                                    <input type="hidden" class="form-control sub_total"
                                        name="transfer_lines[{{$loop->index}}][sub_total]"
                                        value="{{$product->sub_total}}">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="text-right">@lang( 'lang.total' )</td>
                                <td>
                                    <span class="total_span">{{@num_format($transaction->final_total)}}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="update-status">@lang( 'lang.update' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker()
</script>
