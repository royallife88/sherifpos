<div class="modal-dialog" role="document">
    <div class="modal-content">

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.transfer' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    {!! Form::label('reference', __('lang.reference'), []) !!}:
                    <b>{{$transfer->invoice_no}}</b>
                </div>
                <div class="col-md-6">
                    {!! Form::label('date', __('lang.date'), []) !!}:
                    <b>{{@format_date($transfer->transaction_date)}}</b>
                </div>
                <div class="col-md-6">
                    {!! Form::label('sender_store', __('lang.sender_store'), []) !!}:
                    <b>{{$transfer->sender_store->name}}</b>
                </div>

                <div class="col-md-6">
                    {!! Form::label('receiver_store', __('lang.receiver_store'), []) !!}:
                    <b>{{$transfer->receiver_store->name}}</b>
                </div>
                <div class="col-md-6">
                    {!! Form::label('approved', __('lang.approved'), []) !!}:
                    <b>@if(!empty($transfer->approved_at)) {{@format_date($transfer->approved_at)}} @endif -
                        {{$transfer->approved_by_user->name}}</b>
                </div>

                <div class="col-md-6">
                    {!! Form::label('receiver_store', __('lang.received'), []) !!}:
                    <b>@if(!empty($transfer->received_at)) {{@format_date($transfer->received_at)}} @endif -
                        {{$transfer->received_by_user->name}}</b>
                </div>

            </div>
            <br>
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
                            @foreach ($transfer->transfer_lines as $line)
                            <tr>
                                <td>
                                    {{$line->product->name}}
                                    @if(!empty($line->variation))
                                    @if($line->variation->name != "Default")
                                    <b>{{$line->variation->name}}</b>
                                    @endif
                                    @endif

                                </td>
                                <td>
                                    {{$line->variation->sub_sku}}
                                </td>
                                <td>
                                    @if(isset($line->quantity)){{@num_format($line->quantity)}}@else{{1}}@endif
                                </td>
                                <td>
                                    @if(isset($line->purchase_price)){{@num_format($line->purchase_price)}}@else{{0}}@endif
                                </td>
                                <td>
                                    {{@num_format($line->sub_total)}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-12">
                <div class="col-md-3 offset-md-8 text-right">
                    <h3> @lang('lang.total'): <span
                            class="final_total_span">{{@num_format($transfer->final_total)}}</span>
                    </h3>

                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('notes', __('lang.notes'), []) !!}: <br>
                        {{$transfer->notes}}

                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
