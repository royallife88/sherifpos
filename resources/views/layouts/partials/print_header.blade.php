<div class="row" style="text-align: center; width: 100%;" >
    @php
        $letter_header = App\Models\System::getProperty('letter_header');
    @endphp
    <img src="@if(!empty($letter_header)){{asset('uploads/'.$letter_header)}}@else{{asset('/uploads/'.session('logo'))}}@endif" alt="header" style="width: auto; margin: auto; max-height: 150px;">
</div>
