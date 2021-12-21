@extends('layouts.login')

@section('content')
@php
$logo = App\Models\System::getProperty('logo');
$site_title = App\Models\System::getProperty('site_title');
$config_languages = config('constants.langs');
$languages = [];
foreach ($config_languages as $key => $value) {
$languages[$key] = $value['full_name'];
}
$version_number = App\Models\System::getProperty('version_number');
$version_update_datatime = App\Models\System::getProperty('version_update_date');
@endphp
<div class="container">
    <div class="form-outer text-center d-flex align-items-center">
        <div class="form-inner">
            <div class="row" style="text-align: left;">
                <div class="col-md-12" style="color: #7c5cc4">
                    <h4>@lang('lang.version'): {{$version_number}}</h4>
                    <h4>@lang('lang.last_update'):
                        @if(!empty($version_update_datatime)){{\Carbon\Carbon::createFromTimestamp(strtotime($version_update_datatime))->format('d-M-Y
                        H:i a')}}@endif</h4>
                </div>
            </div>
            <div class="navbar-holder">
                <div class="dropdown">
                    <button class="btn dropdown-toggle" style="color: gray" type="button" id="dropdownMenuButton"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @lang('lang.language')
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        @foreach ($languages as $key => $lang)
                        <a class="dropdown-item" href="{{action('GeneralController@switchLanguage', $key) }}">
                            {{$lang}}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="logo">@if($logo)<img src="{{asset('/uploads/'.$logo)}}" width="200">&nbsp;&nbsp;@endif</div>
            @if(session()->has('delete_message'))
            <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                    data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{
                session()->get('delete_message') }}</div>
            @endif
            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf
                <div class="form-group-material">
                    <input id="email" type="email" name="email" required class="input-material" value=""
                        placeholder="{{trans('lang.email')}}">
                </div>

                <div class="form-group-material">
                    <input id="password" type="password" name="password" required class="input-material" value=""
                        placeholder="{{trans('lang.password')}}">
                </div>
                @if ($errors->has('email'))
                <p style="color:red">
                    <strong>{{ $errors->first('email') }}</strong>
                </p>
                <br>
                @endif
                <button type="submit" class="btn btn-primary btn-block">{{trans('lang.login')}}</button>
            </form>
            <a href="{{ route('password.request') }}" class="forgot-pass">{{trans('lang.forgot_passowrd')}}</a>
            <p>
                <a href="{{action('ContactUsController@getContactUs')}}">@lang('lang.contact_us')</a>
            </p>
            <div class="copyrights text-center">
                <p>&copy; {{App\Models\System::getProperty('site_title')}} | <span class="">@lang('lang.developed_by')
                        <a target="_blank" href="http://sherifshalaby.tech">sherifshalaby.tech</a></span></p>
                <p>
                    <a href="mailto:info@sherifshalaby.tech">info@sherifshalaby.tech</a>
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
