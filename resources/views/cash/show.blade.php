<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.cash_details' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <table class="table">
                    <tr>
                        <td><b>@lang('lang.date_and_time')</b></td>
                        <td>{{@format_datetime($cash_register->created_at)}}</td>
                    </tr>
                    <tr>
                        <td><b>@lang('lang.cash_in')</b></td>
                        <td>{{@num_format($cash_register->total_cash_in)}}</td>
                    </tr>
                    <tr>
                        <td><b>@lang('lang.total_sales')</b></td>
                        <td>{{@num_format($cash_register->total_sale)}}</td>
                    </tr>
                    <tr>
                        <td><b>@lang('lang.total_cash_sale')</b></td>
                        <td>{{@num_format($cash_register->total_cash_sales)}}</td>
                    </tr>
                    <tr>
                        <td><b>@lang('lang.total_card_sale')</b></td>
                        <td>{{@num_format($cash_register->total_card_sales)}}</td>
                    </tr>
                    <tr>
                        <td><b>@lang('lang.total_cheque_sale')</b></td>
                        <td>{{@num_format($cash_register->total_cheque_sales)}}</td>
                    </tr>
                    <tr>
                        <td><b>@lang('lang.total_bank_transfer_sale')</b></td>
                        <td>{{@num_format($cash_register->total_bank_transfer_sales)}}</td>
                    </tr>
                    <tr>
                        <td><b>@lang('lang.total_gift_card_sale')</b></td>
                        <td>{{@num_format($cash_register->total_gift_card_sales)}}</td>
                    </tr>
                    <tr>
                        <td><b>@lang('lang.return_sales')</b></td>
                        <td>{{@num_format($cash_register->total_return_sales)}}</td>
                    </tr>
                    <tr>
                        <td><b>@lang('lang.expenses')</b></td>
                        <td>{{@num_format($cash_register->total_expenses)}}</td>
                    </tr>
                    <tr>
                        <td><b>@lang('lang.current_cash')</b></td>
                        <td>{{@num_format($cash_register->total_cash_sales)}}</td>
                    </tr>
                </table>
            </div>
            <input type="hidden" name="cash_register_id" value="{{$cash_register->id}}">
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker('render')
</script>