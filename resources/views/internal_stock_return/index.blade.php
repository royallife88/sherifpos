@extends('layouts.app')
@section('title', __('lang.internal_stock_return'))

@section('content')
<div class="col-md-12 no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.internal_stock_return')</h4>
        </div>
        <div class="card-body">
            <form action="">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('sender_store_id', __('lang.sender_store'). ':', []) !!}
                            {!! Form::select('sender_store_id', $stores,
                            null, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                            'required',
                            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('receiver_store_id', __('lang.receiver_store'). ':', []) !!}
                            {!! Form::select('receiver_store_id', $stores,
                            null, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                            'required',
                            'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
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
                        <a href="{{action('InternalStockReturnController@index')}}"
                            class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="table-responsive no-print">
    <table id="sales_table" class="table dataTable">
        <thead>
            <tr>
                <th>@lang('lang.date')</th>
                <th>@lang('lang.reference')</th>
                <th>@lang('lang.created_by')</th>
                <th>@lang('lang.approved_at')</th>
                <th>@lang('lang.received_at')</th>
                <th>@lang('lang.sender_store')</th>
                <th>@lang('lang.receiver_store')</th>
                <th class="sum">@lang('lang.value_of_transaction')</th>
                <th>@lang('lang.status')</th>
                <th>@lang('lang.notes')</th>
                {{-- <th>@lang('lang.files')</th> --}}
                <th class="notexport">@lang('lang.action')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transfers as $transfer)
            <tr>
                <td>{{@format_date($transfer->transaction_date)}}</td>
                <td>{{$transfer->invoice_no}}</td>
                <td>{{ucfirst($transfer->created_by_user->name ?? '')}}</td>
                <td>@if(!empty($transfer->approved_at)) {{@format_date($transfer->approved_at)}} @endif</td>
                <td>@if(!empty($transfer->received_at)) {{@format_date($transfer->received_at)}} @endif</td>
                <td>{{ucfirst($transfer->sender_store->name ?? '')}}</td>
                <td>{{ucfirst($transfer->receiver_store->name ?? '')}}</td>
                <td>{{@num_format($transfer->final_total)}}</td>
                <td>@if($transfer->status == 'received'){{__('lang.received')}}@else{{ucfirst($transfer->status)}}@endif</td>
                <td>{{$transfer->notes}}</td>
                {{-- <td>
                    <a data-href="{{action('GeneralController@viewUploadedFiles', ['model_name' => 'Transaction', 'model_id' => $transfer->id, 'collection_name' => 'internal_stock_return'])}}"
                        data-container=".view_modal"
                        class="btn-modal">@lang('lang.view')</a> --}}
                </td>
                <td>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                            <span class="caret"></span>
                            <span class="sr-only">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                            @if($transfer->is_internal_stock_transfer == 1 && $transfer->status != 'final')
                            <li>
                                <a data-href="{{action('InternalStockReturnController@getUpdateStatus', $transfer->id)}}"
                                    data-container=".view_modal" class="btn btn-modal"><i class="fa fa-arrow-up"></i>
                                    @lang('lang.update_status')</a>
                            </li>
                            <li class="divider"></li>
                            @endif
                            @can('stock.internal_stock_return.view')
                            <li>

                                <a href="{{action('InternalStockReturnController@show', $transfer->id)}}"
                                    class="btn"><i class="fa fa-eye"></i>
                                    @lang('lang.view')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('stock.internal_stock_return.view')
                            <li>

                                <a href="{{action('InternalStockReturnController@show', $transfer->id)}}?print=true"
                                    class="btn"><i class="dripicons-print"></i>
                                    @lang('lang.print')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('stock.internal_stock_return.create_and_edit')
                            <li>

                                <a data-href="{{action('InternalStockReturnController@getUpdateStatus', $transfer->id)}}"
                                    data-container=".view_modal" class="btn btn-modal"><i class="fa fa-arrow-up"></i>
                                    @lang('lang.edit')</a>
                            </li>
                            <li class="divider"></li>
                            @endcan
                            @can('stock.internal_stock_return.create_and_edit')
                            @if($transfer->status == 'approved')
                            <li>

                                <a href="{{action('InternalStockReturnController@sendTheGoods', $transfer->id)}}" class="btn"><i
                                        class="dripicons-document-edit"></i> @lang('lang.send_the_goods')</a>
                            </li>
                            <li class="divider"></li>
                            @endif
                            @endcan

                            @can('stock.internal_stock_return.delete')
                            <li>
                                <a data-href="{{action('InternalStockReturnController@destroy', $transfer->id)}}"
                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                    class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                    @lang('lang.delete')</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" style="text-align: right">@lang('lang.total')</th>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<!-- This will be printed -->
<section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
<script src="{{asset('js/transfer.js')}}"></script>
<script>
    table
    .column( '0:visible' )
    .order( 'desc' )
    .draw();

    $(document).on('click', '#update-status', function(e){
        e.preventDefault();
        if($('#update_status_form').valid()){
            $('#update_status_form').submit();
        }
    })

</script>
@endsection