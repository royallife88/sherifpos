@extends('layouts.app')
@section('title', __('lang.daily_sales_summary'))

@section('content')
    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4>@lang('lang.daily_sales_summary')</h4>
            </div>
            @if (session('user.is_superadmin') || auth()->user()->can('reports.sales_per_employee.view'))
                <form action="">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('start_date', __('lang.date'), []) !!}
                                    {!! Form::text('start_date', date('Y-m-d'), ['class' => 'form-control filter start_date']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('start_time', __('lang.start_time'), []) !!}
                                    {!! Form::text('start_time', request()->start_time, ['class' => 'form-control time_picker filter']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('store_id', __('lang.store'), []) !!}
                                    {!! Form::select('store_id[]', $stores, request()->store_id, ['class' => 'form-control selectpicker filter', 'multiple', 'id' => 'store_id', 'data-actions-box' => 'true', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('store_pos_id', __('lang.pos'), []) !!}
                                    {!! Form::select('store_pos_id[]', $store_pos, request()->store_pos_id, ['class' => 'form-control selectpicker filter', 'multiple', 'id' => 'store_pos_id', 'data-actions-box' => 'true', 'placeholder' => __('lang.all'), 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button"
                                    class="btn btn-danger mt-2 ml-2 clear_filter">@lang('lang.clear_filter')</button>
                            </div>
                        </div>
                    </div>
                </form>
            @endif
            <div class="card-body">
                <div class="col-md-12" id="table_div">

                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).on('focusout', '#start_time', function() {
            getDailySaleReport();
        })
        $(document).on('click', '.clear_filter', function() {
            $('.selectpicker').val('');
            $('.selectpicker').selectpicker('refresh');
            $('.date').val("{{ date('Y-m-d') }}");
            $('.time_picker').val("");
            getDailySaleReport();
        })
        $(document).on('change', '.filter', function() {
            getDailySaleReport();
        })
        $(document).ready(function() {
            getDailySaleReport();
        })

        function getDailySaleReport() {
            $("#table_div").html(
                `<div class="text-center"><i class="fa fa-circle-o-notch fa-spin fa-fw"></i></div>`
            );

            $.ajax({
                method: 'get',
                url: '/report/daily-sales-summary',
                data: {
                    start_date: $('#start_date').val(),
                    store_id: $('#store_id').val(),
                    store_pos_id: $('#store_pos_id').val(),
                    start_time: $('#start_time').val(),
                },
                contentType: 'html',
                success: function(result) {
                    $('#table_div').html(result);
                },
            });
        }

        $(document).on("change", "#store_id", function() {

            if ($("#store_id").val()) {
                $.ajax({
                    method: "get",
                    url: "/report/get-pos-details-by-store",
                    data: {
                        store_ids: $("#store_id").val()
                    },
                    success: function(result) {
                        $("#store_pos_id").html(result);
                        $("#store_pos_id").selectpicker("refresh");
                        $("#store_pos_id").selectpicker("val", result.id);
                    },
                });
            }
        });

    </script>

@endsection
