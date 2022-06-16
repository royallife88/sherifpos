<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CashController@saveAddClosingCash'), 'method' => 'post', 'id' => 'add_closing_cash_form', 'files' => true]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang('lang.add_closing_cash')</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="well">
                    <table class="table">
                        <tr>
                            <td><b>@lang('lang.date_and_time')</b></td>
                            <td>{{ @format_datetime($cash_register->created_at) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.cash_in')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_cash_in) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.cash_out')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_cash_out) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_card_sale')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_card_sales) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_cheque_sale')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_cheque_sales) }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_bank_transfer_sale')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_bank_transfer_sales) }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_gift_card_sale')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_gift_card_sales) }}
                                </td>
                            @endforeach
                        </tr>
                        @if (session('system_mode') == 'restaurant')
                            <tr>
                                <td><b>@lang('lang.dining_in')</b></td>
                                @foreach ($cr_data as $data)
                                    <td>{{ $data['currency']['symbol'] }}
                                        {{ @num_format($data['cash_register']->total_dining_in) }}
                                    </td>
                                @endforeach
                            </tr>
                        @endif
                        <tr>
                            <td><b>@lang('lang.other_cash_sales')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_cash_sales - $data['cash_register']->total_dining_in_cash) }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_cash_sale')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_cash_sales) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_sales')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_sale - $data['cash_register']->total_refund) }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.return_sales')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_sell_return) }}
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.purchases')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_purchases) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.expenses')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_expenses) }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><b>@lang('lang.wages_and_compensation')</b></td>
                            @foreach ($cr_data as $data)
                                <td>{{ $data['currency']['symbol'] }}
                                    {{ @num_format($data['cash_register']->total_wages_and_compensation) }}</td>
                            @endforeach
                        </tr>
                        {{-- <tr>
                            <td><b>@lang('lang.current_cash')</b></td>
                            @foreach ($cr_data as $data)
                                <td>
                                    @php
                                        $current_cash = $data['cash_register']->total_cash_sales + $data['cash_register']->total_cash_in - $data['cash_register']->total_cash_out - $data['cash_register']->total_purchases - $data['cash_register']->total_expenses - $data['cash_register']->total_wages_and_compensation - $data['cash_register']->total_sell_return;
                                    @endphp
                                    <h6 class="currency_total_row_td currency_total currency_total_{{ $data['currency']['currency_id'] }}"
                                        data-currency_id="{{ $data['currency']['currency_id'] }}"
                                        data-is_default="{{ $data['currency']['is_default'] }}"
                                        data-conversion_rate="{{ $data['currency']['conversion_rate'] }}"
                                        data-base_conversion="{{ $data['currency']['conversion_rate'] * $current_cash }}"
                                        data-orig_value="{{ $current_cash }}">
                                        <span class="symbol" style="padding-right: 10px;">
                                            {{ $data['currency']['symbol'] }}</span>
                                        <span class="total">{{ @num_format($current_cash) }}</span>
                                    </h6>
                                </td>
                            @endforeach
                        </tr> --}}
                    </table>
                </div>
                <div class="col-md-12">
                    <button type="button" id="print-closing-cash-btn" data-cash_register_id="{{ $cash_register_id }}"
                        class="btn btn-primary pull-right">@lang('lang.print')</button>
                </div>
            </div>
            <br>
            <br>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('current_cash', __('lang.current_cash') . ':*') !!}
                            {!! Form::text('current_cash', @num_format($total_cash), ['class' => 'form-control', 'placeholder' => __('lang.current_cash'), 'readonly', 'id' => 'closing_current_cash']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('amount', __('lang.amount') . ':*') !!}
                            {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => __('lang.amount'), 'required', 'id' => 'closing_amount']) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('source_type', __('lang.source_type'), []) !!} <br>
                            {!! Form::select('source_type', ['user' => __('lang.user'), 'safe' => __('lang.safe')], 'user', ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('cash_given_to', __('lang.cash_given_to') . ':*') !!}
                            {!! Form::select('cash_given_to', $users, false, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('discrepancy', __('lang.discrepancy') . ':*') !!}
                            {!! Form::text('discrepancy', 0, ['class' => 'form-control', 'placeholder' => __('lang.discrepancy'), 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('notes', __('lang.notes'), []) !!}
                            {!! Form::textarea('notes', null, ['class' => 'form-control', 'placeholder' => __('lang.notes'), 'rows' => 3]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="cash_register_id" value="{{ $cash_register_id }}">
        </div>

        <div class="modal-footer">
            <button type="submit" name="submit" class="btn btn-primary hide" value="adjustment"
                id="adjust-btn">@lang('lang.adjustment')</button>
            <button type="submit" name="submit" class="btn btn-primary" value="save"
                id="closing-save-btn">@lang('lang.save')</button>
            <button type="button"
                class="btn btn-default @if ($type == 'logout') close-btn-add-closing-cash @endif"
                @if ($type != 'logout') data-dismiss="modal" @endif>@lang('lang.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker('render');
    $(document).on('change', '#closing_amount', function() {
        let amount = __read_number($(this));
        let current_cash = __read_number($('#closing_current_cash'));

        let discrepancy = amount - current_cash;

        $('#discrepancy').val(discrepancy);

        if (discrepancy !== 0) {
            $('#adjust-btn').removeClass('hide');
        } else {
            $('#adjust-btn').addClass('hide');
        }

    });
    $('#source_type').change();
    $(document).on('change', '#source_type', function() {
        if ($(this).val() !== '') {
            $.ajax({
                method: 'get',
                url: '/add-stock/get-source-by-type-dropdown/' + $(this).val(),
                data: {},
                success: function(result) {
                    $("#cash_given_to").empty().append(result);
                    $("#cash_given_to").selectpicker("refresh");
                },
            });
        }
    });
</script>
