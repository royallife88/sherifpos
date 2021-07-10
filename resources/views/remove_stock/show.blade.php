<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.remove_stock' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="card-header d-flex align-items-center no-print">
                <h4>@lang('lang.invoice_no'): {{$remove_stock->invoice_no}}</h4>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        {!! Form::label('supplier_name', __('lang.supplier_name'), []) !!}:
                        <b>{{$supplier->name}}</b>
                    </div>
                    <div class="col-md-4">
                        {!! Form::label('email', __('lang.email'), []) !!}: <b>{{$supplier->email}}</b>
                    </div>
                    <div class="col-md-4">
                        {!! Form::label('mobile_number', __('lang.mobile_number'), []) !!}:
                        <b>{{$supplier->mobile_number}}</b>
                    </div>
                    <div class="col-md-4">
                        {!! Form::label('address', __('lang.address'), []) !!}: <b>{{$supplier->address}}</b>
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
                                    <th style="width: 25%" class="col-sm-4">@lang( 'lang.removed_quantity' )</th>
                                    <th style="width: 12%" class="col-sm-4">@lang( 'lang.purchase_price' )</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($remove_stock->remove_stock_lines as $line)
                                <tr>
                                    <td>
                                        {{$line->product->name}}

                                        @if($line->variation->name != "Default")
                                        <b>{{$line->variation->name}}</b>
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
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('notes', __('lang.notes'), []) !!}: <br>
                            {{$remove_stock->notes}}

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
