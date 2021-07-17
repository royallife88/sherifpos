<div class="row" style="text-align: center" >
    @php
       $letter_footer = App\Models\System::getProperty('letter_footer');
    @endphp
    <img src="{{asset('uploads/'.$letter_footer)}}" alt="footer" style="width: 100%;">
</div>
