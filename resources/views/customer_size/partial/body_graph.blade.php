<div class="row">
    <div
        style="width: 344px; height: 892px; margin: auto; background-image: url('{{asset('images/customer_size_chart.jpg')}}'); -webkit-print-color-adjust: exact;">
        <div class="col-md-4" style="text-align: left; width: 33%; float: left;">
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 100px;"><span class="neck_round_span">@if(!empty($customer_size)){{@num_format($customer_size->neck_round['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 19px;"><span class="shoulder_er_span">@if(!empty($customer_size)){{@num_format($customer_size->shoulder_er['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 53px;"><span class="arm_round_span">@if(!empty($customer_size)){{@num_format($customer_size->arm_round['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 111px;"><span class="hips_span">@if(!empty($customer_size)){{@num_format($customer_size->hips['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 117px;"><span class="thigh_span">@if(!empty($customer_size)){{@num_format($customer_size->thigh['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 52px;"><span class="knee_round_span">@if(!empty($customer_size)){{@num_format($customer_size->knee_round['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 39px;"><span class="calf_round_span">@if(!empty($customer_size)){{@num_format($customer_size->calf_round['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 110px;"><span class="ankle_span">@if(!empty($customer_size)){{@num_format($customer_size->ankle['cm'])}}@endif</span></p>
        </div>
        <div class="col-md-4" style="text-align: center; width: 33%; float: left;">
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 168px; margin-left: 53px"><span
                    class="neck_deep_span">@if(!empty($customer_size)){{@num_format($customer_size->neck_deep['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 17px;"><span class="upper_bust_span">@if(!empty($customer_size)){{@num_format($customer_size->upper_bust['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 0px;"><span class="bust_span">@if(!empty($customer_size)){{@num_format($customer_size->bust['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 21px;"><span class="low_bust_span">@if(!empty($customer_size)){{@num_format($customer_size->low_bust['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 11px;"><span class="waist_span">@if(!empty($customer_size)){{@num_format($customer_size->waist['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 27px;"><span class="low_waist_span">@if(!empty($customer_size)){{@num_format($customer_size->low_waist['cm'])}}@endif</span></p>
        </div>
        <div class="col-md-4" style="text-align: right; width: 33%; float: left;">
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 123px;"><span class="neck_width_span">@if(!empty($customer_size)){{@num_format($customer_size->neck_width['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 29px;"><span class="arm_hole_span">@if(!empty($customer_size)){{@num_format($customer_size->arm_hole['cm'])}}@endif</span></p>
            <p style="padding: 0px; margin-bottom: 0px; margin-top: 159px;"><span class="wrist_round_span">@if(!empty($customer_size)){{@num_format($customer_size->wrist_round['cm'])}}@endif</span></p>
        </div>
    </div>

</div>
