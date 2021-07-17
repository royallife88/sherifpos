@extends('layouts.app')
@section('title', __('lang.emails'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.emails')</h4>
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
                            <th width="10%">@lang('lang.attachments')</th>
                            <th>@lang('lang.notes')</th>
                            <th class="notexport"  style="width: 30%">@lang('lang.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emails as $key)
                        <tr>
                            <td>{{$key->created_at}}</td>
                            <td>{{$key->sent_by}}</td>
                            <td>{!!$key->body!!}</td>
                            <td>{{$key->emails}}</td>
                            <td>
                                @foreach ($key->attachments as $item)
                                <a target="_blank" href="{{asset($item)}}">{{str_replace('/emails/', '', $item)}}</a>
                                <br>
                                @endforeach
                            </td>
                            <td>{{$key->notes}}</td>
                            <td>
                                @can('email_module.email.create_and_edit')
                                <a href="{{action('EmailController@edit', $key->id)}}"
                                    class="btn btn-danger text-white"><i class="fa fa-pencil-square-o"></i></a>
                                @endcan
                                @can('email_module.email.delete')
                                <a data-href="{{action('EmailController@destroy', $key->id)}}"
                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
                                    class="btn btn-danger text-white delete_item"><i class="fa fa-trash"></i></a>
                                @endcan
                                @can('email_module.resend.create_and_edit')
                                <a href="{{action('EmailController@resend', $key->id)}}"
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
