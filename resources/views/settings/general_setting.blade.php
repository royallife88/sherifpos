@extends('layouts.app')
@section('title', __('lang.general_settings'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.general_settings')</h4>
        </div>
        <div class="card-body">
            {!! Form::open(['url' => action('SettingController@updateGeneralSetting'), 'method' => 'post', 'enctype' =>
            'multipart/form-data']) !!}
            <div class="row">
                <div class="col-md-3">
                    {!! Form::label('site_title', __('lang.site_title'), []) !!}
                    {!! Form::text('site_title',!empty($settings['site_title']) ?
                    $settings['site_title'] : null, ['class' =>
                    'form-control'])
                    !!}
                </div>
                <div class="col-md-3 hide">
                    {!! Form::label('developed_by', __('lang.developed_by'), []) !!}
                    {!! Form::text('developed_by', null, ['class' =>
                    'form-control'])
                    !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('time_format', __('lang.time_format'), []) !!}
                    {!! Form::select('time_format', ['12' => '12 hours', '24' => '24
                    hours'],!empty($settings['time_format']) ?
                    $settings['time_format'] : null, ['class' =>
                    'form-control selectpicker', 'data-live-search' => "true"])
                    !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('timezone', __('lang.timezone'), []) !!}
                    {!! Form::select('timezone', $timezone_list,!empty($settings['timezone']) ?
                    $settings['timezone'] : null, ['class' =>
                    'form-control selectpicker', 'data-live-search' => "true"])
                    !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('language', __('lang.language'), []) !!}
                    {!! Form::select('language', $languages,!empty($settings['language']) ?
                    $settings['language'] : null, ['class' =>
                    'form-control selectpicker', 'data-live-search' => "true"])
                    !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('currency', __('lang.currency'), []) !!}
                    {!! Form::select('currency', $currencies,!empty($settings['currency']) ?
                    $settings['currency'] : null, ['class' =>
                    'form-control selectpicker', 'data-live-search' => "true"])
                    !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('invoice_lang', __('lang.invoice_lang'), []) !!}
                    {!! Form::select('invoice_lang', $languages + ['ar_and_en' => 'Arabic and English'],!empty($settings['invoice_lang']) ?
                    $settings['invoice_lang'] : null, ['class' =>
                    'form-control selectpicker', 'data-live-search' => "true"])
                    !!}
                </div>
            </div>
            <br>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('letter_header', __('lang.letter_header'), []) !!} <br>
                                {!! Form::file('letter_header', null, ['class' =>
                                'form-control'])
                                !!}
                            </div>
                        </div>
                        <div class="col-md-6">
                            @php
                            $letter_header = !empty($settings['letter_header']) ? $settings['letter_header'] : null;
                            @endphp
                            <img style="width: 220px; height: auto" src="{{asset('uploads/'.$letter_header)}}" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('letter_footer', __('lang.letter_footer'), []) !!} <br>
                                {!! Form::file('letter_footer', null, ['class' =>
                                'form-control'])
                                !!}

                            </div>
                        </div>
                        <div class="col-md-6">
                            @php
                            $letter_footer = !empty($settings['letter_footer']) ? $settings['letter_footer'] : null;
                            @endphp
                            <img style="width: 220px; height: auto" src="{{asset('uploads/'.$letter_footer)}}" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('logo', __('lang.logo'), []) !!} <br>
                                {!! Form::file('logo', null, ['class' =>
                                'form-control'])
                                !!}

                            </div>
                        </div>
                        <div class="col-md-6">
                            @php
                            $logo = !empty($settings['logo']) ? $settings['logo'] : null;
                            @endphp
                            <img style="width: 220px; height: auto" src="{{asset('uploads/'.$logo)}}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('help_page_content', __('lang.help_page_content'), []) !!}
                        {!! Form::textarea('help_page_content', !empty($settings['help_page_content']) ? $settings['help_page_content'] : null, ['class' => 'form-control', 'id' => 'help_page_content']) !!}
                    </div>
                </div>
            </div>
            <br>
            <br>
            <br>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">@lang('lang.save')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script>
    $('.selectpicker').selectpicker();
    $(document).ready(function () {
    tinymce.init({
        selector: "#help_page_content",
        height: 130,
        plugins: [
            "advlist autolink lists link charmap print preview anchor textcolor image",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime table contextmenu paste code wordcount",
        ],
        toolbar:
            "insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat",
        branding: false,
    });
});
</script>
@endsection
