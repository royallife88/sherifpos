@extends('layouts.app')
@section('title', __('lang.settings'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.settings')</h4>
                    </div>
                    {!! Form::open(['url' => action('SmsController@saveSetting'), 'method' => 'post', 'id' => 'sms_form'
                    ]) !!}
                    <div class="col-md-12">
                        <div class=" row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sms_username">{{__('lang.username')}}:</label>
                                    <input type="text" class="form-control" id="sms_username" name="sms_username" required
                                        value="@if(!empty($settings['sms_username'])){{$settings['sms_username']}}@endif">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sms_password">{{__('lang.password')}}:</label>
                                    <input type="text" class="form-control" id="sms_password" name="sms_password" required
                                        value="@if(!empty($settings['sms_password'])){{$settings['sms_password']}}@endif">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sms_sender_name">{{__('lang.sender_name')}}:</label>
                                    <input type="text" class="form-control" id="sms_sender_name" name="sms_sender_name" required
                                        value="@if(!empty($settings['sms_sender_name'])){{$settings['sms_sender_name']}}@endif">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <button type="submit" name="submit" id="print" style="margin: 10px" value="save"
                            class="btn btn-primary pull-right btn-flat submit">@lang( 'lang.save' )</button>

                    </div>
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">

</script>
@endsection
