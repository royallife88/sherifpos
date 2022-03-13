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
                    {!! Form::open(['url' => action('EmailController@saveSetting'), 'method' => 'post', 'id' =>
                    'sms_form'
                    ]) !!}
                    <div class="col-md-12">
                        <div class=" row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="sender_email">{{__('lang.email')}}:</label>
                                    <input type="text" class="form-control" id="sender_email" name="sender_email"
                                        required
                                        value="@if(!empty($settings['sender_email'])){{$settings['sender_email']}}@endif">
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
