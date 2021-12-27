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
    <input type="hidden" id="__language" value="{{session('language')}}">
    <div class="page login-page" @yield('content') </div>
        <script type="text/javascript">
            base_path = "{{url('/')}}";
        </script>
        @include('layouts.partials.javascript')
        @yield('javascript')
</body>

</html>
