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
                <table id="" class="table dataTable" style="width: auto">
                    <thead>
                        <tr>
                            <th style="width: 10% !important;">@lang('lang.date_and_time')</th>
                            <th style="width: 10% !important;">@lang('lang.created_by')</th>
                            <th style="width: 40% !important;">@lang('lang.content')</th>
                            <th style="width: 10% !important;">@lang('lang.receiver')</th>
                            <th style="width: 10% !important;">@lang('lang.attachments')</th>
                            <th style="width: 10% !important;">@lang('lang.notes')</th>
                            <th style="width: 10% !important;" class="notexport">@lang('lang.action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($emails as $key)
                        <tr>
                            <td>{{$key->created_at}}</td>
                            <td>{{$key->sent_by}}</td>
                            <td>{!!$key->body!!}</td>
                            @php
                                $emails_array = explode(',', $key->emails);
                                $emails = implode(' ,', $emails_array);
                            @endphp
                            <td>{{$emails}}</td>
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
