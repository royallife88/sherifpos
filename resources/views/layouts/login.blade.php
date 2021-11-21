@php
$logo = App\Models\System::getProperty('logo');
$site_title = App\Models\System::getProperty('site_title');
@endphp
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{$site_title}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <link rel="manifest" href="{{url('manifest.json')}}">
    <link rel="icon" type="image/png" href="{{asset('/uploads/'.$logo)}}" />
    <!-- Bootstrap CSS-->
    @include('layouts.partials.css')
</head>

<body>
    <div class="page login-page"
        {{-- style="background-image: url('{{asset('images/r2.jpg')}}'); background-repeat: no-repeat; background-size: cover; background-position: center;" --}}
        >
        {{-- <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
                <div class="top-email">
                    <span class="h5"> @lang('lang.email'): info@sherifshalaby.tech</span>
                </div>
            </div>
        </nav> --}}
        @yield('content')
    </div>

    @include('layouts.partials.javascript')
    @yield('javascript')
</body>

</html>
