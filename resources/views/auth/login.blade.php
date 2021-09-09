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
@endphp
<div class="container">
    <div class="navbar-holder d-flex align-items-center justify-content-between">
        <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center pull-right" style="margin-left: 95%">
            <li class="nav-item">
                <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true"
                    aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-web"></i>
                    <span>{{__('lang.language')}}</span> <i class="fa fa-angle-down"></i></a>
                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                    @foreach ($languages as $key => $lang)
                    <li>
                        <a href="{{action('GeneralController@switchLanguage', $key) }}" class="btn btn-link">
                            {{$lang}}</a>
                    </li>
                    @endforeach

                </ul>
            </li>
        </ul>
    </div>
    <div class="form-outer text-center d-flex align-items-center">
        <div class="form-inner">
            <div class="logo">@if($logo)<img src="{{asset('/uploads/'.$logo)}}" width="200">&nbsp;&nbsp;@endif</div>
            @if(session()->has('delete_message'))
            <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close"
                    data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>{{ session()->get('delete_message') }}</div>
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
        </div>
        <div class="copyrights text-center">
            <p>&copy; {{App\Models\System::getProperty('site_title')}} | <span class="">@lang('lang.developed_by') <a
                        target="_blank" href="http://sherifshalaby.tech">sherifshalaby.tech</a></span></p>
        </div>
    </div>
</div>
@endsection
