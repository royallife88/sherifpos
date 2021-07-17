<div class="row" style="text-align: center" >
    @php
        $letter_header = App\Models\System::getProperty('letter_header');
    @endphp
    <img src="{{asset('uploads/'.$letter_header)}}" alt="footer" style="width: 100%;">
</div>
