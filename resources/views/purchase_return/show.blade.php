<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang('lang.return_purchase')</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="col-md-12">
                        @lang('lang.invoice_no'):<b> {{ $purchase_return->invoice_no }}</b>
                    </div>
                    <div class="col-md-12">
                        @lang('lang.date'): <b>{{ @format_date($purchase_return->transaction_date) }}</b>
                    </div>
                    <div class="col-md-12">
                        @lang('lang.store'): <b>{{ $purchase_return->store->name ?? '' }}</b>
                    </div>
                </div>
                <br>
                <div class="col-md-6">
                    <div class="col-md-12">
                        {!! Form::label('supplier_name', __('lang.supplier_name'), []) !!}:
                        <b>{{ $purchase_return->supplier->name }}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('email', __('lang.email'), []) !!}: <b>{{ $purchase_return->supplier->email }}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('mobile_number', __('lang.mobile_number'), []) !!}:
                        <b>{{ $purchase_return->supplier->mobile_number }}</b>
                    </div>
                    <div class="col-md-12">
                        {!! Form::label('address', __('lang.address'), []) !!}: <b>{{ $purchase_return->supplier->address }}</b>
                    </div>
                </div>
            </div>

            <br>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped table-condensed" id="product_table">
                        <thead class="bg-success">
                            <tr style="color: white">
                                <th style="width: 25%" class="col-sm-8">@lang('lang.products')</th>
                                <th style="width: 25%" class="col-sm-4">@lang('lang.sku')</th>
                                <th style="width: 25%" class="col-sm-4">@lang('lang.quantity')</th>
                                <th style="width: 12%" class="col-sm-4">@lang('lang.purchase_price')</th>
                                <th class="sum" style="width: 12%" class="col-sm-4">@lang('lang.sub_total')
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($purchase_return->purchase_return_lines as $line)
                                @if ($line->quantity == 0)
                                    @continue
                                @endif

                                <tr>
                                    <td>
                                        {{ $line->product->name }}
                                        @if (!empty($line->variation))
                                            @if ($line->variation->name != 'Default')
                                                <b>{{ $line->variation->name }}</b>
                                            @endif
                                        @endif

                                    </td>
                                    <td>
                                        @if (!empty($line->variation))
                                            @if ($line->variation->name != 'Default')
                                                {{ $line->variation->sub_sku }}
                                            @else
                                                {{ $line->product->sku ?? '' }}
                                            @endif
                                        @else
                                            {{ $line->product->sku ?? '' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($line->quantity))
                                            {{ @num_format($line->quantity) }}@else{{ 0 }}
                                        @endif
                                    </td>
                                    <td>
                                        @if (isset($line->purchase_price))
                                            {{ @num_format($line->purchase_price) }}@else{{ 0 }}
                                        @endif
                                    </td>
                                    <td>
                                        {{ @num_format($line->purchase_price * $line->quantity) }}
                                    </td>
                                </tr>
                                @php
                                    $total += $line->purchase_price * $line->quantity;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th style="text-align: right"> @lang('lang.total')</th>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <br>
            <br>
            @include('transaction_payment.partials.payment_table', [
                'payments' => $purchase_return->transaction_payments,
            ])

            <br>
            <br>
            <div class="row">
                <div class="col-md-6">
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th>@lang('lang.grand_total'):</th>
                            <td>{{ @num_format($purchase_return->final_total) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang.paid_amount'):</th>
                            <td>{{ @num_format($purchase_return->transaction_payments->sum('amount')) }}</td>
                        </tr>
                        <tr>
                            <th>@lang('lang.due'):</th>
                            <td> {{ @num_format($purchase_return->final_total - $purchase_return->transaction_payments->sum('amount')) }}
                            </td>
                        </tr>
                    </table>
                </div>

            </div>

        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lang.close')</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
