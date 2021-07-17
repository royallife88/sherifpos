@extends('layouts.app')
@section('title', __('lang.sms'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.sms')</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="store_table" class="table dataTable">
                    <thead>
                        <tr>
                            <th>@lang('lang.date_and_time')</th>
                            <th>@lang('lang.created_by')</th>
                            <th>@lang('lang.content')</th>
                            <th>@lang('lang.receiver')</th>
                            <th>@lang('lang.notes')</th>
                            <th class="notexport">@lang('lang.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sms as $key)
                        <tr>
                            <td>{{$key->created_at}}</td>
                            <td>{{$key->sent_by}}</td>
                            <td>{!!$key->message!!}</td>
                            <td>{{$key->mobile_numbers}}</td>
                            <td>{{$key->notes}}</td>
                            <td>
                                @can('sms_module.sms.create_and_edit')
                                <a href="{{action('SmsController@edit', $key->id)}}"
                                    class="btn btn-danger text-white"><i class="fa fa-pencil-square-o"></i></a>
                                @endcan
                                @can('sms_module.sms.delete')
                                <a data-href="{{action('SmsController@destroy', $key->id)}}"
                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                    class="btn btn-danger text-white delete_item"><i class="fa fa-trash"></i></a>
                                @endcan
                                @can('sms_module.resend.create_and_edit')
                                <a href="{{action('SmsController@resend', $key->id)}}"
                                    class="btn btn-danger text-white"><i class="fa fa-paper-plane"></i></a>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')

@endsection
