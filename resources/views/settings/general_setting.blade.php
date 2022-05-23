@extends('layouts.app')
@section('title', __('lang.general_settings'))

@section('content')
    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.general_settings')</h4>
            </div>
            <div class="card-body">
                {!! Form::open(['url' => action('SettingController@updateGeneralSetting'), 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
                <div class="row">
                    <div class="col-md-3">
                        {!! Form::label('site_title', __('lang.site_title'), []) !!}
                        {!! Form::text('site_title', !empty($settings['site_title']) ? $settings['site_title'] : null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-3 hide">
                        {!! Form::label('developed_by', __('lang.developed_by'), []) !!}
                        {!! Form::text('developed_by', null, ['class' => 'form-control']) !!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('time_format', __('lang.time_format'), []) !!}
                        {!! Form::select('time_format', ['12' => '12 hours', '24' => '24 hours'], !empty($settings['time_format']) ? $settings['time_format'] : null, ['class' => 'form-control selectpicker', 'data-live-search' => 'true']) !!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('timezone', __('lang.timezone'), []) !!}
                        {!! Form::select('timezone', $timezone_list, !empty($settings['timezone']) ? $settings['timezone'] : null, ['class' => 'form-control selectpicker', 'data-live-search' => 'true']) !!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('language', __('lang.language'), []) !!}
                        {!! Form::select('language', $languages, !empty($settings['language']) ? $settings['language'] : null, ['class' => 'form-control selectpicker', 'data-live-search' => 'true']) !!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('currency', __('lang.currency'), []) !!}
                        {!! Form::select('currency', $currencies, !empty($settings['currency']) ? $settings['currency'] : null, ['class' => 'form-control selectpicker', 'data-live-search' => 'true']) !!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('invoice_lang', __('lang.invoice_lang'), []) !!}
                        {!! Form::select('invoice_lang', $languages + ['ar_and_en' => 'Arabic and English'], !empty($settings['invoice_lang']) ? $settings['invoice_lang'] : null, ['class' => 'form-control selectpicker', 'data-live-search' => 'true']) !!}
                    </div>
                    <div class="col-md-3">
                        {!! Form::label('invoice_terms_and_conditions', __('lang.tac_to_be_printed'), []) !!}
                        {!! Form::select('invoice_terms_and_conditions', $terms_and_conditions, !empty($settings['invoice_terms_and_conditions']) ? $settings['invoice_terms_and_conditions'] : null, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select')]) !!}
                    </div>
                    @if (session('system_mode') != 'restaurant')
                        <div class="col-md-3">
                            {!! Form::label('default_purchase_price_percentage', __('lang.default_purchase_price_percentage'), []) !!} <i class="dripicons-question" data-toggle="tooltip"
                                title="@lang('lang.default_purchase_price_percentage_info')"></i>
                            {!! Form::number('default_purchase_price_percentage', !empty($settings['default_purchase_price_percentage']) ? $settings['default_purchase_price_percentage'] : null, ['class' => 'form-control']) !!}
                        </div>
                    @else
                        <div class="col-md-3">
                            {!! Form::label('default_profit_percentage', __('lang.default_profit_percentage'), []) !!} <small>@lang('lang.without_%_symbol')</small>
                            {!! Form::number('default_profit_percentage', !empty($settings['default_profit_percentage']) ? $settings['default_profit_percentage'] : null, ['class' => 'form-control']) !!}
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="i-checks">
                            <input id="show_the_window_printing_prompt" name="show_the_window_printing_prompt"
                                type="checkbox" @if (!empty($settings['show_the_window_printing_prompt']) && $settings['show_the_window_printing_prompt'] == '1') checked @endif value="1"
                                class="form-control-custom">
                            <label for="show_the_window_printing_prompt"><strong>
                                    @lang('lang.show_the_window_printing_prompt')
                                </strong></label>
                        </div>
                    </div>
                    @if (session('system_mode') == 'restaurant')
                        <div class="col-md-3">
                            <div class="i-checks">
                                <input id="enable_the_table_reservation" name="enable_the_table_reservation" type="checkbox"
                                    @if (!empty($settings['enable_the_table_reservation']) && $settings['enable_the_table_reservation'] == '1') checked @endif value="1" class="form-control-custom">
                                <label for="enable_the_table_reservation"><strong>
                                        @lang('lang.enable_the_table_reservation')
                                    </strong></label>
                            </div>
                        </div>
                    @endif
                </div>
                <br>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('letter_header', __('lang.letter_header'), []) !!} @if (!empty($settings['letter_header']))
                                        <button class="btn btn-xs btn-danger remove_image" data-type="letter_header"><i
                                                class="fa fa-times"></i></button>
                                    @endif <br>
                                    {!! Form::file('letter_header', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $letter_header = !empty($settings['letter_header']) ? $settings['letter_header'] : null;
                                @endphp
                                <img style="width: 220px; height: auto" src="{{ asset('uploads/' . $letter_header) }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('letter_footer', __('lang.letter_footer'), []) !!} @if (!empty($settings['letter_footer']))
                                        <button class="btn btn-xs btn-danger remove_image" data-type="letter_footer"><i
                                                class="fa fa-times"></i></button>
                                    @endif
                                    <br>
                                    {!! Form::file('letter_footer', null, ['class' => 'form-control']) !!}

                                </div>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $letter_footer = !empty($settings['letter_footer']) ? $settings['letter_footer'] : null;
                                @endphp
                                <img style="width: 220px; height: auto" src="{{ asset('uploads/' . $letter_footer) }}"
                                    alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('logo', __('lang.logo'), []) !!} @if (!empty($settings['logo']))
                                        <button class="btn btn-xs btn-danger remove_image" data-type="logo"><i
                                                class="fa fa-times"></i></button>
                                    @endif
                                    <br>
                                    {!! Form::file('logo', null, ['class' => 'form-control']) !!}

                                </div>
                            </div>
                            <div class="col-md-6">
                                @php
                                    $logo = !empty($settings['logo']) ? $settings['logo'] : null;
                                @endphp
                                <img style="width: 220px; height: auto" src="{{ asset('uploads/' . $logo) }}" alt="">
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
        $(document).ready(function() {
            tinymce.init({
                selector: "#help_page_content",
                height: 130,
                plugins: [
                    "advlist autolink lists link charmap print preview anchor textcolor image",
                    "searchreplace visualblocks code fullscreen",
                    "insertdatetime table contextmenu paste code wordcount",
                ],
                toolbar: "insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat",
                branding: false,
            });
        });
        $(document).on('click', '.remove_image', function() {
            var type = $(this).data('type');
            $.ajax({
                url: "/settings/remove-image/" + type,
                type: "POST",
                success: function(response) {
                    if (response.status == 'success') {
                        location.reload();
                    }
                }
            });
        });
    </script>
@endsection
