Hello {{ $user->name }},<br><br>

Welcome to {{env('APP_NAME')}}.<br><br>

Please use below credentials to login: <br>
Email: {{$user->email}} <br>
Password: {{$employee->pass_string}} <br><br>


Thank You,<br>
<a href="{{url('/')}}">{{env('APP_NAME')}}</a>
