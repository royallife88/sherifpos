<div class="row text-center">
    <div class="col-md-12">
        <h4>@lang('lang.payment_details')</h4>
    </div>

</div>
<div class="col-md-12">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>@lang('lang.amount')</th>
                    <th>@lang('lang.payment_date')</th>
                    <th>@lang('lang.payment_type')</th>
                    <th>@lang('lang.bank_name')</th>
                    <th>@lang('lang.ref_number')</th>
                    <th>@lang('lang.bank_deposit_date')</th>
                    <th>@lang('lang.card_number')</th>
                    <th>@lang('lang.year')</th>
                    <th>@lang('lang.month')</th>
                    <th>@lang('lang.files')</th>
                    @if(!empty($show_action))
                    <th>@lang('lang.action')</th>
                    @endif

                </tr>
            </thead>

            @foreach ($payments as $payment)
            <tr>
                <td>{{@num_format($payment->amount)}}</td>
                <td>{{@format_date($payment->paid_on)}}</td>
                <td>{{$payment_type_array[$payment->method]}}</td>
                <td>{{$payment->bank_name}}</td>
                <td>{{$payment->ref_number}}</td>
                <td>@if(!empty($payment->bank_deposit_date && ($payment->method == 'bank_transfer' || $payment->method == 'cheque'))){{@format_date($payment->bank_deposit_date)}} @endif</td>
                <td>{{$payment->card_number}}</td>
                <td>{{$payment->card_year}}</td>
                <td>{{$payment->card_month}}</td>
                <td>
                    @php
                    $payment_media = $payment->getMedia('transaction_payment');
                    @endphp
                    @if(!empty($payment_media))
                    @foreach ($payment_media as $media)
                    <a href="{{$media->getUrl()}}">{{$media->name}}</a> <br>
                    @endforeach

                    @endif
                </td>
                @if(!empty($show_action))
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            @can('sale.pay.create_and_edit')
                            <li>
                                <a data-href="{{action('TransactionPaymentController@edit', $payment->id)}}"
                                    data-container=".view_modal" class="btn btn-modal"><i
                                        class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                            </li>
                            @endcan
                            @can('sale.pay.delete')
                            <li>
                                <a data-href="{{action('TransactionPaymentController@destroy', $payment->id)}}"
                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                    class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                    @lang('lang.delete')</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </td>
                @endif
            </tr>
            @endforeach
        </table>
    </div>
</div>
