@extends('layouts.app')
@section('title', __('lang.remove_stock'))

@section('content')
<section class="">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('supplier_id', __('lang.supplier'), []) !!}
                                {!! Form::select('supplier_id', $suppliers, request()->supplier_id, ['class' =>
                                'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('store_id', __('lang.store'), []) !!}
                                {!! Form::select('store_id', $stores, request()->store_id, ['class' =>
                                'form-control', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('status', __('lang.status'), []) !!}
                                {!! Form::select('status', ['pendign' => __('lang.pending'), 'compensated' =>
                                __('lang.compensated')], request()->status, ['class' =>
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
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                {!! Form::text('start_date', request()->start_date, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('start_time', __('lang.start_time'), []) !!}
                                {!! Form::text('start_time', request()->start_time, ['class' => 'form-control
                                time_picker sale_filter']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                {!! Form::text('end_date', request()->end_date, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                {!! Form::label('end_time', __('lang.end_time'), []) !!}
                                {!! Form::text('end_time', request()->end_time, ['class' => 'form-control time_picker
                                sale_filter']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <br>
                            <button type="submit" class="btn btn-success mt-2">@lang('lang.filter')</button>
                            <a href="{{action('RemoveStockController@index')}}"
                                class="btn btn-danger mt-2 ml-2">@lang('lang.clear_filter')</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table dataTable">
            <thead>
                <tr>
                    <th>@lang('lang.date_and_time')</th>
                    <th>@lang('lang.removal_transaction_no')</th>
                    <th>@lang('lang.status')</th>
                    <th>@lang('lang.store')</th>
                    <th>@lang('lang.reason')</th>
                    <th>@lang('lang.value')</th>
                    <th>@lang('lang.files')</th>
                    <th>@lang('lang.invoice_no')</th>
                    <th class="notexport">@lang('lang.action')</th>
                </tr>
            </thead>

            <tbody>
                @foreach ($remove_stocks as $remove_stock)
                <tr>
                    <td> {{@format_date($remove_stock->transaction_date)}}</td>
                    <td>{{$remove_stock->invoice_no}}</td>
                    <td>{{ucfirst($remove_stock->status)}}</td>
                    <td>
                        {{$remove_stock->store->name ?? ''}}
                    </td>
                    <td>
                        {{$remove_stock->reason}}
                    </td>
                    <td>
                        {{@num_format($remove_stock->final_total)}}
                    </td>
                    <td><a data-href="{{action('GeneralController@viewUploadedFiles', ['model_name' => 'Transaction', 'model_id' => $remove_stock->id, 'collection_name' => 'remove_stock'])}}"
                            data-container=".view_modal" class="btn btn-modal">@lang('lang.view')</a></td>
                    <td>
                        @if(!empty($remove_stock->add_stock_id)){{App\Models\Transaction::find($remove_stock->add_stock_id)->invoice_no
                        }}@endif
                    </td>
                    <td>

                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                @can('remove_stock.remove_stock.view')
                                <li>
                                    <a href="{{action('RemoveStockController@show', $remove_stock->id)}}"><i
                                            class="fa fa-eye btn"></i>@lang('lang.view')</a>
                                </li>
                                <li>
                                    <a href="{{action('RemoveStockController@show', $remove_stock->id)}}?print=true"><i
                                            class="dripicons-print btn"></i>@lang('lang.print')</a>
                                </li>
                                <li class="divider"></li>
                                @endcan
                                @can('remove_stock.remove_stock.create_and_edit')
                                <li>
                                    <a href="{{action('RemoveStockController@edit', $remove_stock->id)}}"><i
                                            class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                </li>
                                <li class="divider"></li>
                                @endcan
                                @if($remove_stock->status != 'compensated')
                                @can('remove_stock.remove_stock.create_and_edit')
                                <li>
                                    <a data-href="{{action('RemoveStockController@getUpdateStatusAsCompensated', $remove_stock->id)}}"
                                        class="btn-modal" data-container=".view_modal"><i
                                            class="fa fa-adjust btn"></i>@lang('lang.compensated')</a>
                                </li>
                                <li class="divider"></li>
                                @endcan
                                @endif
                                @can('remove_stock.remove_stock.delete')
                                <li>
                                    <a data-href="{{action('RemoveStockController@destroy', $remove_stock->id)}}"
                                        data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                        class="btn text-red delete_item"><i class="dripicons-trash"></i>
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

            </tfoot>
        </table>

    </div>



</section>
@endsection

@section('javascript')
<script type="text/javascript">

</script>
@endsection
