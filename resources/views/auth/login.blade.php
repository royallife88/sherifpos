<?php $general_setting = DB::table('general_settings')->find(1); ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{$general_setting->site_title}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <link rel="manifest" href="{{url('manifest.json')}}">
    <link rel="icon" type="image/png" href="{{url('logo', $general_setting->site_logo)}}" />
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="{{asset('vendor/bootstrap/css/bootstrap.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('vendor/bootstrap/css/bootstrap-datepicker.min.css')}}" type="text/css">
    <link rel="stylesheet" href="{{asset('vendor/bootstrap/css/bootstrap-select.min.css')}}" type="text/css">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="{{asset('vendor/font-awesome/css/font-awesome.min.css')}}" type="text/css">
    <!-- Google fonts - Roboto -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700">
    <!-- jQuery Circle-->
    <link rel="stylesheet" href="{{asset('css/grasp_mobile_progress_circle-1.0.0.min.css')}}" type="text/css">
    <!-- Custom Scrollbar-->
    <link rel="stylesheet" href="{{asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css')}}"
        type="text/css">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="{{asset('css/style.default.css')}}" id="theme-stylesheet" type="text/css">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="{{asset('css/custom-'.$general_setting->theme)}}" type="text/css">
    <!-- Tweaks for older IEs-->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->


    <script type="text/javascript" src="{{asset('vendor/jquery/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/jquery/jquery-ui.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/jquery/bootstrap-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/popper.js/umd/popper.min.js')}}">
    </script>
    <script type="text/javascript" src="{{asset('vendor/bootstrap/js/bootstrap.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/bootstrap/js/bootstrap-select.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('js/grasp_mobile_progress_circle-1.0.0.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/jquery.cookie/jquery.cookie.js')}}">
    </script>
    <script type="text/javascript" src="{{asset('vendor/jquery-validation/jquery.validate.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('vendor/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js')}}"></script>
<script type=" text/javascript" src="{{asset('js/front.js')}}"></script>
</head>
@php
    $logo = App\Models\System::getProperty('logo');
    $site_title = App\Models\System::getProperty('site_title');
    $config_languages = config('constants.langs');
    $languages = [];
    foreach ($config_languages as $key => $value) {
        $languages[$key] = $value['full_name'];
    }
@endphp
<body>
    <div class="page login-page">
        <div class="container">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
            <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center pull-right" style="margin-left: 95%">
                <li class="nav-item">
                    <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-item"><i class="dripicons-web"></i> <span>{{__('lang.language')}}</span> <i class="fa fa-angle-down"></i></a>
                    <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                        @foreach ($languages as $key => $lang)
                        <li>
                          <a href="{{action('GeneralController@switchLanguage', $key) }}" class="btn btn-link"> {{$lang}}</a>
                        </li>
                        @endforeach

                    </ul>
              </li>
            </ul>
            </div>
            <div class="form-outer text-center d-flex align-items-center">
                <div class="form-inner">
                    <div class="logo"><span>{{$site_title}}</span></div>
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
                            <input id="password" type="password" name="password" required class="input-material"
                                value="" placeholder="{{trans('lang.password')}}">
                        </div>
                        @if ($errors->has('email'))
                        <p style="color:red">
                            <strong>{{ $errors->first('email') }}</strong>
                        </p>
                        <br>
                        @endif
                        <button type="submit" class="btn btn-primary btn-block">{{trans('lang.login')}}</button>
                    </form>

                </div>
                <div class="copyrights text-center">
                    <p>{{trans('lang.developed_by')}} <span class="external">{{$general_setting->developed_by}}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
