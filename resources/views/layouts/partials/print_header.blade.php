<div class="row" style="text-align: center" >
    @php
        $letter_header = App\Models\System::getProperty('letter_header');
    @endphp
    <img src="@if(!empty($letter_header)){{asset('uploads/'.$letter_header)}}@else{{asset('/uploads/'.session('logo'))}}@endif" alt="header" style="width: 100%;">
</div>
