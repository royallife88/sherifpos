@extends('layouts.app')
@section('title', __('lang.list_of_redeemed_point_by_transactions'))

@section('content')
<div class="container-fluid no-print">


    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.list_of_redeemed_point_by_transactions')</h4>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table id="store_table" class="table dataTable">
                        <thead>
                            <tr>
                                <th>@lang('lang.date_and_time')</th>
                                <th>@lang('lang.store')</th>
                                <th>@lang('lang.cashier')</th>
                                <th>@lang('lang.customer')</th>
                                <th>@lang('lang.invoice_no')</th>
                                <th>@lang('lang.product_purchased_with_points')</th>
                                <th class="sum">@lang('lang.value')</th>
                                <th class="sum">@lang('lang.paid_amount')</th>
                                <th class="sum">@lang('lang.point_earned')</th>
                                <th class="sum">@lang('lang.balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_paid = 0;
                                $total_point_earned = 0;
                                $total_balance = 0;
                            @endphp
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>{{@format_datetime($transaction->transaction_date)}}
                                </td>
                                <td>{{$transaction->store->name ?? ''}}</td>
                                <td>{{ucfirst($transaction->created_by_user->name ?? '')}}</td>
                                <td>{{$transaction->customer->name ?? ''}}</td>
                                <td style="color: rgb(85, 85, 231)"><a
                                        data-href="{{action('SellController@show', $transaction->id)}}"
                                        data-container=".view_modal"
                                        class="btn btn-modal">{{$transaction->invoice_no}}</a></td>
                                <td>
                                    @php
                                    $sell_lines = App\Models\TransactionSellLine::where('transaction_id',
                                    $transaction->id)->where('point_earned', 1)->get();
                                    @endphp
                                    @foreach ($sell_lines as $line)
                                    {{$line->product->name}},
                                    @endforeach
                                </td>
                                <td>{{@num_format($transaction->final_total)}}</td>
                                <td>{{@num_format($transaction->transaction_payments->sum('amount'))}}</td>
                                <td>{{@num_format($transaction->rp_redeemed)}}</td>
                                <td>{{@num_format($transaction->customer->total_rp)}}</td>

                            </tr>
                            @php
                                $total_paid += $transaction->transaction_payments->sum('amount');
                                $total_point_earned += $transaction->rp_redeemed;
                                $total_balance += $transaction->customer->total_rp;
                            @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th style="text-align: right">@lang('lang.total')</th>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>

</script>
@endsection
