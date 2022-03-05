@extends('layouts.app')
@section('title', __('lang.gift_card'))

@section('content')
<div class="container-fluid">
    @can('coupons_and_gift_cards.gift_card.create_and_edit')
    <a style="color: white" data-href="{{action('GiftCardController@create')}}" data-container=".view_modal"
        class="btn btn-modal btn-info"><i class="dripicons-plus"></i>
        @lang('lang.generate_gift_card')</a>
    @endcan

    <br>
    <br>
    <div class="col-md-12 card pt-3 pb-3">
        <form action="">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_id', __('lang.customer'), []) !!}
                        {!! Form::select('customer_id', $customers, request()->customer_id, ['class' =>
                        'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('created_by', __('lang.created_by'), []) !!}
                        {!! Form::select('created_by', $users, request()->created_by, ['class' =>
                        'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('start_date', __('lang.start_date'), []) !!}
                        {!! Form::text('start_date', request()->start_date, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('end_date', __('lang.end_date'), []) !!}
                        {!! Form::text('end_date', request()->end_date, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('status', __('lang.status'), []) !!}
                        {!! Form::select('status', [0 => 'Not Used', 1 => 'Used'], request()->status, ['class' =>
                        'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <br>
                    <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                    <a href="{{action('GiftCardController@index')}}"
                        class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                </div>

            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table id="gift_card_table" class="table dataTable">
            <thead>
                <tr>
                    <th>@lang('lang.card_number')</th>
                    <th class="sum">@lang('lang.amount')</th>
                    <th>@lang('lang.date_and_time')</th>
                    <th>@lang('lang.expiry_date')</th>
                    <th>@lang('lang.created_by')</th>
                    <th>@lang('lang.balance')</th>
                    <th>@lang('lang.status')</th>

                    <th class="notexport">@lang('lang.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gift_cards as $gift_card)
                <tr>
                    <td>{{$gift_card->card_number}}</td>
                    <td>{{@num_format($gift_card->amount)}}</td>
                    <td>{{@format_datetime($gift_card->created_at)}}</td>
                    <td>@if(!empty($gift_card->expiry_date)){{@format_date($gift_card->expiry_date)}}@else @lang('lang.unlimited') @endif</td>
                    <td>{{ucfirst($gift_card->created_by_user->name ?? '')}}</td>
                    <td>{{@num_format($gift_card->balance)}}</td>
                    <td>
                        @if($gift_card->used)
                        @php
                        $transaction = App\Models\Transaction::where('type', 'sell')->where('gift_card_id',
                        $gift_card->id)->first();
                        @endphp
                        @if(!empty($transaction))
                        <a data-href="{{action('SellController@show', $transaction->id)}}" data-container=".view_modal"
                            class="btn btn-modal">
                            @lang('lang.used') </a>
                        @else
                        @lang('lang.used')
                        @endif
                        @else
                        @lang('lang.not_used')
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                @can('coupons_and_gift_cards.gift_card.create_and_edit')
                                <li>

                                    <a data-href="{{action('GiftCardController@edit', $gift_card->id)}}"
                                        data-container=".view_modal" class="btn btn-modal"><i
                                            class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                                </li>
                                <li class="divider"></li>
                                @endcan
                                @can('coupons_and_gift_cards.gift_card.delete')
                                <li>
                                    <a data-href="{{action('GiftCardController@destroy', $gift_card->id)}}"
                                        data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                        class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                        @lang('lang.delete')</a>
                                </li>
                                @endcan
                                <li>
                                    <a data-href="{{action('GiftCardController@toggleActive', $gift_card->id)}}"
                                        class="btn text-red toggle-active"><i class="fa fa-refresh"></i>
                                        @if($gift_card->active) @lang('lang.suspend') @else @lang('lang.reactivate')
                                        @endif</a>
                                </li>

                            </ul>
                        </div>
                    </td>
                </tr>

                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th style="text-align: right">@lang('lang.total')</th>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $(document).on('click', 'a.toggle-active', function(e) {
		e.preventDefault();

        $.ajax({
            method: 'get',
            url: $(this).data('href'),
            data: {  },
            success: function(result) {
                if (result.success == true) {
                    swal(
                    'Success',
                    result.msg,
                    'success'
                    );
                    setTimeout(() => {
                        location.reload();
                    }, 500);
                }
            },
        });
    });
</script>
@endsection
