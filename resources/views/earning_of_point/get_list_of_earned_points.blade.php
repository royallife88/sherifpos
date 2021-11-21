@extends('layouts.app')
@section('title', __('lang.list_of_earn_point_by_transactions'))

@section('content')
<div class="container-fluid no-print">


    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.list_of_earn_point_by_transactions')</h4>
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
                                <th>@lang('lang.product_grant_the_points')</th>
                                <th>@lang('lang.value')</th>
                                <th>@lang('lang.paid_amount')</th>
                                <th>@lang('lang.point_earned')</th>
                                <th>@lang('lang.balance')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr>
                                <td>{{@format_datetime($transaction->transaction_date)}}
                                </td>
                                <td>{{$transaction->store->name ?? ''}}</td>
                                <td>{{ucfirst($transaction->created_by_user->name ?? '')}}</td>
                                <td>{{$transaction->customer->name ?? ''}}</td>
                                <td style="color: rgb(85, 85, 231)"><a data-href="{{action('SellController@show', $transaction->id)}}"
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
                                <td>{{@num_format($transaction->rp_earned)}}</td>
                                <td>{{@num_format($transaction->customer->total_rp)}}</td>

                            </tr>

                            @endforeach
                        </tbody>
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
