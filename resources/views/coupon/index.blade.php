@extends('layouts.app')
@section('title', __('lang.coupon'))

@section('content')
<div class="container-fluid">
    @can('coupons_and_gift_cards.coupon.create_and_edit')
    <a style="color: white" data-href="{{action('CouponController@create')}}" data-container=".view_modal"
        class="btn btn-modal btn-info"><i class="dripicons-plus"></i>
        @lang('lang.generate_coupon')</a>
    @endcan

</div>
<br>
<div class="col-md-12 card pt-3 pb-3">
    <form action="">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __( 'lang.type' ) . ':*') !!}
                    {!! Form::select('type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], request()->type,
                    ['class' =>
                    'form-control', 'data-live-search' => 'true', 'placeholder' => __('lang.all')]) !!}
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
                    {!! Form::date('start_date', request()->start_date, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('end_date', __('lang.end_date'), []) !!}
                    {!! Form::date('end_date', request()->end_date, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <br>
                <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                <a href="{{action('CouponController@index')}}"
                    class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
            </div>

        </div>
    </form>
</div>

<div class="table-responsive">
    <table id="coupon_table" class="table dataTable">
        <thead>
            <tr>
                <th>@lang('lang.coupon_code')</th>
                <th>@lang('lang.type')</th>
                <th>@lang('lang.date_and_time')</th>
                <th>@lang('lang.created_by')</th>
                <th>@lang('lang.affected_by_products')</th>
                <th>@lang('lang.status')</th>

                <th class="notexport">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($coupons as $coupon)
            <tr>
                <td>{{$coupon->coupon_code}}</td>
                <td>{{ucfirst($coupon->type)}}</td>
                <td>{{@format_datetime($coupon->created_at)}}</td>
                <td>{{ucfirst($coupon->created_by_user->name)}}</td>
                <td>
                    @if(!$coupon->all_products)
                    @foreach ($coupon->products as $item)
                    {{$item->name}},
                    @endforeach
                    @else
                    @lang('lang.all_products')
                    @endif
                </td>
                {{-- TODO: 5.2.5.8.8- If the coupon is used the details of the transaction must appear upon click on the word “Used” --}}
                <td>@if($coupon->used) @lang('lang.used') @else @lang('lang.not_used') @endif</td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            @can('coupons_and_gift_cards.coupon.create_and_edit')
                            <li>

                                <a data-href="{{action('CouponController@edit', $coupon->id)}}"
                                    data-container=".view_modal" class="btn btn-modal"><i
                                        class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('coupons_and_gift_cards.coupon.delete')
                            <li>
                                <a data-href="{{action('CouponController@destroy', $coupon->id)}}"
                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                    class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                    @lang('lang.delete')</a>
                            </li>
                            @endcan
                            <li>
                                <a data-href="{{action('CouponController@toggleActive', $coupon->id)}}"
                                    class="btn text-red toggle-active"><i class="fa fa-refresh"></i>
                                    @if($coupon->active) @lang('lang.suspend') @else @lang('lang.reactivate') @endif</a>
                            </li>

                        </ul>
                    </div>
                </td>
            </tr>

            @endforeach
        </tbody>
    </table>
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
