@extends('layouts.app')
@section('title', __('lang.contact_us'))
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.contact_us')</h4>
                    </div>
                    <form method="POST" action="{{ action('ContactUsController@sendUserContactUs') }}" enctype="multipart/form-data"
                        id="contact-us-form">
                        <div class="card-body">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input id="phone_number" type="phone_number" name="phone_number"
                                            class="form-control" value=""
                                            placeholder="{{trans('lang.phone_number')}}">
                                        @if($errors->has('phone_number'))
                                        <div class="error">{{ $errors->first('phone_number') }}</div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input id="email" type="email" name="email" class="form-control" value=""
                                            placeholder="{{trans('lang.email')}}">
                                        @if($errors->has('email'))
                                        <div class="error">{{ $errors->first('email') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <textarea name="message" id="message" rows="10"
                                            class="form-control form-control"
                                            placeholder="@lang('lang.your_message')"></textarea>
                                        @if($errors->has('message'))
                                        <div class="error">{{ $errors->first('message') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <input type="file" name="files[]" id="files" multiple>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <button type="submit" name="submit" id="print" style="margin: 10px" value="send"
                                    class="btn btn-primary pull-right btn-flat submit">@lang( 'lang.send' )</button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script>
    @if (session('status'))
        swal(@if(session('status.success') == '1')"Success" @else "Error" @endif, "{{ session('status.msg') }}" , @if(session('status.success') == '1')"success" @else "error" @endif);
    @endif
</script>
@endsection
