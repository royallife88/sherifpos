<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CashController@saveAddClosingCash'), 'method' => 'post', 'id' => 'add_closing_cash_form', 'files' => true]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_closing_cash' )</h4>
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
                            <td>{{ @num_format($cash_register->total_cash_in) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.cash_out')</b></td>
                            <td>{{ @num_format($cash_register->total_cash_out) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_sales')</b></td>
                            <td>{{ @num_format($cash_register->total_sale) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_cash_sale')</b></td>
                            <td>{{ @num_format($cash_register->total_cash_sales - $cash_register->total_refund_cash) }}
                            </td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_card_sale')</b></td>
                            <td>{{ @num_format($cash_register->total_card_sales) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_cheque_sale')</b></td>
                            <td>{{ @num_format($cash_register->total_cheque_sales) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_bank_transfer_sale')</b></td>
                            <td>{{ @num_format($cash_register->total_bank_transfer_sales) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.total_gift_card_sale')</b></td>
                            <td>{{ @num_format($cash_register->total_gift_card_sales) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.return_sales')</b></td>
                            <td>{{ @num_format($cash_register->total_sell_return) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.purchases')</b></td>
                            <td>{{ @num_format($cash_register->total_purchases) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.expenses')</b></td>
                            <td>{{ @num_format($cash_register->total_expenses) }}</td>
                        </tr>
                        <tr>
                            <td><b>@lang('lang.current_cash')</b></td>
                            <td>{{ @num_format($cash_register->total_cash_sales -$cash_register->total_refund_cash +$cash_register->total_cash_in -$cash_register->total_cash_out -$cash_register->total_purchases -$cash_register->total_expenses -$cash_register->total_sell_return) }}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
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
            <button type="submit" name="submit" class="btn btn-primary hide" value="adjustment" id="adjust-btn">@lang(
                'lang.adjustment' )</button>
            <button type="submit" name="submit" class="btn btn-primary" value="save" id="closing-save-btn">@lang(
                'lang.save' )</button>
            <button type="button"
                class="btn btn-default @if ($type == 'logout') close-btn-add-closing-cash @endif"
                @if ($type != 'logout') data-dismiss="modal" @endif>@lang( 'lang.close' )</button>
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

    })
</script>
