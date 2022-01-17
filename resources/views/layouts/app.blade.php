<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'POS') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    @include('layouts.partials.css')
</head>

<body onload="myFunction()">
    <div id="loader"></div>
    @if(request()->segment(1) != 'pos')
    @include('layouts.partials.header')
    @endif
    <div class="@if(request()->segment(1) != 'pos') page @else pos-page @endif">
        @include('layouts.partials.sidebar')
        <div style="display:none" id="content" class="animate-bottom">
            @foreach ($errors->all() as $message)
            <div class="alert alert-danger alert-dismissible text-center">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>{{ $message }}
            </div>
            @endforeach
            <input type="hidden" id="__language" value="{{session('language')}}">
            <input type="hidden" id="__decimal" value=".">
            <input type="hidden" id="__currency_precision" value="2">
            <input type="hidden" id="__currency_symbol" value="$">
            <input type="hidden" id="__currency_thousand_separator" value=",">
            <input type="hidden" id="__currency_symbol_placement" value="before">
            <input type="hidden" id="__precision" value="3">
            <input type="hidden" id="__quantity_precision" value="3">
            @yield('content')
        </div>

        @include('layouts.partials.footer')
        <div class="modal fade view_modal no-print" role="dialog" aria-hidden="true"></div>

        @php
        $cash_register = App\Models\CashRegister::where('user_id', Auth::user()->id)->where('status', 'open')->first();
        @endphp
        <input type="hidden" name="is_register_close" id="is_register_close"
            value="@if(!empty($cash_register)){{0}}@else{{1}}@endif">
        <input type="hidden" name="cash_register_id" id="cash_register_id"
            value="@if(!empty($cash_register)){{$cash_register->id}}@endif">
        <div id="closing_cash_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
            class="modal fade text-left">
        </div>
    </div>
    <a target="_blank" href="{{action('ContactUsController@getUserContactUs')}}" id="contact_us_btn"
        style="background-image: url('{{asset('images/handshake.jpg')}}');"
        class="btn no-print">
        <p>@lang('lang.contact_us')</p>
    </a>
    <script type="text/javascript">
        base_path = "{{url('/')}}";
    </script>
    @include('layouts.partials.javascript')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function (jqXHR, settings) {
                if (settings.url.indexOf('http') === -1) {
                    settings.url = base_path + settings.url;
                }
            },
        });

    </script>
    @yield('javascript')

    <script type="text/javascript">
        @if (session('status'))
        swal(@if(session('status.success') == '1')"Success" @else "Error" @endif, "{{ session('status.msg') }}" , @if(session('status.success') == '1')"success" @else "error" @endif);
    @endif
    $(document).ready(function(){
        let cash_register_id = $('#cash_register_id').val();

        if(cash_register_id){
            $('#power_off_btn').removeClass('hide');
        }
    })

    jQuery.validator.setDefaults( {
        errorPlacement: function(error, element) {
            if(element.parent().parent().hasClass('my-group')){
                element.parent().parent().parent().find('.error-msg').html(error)
            }else{
                error.insertAfter( element);
            }
        }
    } );
    $(document).on('click', '.btn-modal', function (e) {
        e.preventDefault();
        var container = $(this).data('container');
        console.log($(this).data('href'));
        $.ajax({
            url: $(this).data('href'),
            dataType: 'html',
            success: function (result) {
                $(container).html(result).modal('show');
            },
        });
    });
    @if(request()->segment(1) != 'pos')
    if ($(window).outerWidth() > 1199) {
        $('nav.side-navbar').removeClass('shrink');
    }
    @endif
    function myFunction() {
        setTimeout(showPage, 150);
    }
    function showPage() {
        document.getElementById("loader").style.display = "none";
        document.getElementById("content").style.display = "block";
    }

      $("div.alert").delay(3000).slideUp(750);

      $(document).on('click', '.delete_item', function(e) {
		e.preventDefault();
        swal({
            title: 'Are you sure?',
            text: "Are you sure You Wanna Delete it?",
            icon: 'warning',
        }).then(willDelete => {
            if (willDelete) {
                var check_password = $(this).data('check_password');
                var href = $(this).data('href');
                var data = $(this).serialize();

                swal({
                    title: 'Please Enter Your Password',
                    content: {
                        element: "input",
                        attributes: {
                            placeholder: "Type your password",
                            type: "password",
                            autocomplete: "off",
                            autofocus: true,
                        },
                    },
                    inputAttributes: {
                        autocapitalize: 'off',
                        autoComplete: 'off',
                    },
                    focusConfirm:true
                }).then((result) => {
                    if (result) {
                    $.ajax({
                        url: check_password,
                        method: 'POST',
                        data: {
                            value: result
                        },
                        dataType: 'json',
                        success: (data) => {

                            if (data.success == true) {
                                swal(
                                'Success',
                                'Correct Password!',
                                'success'
                                );

                                $.ajax({
                                    method: 'DELETE',
                                    url: href,
                                    dataType: 'json',
                                    data: data,
                                    success: function(result) {
                                        if (result.success == true) {
                                            swal(
                                            'Success',
                                            result.msg,
                                            'success'
                                            );
                                            setTimeout(() => {
                                                location.reload();
                                            }, 1500);
                                            location.reload();
                                        }else{
                                            swal(
                                            'Error',
                                            result.msg,
                                            'error'
                                            );
                                        }
                                    },
                                });

                            } else {
                                swal(
                                'Failed!',
                                'Wrong Password!',
                                'error'
                                )

                            }
                        }
                    });
                    }
                });
            }
        });
    });


      $(".daterangepicker-field").daterangepicker({
          callback: function(startDate, endDate, period){
            var start_date = startDate.format('YYYY-MM-DD');
            var end_date = endDate.format('YYYY-MM-DD');
            var title = start_date + ' To ' + end_date;
            $(this).val(title);
            $('input[name="start_date"]').val(start_date);
            $('input[name="end_date"]').val(end_date);
          }
      });
      $('[data-toggle="tooltip"]').tooltip();
      $('.selectpicker').selectpicker({
          style: 'btn-link',
      });


    $(document).on('click', "#power_off_btn", function(e) {
        let cash_register_id = $('#cash_register_id').val();
        let is_register_close = parseInt($('#is_register_close').val());
        if(!is_register_close){
            getClosingModal(cash_register_id);
            return 'Please enter the closing cash';
        }else{
            return;
        }
    });


    function getClosingModal(cash_register_id, type = 'close'){
        $.ajax({
            method: 'get',
            url: '/cash/add-closing-cash/'+cash_register_id,
            data: { type },
            contentType: 'html',
            success: function(result) {
                $('#closing_cash_modal').empty().append(result);
                $('#closing_cash_modal').modal('show');
            },
        });
    }
    $(document).on('click', '#closing-save-btn, #adjust-btn', function(e){
        $('#is_register_close').val(1);
    })
    $(document).on('click', '#logout-btn', function(e){
        let cash_register_id = $('#cash_register_id').val();

        let is_register_close = parseInt($('#is_register_close').val());
        if(!is_register_close){
            getClosingModal(cash_register_id, 'logout');
            return 'Please enter the closing cash';
        }else{
            $('#logout-form').submit();
        }
    })
    $(document).on('click', '.close-btn-add-closing-cash', function(e){
        e.preventDefault()
        $('form#logout-form').submit();
    })
    $(document).on('click', '.notification-list', function(){
        $.ajax({
            method: 'get',
            url: '/notification/notification-seen',
            data: {  },
            success: function(result) {
                if(result){
                    $('.notification-number').addClass('hide')
                }
            },
        });
    })
    $(document).on('click', '.notification_item', function(e){
        e.preventDefault();
        let mark_read_action = $(this).data('mark-read-action');
        let href = $(this).data('href');
        $.ajax({
            method: 'get',
            url: mark_read_action,
            data: {  },
            success: function(result) {

            },
        });
        window.location = href;
    })
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};
    $('input').attr('autocomplete', 'off');
    </script>
</body>

</html>
