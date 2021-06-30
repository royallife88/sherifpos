<div class="modal-dialog" role="document">
    <div class="modal-content">

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.recent_transactions' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <table id="recent_transaction_table" class="table dataTable">
                <thead>
                    <tr>
                        <th>@lang('lang.date_and_time')</th>
                        <th>@lang('lang.value')</th>
                        <th>@lang('lang.customer_type')</th>
                        <th>@lang('lang.customer_name')</th>
                        <th>@lang('lang.payment_type')</th>
                        <th>@lang('lang.status')</th>
                        <th>@lang('lang.delivery_man')</th>
                        <th>@lang('lang.action')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                    <tr>
                        <td>{{\Carbon\Carbon::parse($transaction->transaction_date)->format('m/d/Y H:i:s')}}</td>
                        <td>{{@num_format($transaction->final_total)}}</td>
                        <td>@if(!empty($transaction->customer->customer_type)){{$transaction->customer->customer_type->name}}@endif</td>
                        <td>@if(!empty($transaction->customer)){{$transaction->customer->name}}@endif</td>
                        <td>{{$payment_types[$transaction->transaction_payments->first()->method]}}</td>
                        <td>{{ucfirst($transaction->status)}}</td>
                        <td></td>
                        <td>
                            <div class="btn-group">
                                <a href="{{action('SellPosController@edit', $transaction->id)}}" class="btn btn-success" ><i class="dripicons-document-edit"></i></a>
                                <button class="btn btn-danger remove_item" data-href={{action('SellPosController@destroy', $transaction->id)}}><i class="dripicons-trash"></i></button>

                            </div>
                        </td>


                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>

</script>
