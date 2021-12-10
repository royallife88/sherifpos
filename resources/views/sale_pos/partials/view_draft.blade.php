<div class="table-responsive">
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

            @forelse($transactions as $transaction)
            <tr>
                <td>{{@format_datetime($transaction->transaction_date)}}</td>
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
                        <a target="_blank" href="{{action('SellPosController@edit', $transaction->id)}}?status=draft"
                            class="btn btn-success"><i class="dripicons-document-edit"></i></a>
                        <button class="btn btn-danger remove_draft" data-href={{action('SellController@destroy',
                            $transaction->id)}}
                            data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                            ><i class="dripicons-trash"></i></button>
                        <a target="_blank" href="{{action('SellPosController@edit', $transaction->id)}}?status=final"
                            class="btn btn-success"><i class="fa fa-money"></i></a>

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
