<?php $general_setting = DB::table('general_settings')->find(1); ?>
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
    @include('layouts.partials.sidebar')
    @include('layouts.partials.header')

    <div class="page">
        <div style="display:none" id="content" class="animate-bottom">
            @foreach ($errors->all() as $message)
            <div class="alert alert-danger alert-dismissible text-center">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>{{ $message }}</div>
            @endforeach
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
        <div class="modal fade view_modal" role="dialog" aria-hidden="true"></div>
    </div>
    @include('layouts.partials.javascript')
    <script type="text/javascript">
        base_path = "{{url('/')}}";
    </script>
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
    if ($(window).outerWidth() > 1199) {
        $('nav.side-navbar').removeClass('shrink');
    }
    function myFunction() {
        setTimeout(showPage, 150);
    }
    function showPage() {
        document.getElementById("loader").style.display = "none";
        document.getElementById("content").style.display = "block";
    }

      $("div.alert").delay(3000).slideUp(750);

      $(document).on('click', 'a.delete_item', function(e) {
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
                        },
                    },
                    inputAttributes: {
                        autocapitalize: 'off'
                    },
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
                                        }
                                        setTimeout(() => {
                                            location.reload();
                                        }, 1500);
                                        location.reload();
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
      $('.datepicker').datepicker();
      $('.selectpicker').selectpicker({
          style: 'btn-link',
      });

      $(document).ready( function () {
        table = $('.dataTable').DataTable({
            "paging":   false,
            "info":     false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':visible:not(.notexport)'
                    }
                },
                {
                    extend: 'excel',
                    exportOptions: {
                        columns: ':visible:not(.notexport)'
                    }
                },
                {
                    extend: 'csvHtml5',
                    exportOptions: {
                        columns: ':visible:not(.notexport)'
                    }
                },
                {
                    extend: 'pdfHtml5',
                    exportOptions: {
                        columns: ':visible:not(.notexport)'
                    }
                },
                {
                    extend: 'copyHtml5',
                    exportOptions: {
                        columns: ':visible:not(.notexport)'
                    }
                },
                {
                    extend: 'colvis',
                columns: ':gt(0)'

                }
            ]
        });
    });
    </script>
</body>

</html>
