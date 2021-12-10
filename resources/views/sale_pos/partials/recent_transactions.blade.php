<div class="table-responsive">
    <table id="recent_transaction_table" class="table">
        <thead>
            <tr>
                <th>@lang('lang.date_and_time')</th>
                <th>@lang('lang.invoice_no')</th>
                <th class="sum">@lang('lang.value')</th>
                <th>@lang('lang.customer_type')</th>
                <th>@lang('lang.customer_name')</th>
                <th>@lang('lang.payment_type')</th>
                <th>@lang('lang.status')</th>
                <th>@lang('lang.delivery_man')</th>
                <th class="notexport">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody>

            @forelse($transactions as $transaction)
            <tr>
                <td>{{@format_datetime($transaction->transaction_date)}}</td>
                <td>{{$transaction->invoice_no}}</td>
                <td>{{@num_format($transaction->final_total)}}</td>
                <td>@if(!empty($transaction->customer->customer_type)){{$transaction->customer->customer_type->name}}@endif
                </td>
                <td>@if(!empty($transaction->customer)){{$transaction->customer->name}}@endif</td>
                <td>@if(!empty($transaction->transaction_payments->first()->method)){{$payment_types[$transaction->transaction_payments->first()->method]}}@endif
                </td>
                <td>@if($transaction->status == 'final' && $transaction->payment_status == 'pending')
                    @lang('lang.pending') @else {{ucfirst($transaction->status)}} @endif</td>
                <td></td>
                <td>
                    <div class="btn-group">
                        @can('sale.pos.view')
                        <a data-href="{{action('SellController@print', $transaction->id)}}"
                            class="btn btn-danger text-white print-invoice"><i title="@lang('lang.print')"
                                data-toggle="tooltip" class="dripicons-print"></i></a>
                        @endcan
                        @can('sale.pos.view')
                        <a data-href="{{action('SellController@show', $transaction->id)}}"
                            class="btn btn-primary text-white  btn-modal" data-container=".view_modal"><i
                                title="@lang('lang.view')" data-toggle="tooltip" class="fa fa-eye"></i></a>
                        @endcan
                        @can('superadmin')
                        <a href="{{action('SellController@edit', $transaction->id)}}" class="btn btn-success"><i
                                title="@lang('lang.edit')" data-toggle="tooltip"
                                class="dripicons-document-edit"></i></a>
                        @endcan
                        @can('superadmin')
                        <a data-href="{{action('SellController@destroy', $transaction->id)}}"
                            title="@lang('lang.delete')" data-toggle="tooltip"
                            data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                            class="btn btn-danger delete_item" style="color: white"><i class="fa fa-trash"></i></a>
                        @endcan
                        @if(empty($transaction->return_parent))
                        @can('return.sell_return.create_and_edit')
                        <a href="{{action('SellReturnController@add', $transaction->id)}}"
                            title="@lang('lang.sell_return')" data-toggle="tooltip" class="btn btn-secondary"
                            style="color: white"><i class="fa fa-undo"></i></a>
                        @endcan
                        @endif
                        @if($transaction->status != 'draft' && $transaction->payment_status != 'paid')
                        <a data-href="{{action('TransactionPaymentController@addPayment', ['id' => $transaction->id])}}"
                            title="@lang('lang.pay_now')" data-toggle="tooltip" data-container=".view_modal"
                            class="btn btn-modal btn-success" style="color: white"><i class="fa fa-money"></i></a>
                        @endif

                    </div>
                </td>
                @empty
            <tr style="text-align: center;" class="no_data_found">
                <td colspan="9">@lang('lang.no_transaction_found')</td>
            </tr>

            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" style="text-align: right"> @lang('lang.total')</th>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>
