<div class="table-responsive">
    <table id="recent_transaction_table" class="table dataTable">
        <thead>
            <tr>
                <th>@lang('lang.date_and_time')</th>
                <th>@lang('lang.invoice_no')</th>
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

            @forelse($transactions as $transaction)
            <tr>
                <td>{{\Carbon\Carbon::parse($transaction->transaction_date)->format('m/d/Y H:i:s')}}</td>
                <td>{{$transaction->invoice_no}}</td>
                <td>{{@num_format($transaction->final_total)}}</td>
                <td>@if(!empty($transaction->customer->customer_type)){{$transaction->customer->customer_type->name}}@endif
                </td>
                <td>@if(!empty($transaction->customer)){{$transaction->customer->name}}@endif</td>
                <td>@if(!empty($transaction->transaction_payments->first()->method)){{$payment_types[$transaction->transaction_payments->first()->method]}}@endif
                </td>
                <td>{{ucfirst($transaction->status)}}</td>
                <td></td>
                <td>
                    <div class="btn-group">
                        @can('sale.pos.create_and_edit')
                        <a href="{{action('SellController@edit', $transaction->id)}}" class="btn btn-success"><i
                                class="dripicons-document-edit"></i></a>
                        @endcan
                        @can('sale.pos.delete')
                        <a data-href="{{action('SellController@destroy', $transaction->id)}}"
                            data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                            class="btn btn-danger delete_item" style="color: white"><i class="fa fa-trash"></i></a>
                        @endcan
                        @can('return.sell_return.create_and_edit')
                        <a href="{{action('SellReturnController@add', $transaction->id)}}"
                            title="@lang('lang.sell_return')" data-toggle="tooltip" class="btn btn-secondary"
                            style="color: white"><i class="fa fa-undo"></i></a>
                        @endcan
                        @if($transaction->status != 'draft' && $transaction->payment_status != 'paid')
                        <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $transaction->id])}}"
                            data-container=".view_modal" class="btn btn-modal btn-success" style="color: white"><i
                                class="fa fa-money"></i></a>
                        @endif

                    </div>
                </td>
                @empty
            <tr style="text-align: center;">
                <td colspan="8">@lang('lang.no_transaction_found')</td>
            </tr>

            @endforelse
        </tbody>
    </table>
</div>
