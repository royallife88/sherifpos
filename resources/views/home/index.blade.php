@extends('layouts.app')
@section('title', __('lang.dashboard'))

@php
$module_settings = App\Models\System::getProperty('module_settings');
$module_settings = !empty($module_settings) ? json_decode($module_settings, true) : [];
@endphp
@section('content')
    @if (!empty($module_settings['dashboard']))
        <div class="row">
            <div class="container-fluid">
                <div class="col-md-12">
                    <div class="brand-text float-left mt-4">
                        <h3>@lang('lang.welcome') <span>{{ Auth::user()->name }}</span> </h3>
                    </div>
                    @if (auth()->user()->can('superadmin') ||
                        auth()->user()->is_admin ||
                        auth()->user()->can('dashboard.profit.view'))
                        @if (strtolower(session('user.job_title')) != 'deliveryman')
                            <div class="filter-toggle btn-group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label for="store_id"><b>@lang('lang.store')</b></label>
                                        {!! Form::select('store_id', $stores, session('user.is_superadmin') ? null : key($stores), ['class' => 'form-control ', 'data-live-search' => 'true', 'id' => 'store_id', 'placeholder' => __('lang.please_select')]) !!}

                                    </div>
                                    <div class="col-md-3">
                                        <label for="from_date"><b>@lang('lang.from_date')</b></label>
                                        <input type="date" class="form-control filter" name="from_date" id="from_date"
                                            value="{{ date('Y-m-01') }}" placeholder="{{ __('lang.from_date') }}">

                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="start_time"><b>@lang('lang.start_time')</b></label>
                                            {!! Form::text('start_time', null, ['class' => 'form-control time_picker filter', 'id' => 'start_time']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="to_date"><b>@lang('lang.to_date')</b></label>
                                        <input type="date" class="form-control filter" name="to_date" id="to_date"
                                            value="{{ date('Y-m-t') }}" placeholder="{{ __('lang.to_date') }}">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="end_time"><b>@lang('lang.end_time')</b></label>
                                            {!! Form::text('end_time', null, ['class' => 'form-control time_picker filter', 'id' => 'end_time']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        @if (strtolower(session('user.job_title')) != 'deliveryman')
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <div class="row">
                            @if (auth()->user()->can('superadmin') || auth()->user()->is_admin)
                                <!-- Count item widget-->
                                <div class="col-sm-2">
                                    <div class="wrapper count-title text-center">
                                        <div class="icon"><i class="fa fa-cubes" style="color: #498636"></i>
                                        </div>
                                        <div class="name"><strong
                                                style="color: #498636">@lang('lang.current_stock_value')</strong>
                                        </div>
                                        <div class="count-number current_stock_value-data">
                                            {{ @num_format(0) }}</div>
                                    </div>
                                </div>
                            @endif
                            @if (auth()->user()->can('superadmin') ||
                                auth()->user()->is_admin ||
                                auth()->user()->can('dashboard.profit.view'))
                                <!-- Count item widget-->
                                <div class="col-sm-2">
                                    <div class="wrapper count-title text-center">
                                        <div class="icon"><i class="dripicons-graph-bar"
                                                style="color: #733686"></i>
                                        </div>
                                        <div class="name"><strong
                                                style="color: #733686">@lang('lang.revenue')</strong>
                                        </div>
                                        <div class="count-number revenue-data">{{ @num_format(0) }}
                                        </div>
                                    </div>
                                </div>
                                <!-- Count item widget-->
                                <div class="col-sm-2">
                                    <div class="wrapper count-title text-center">
                                        <div class="icon"><i class="dripicons-return" style="color: #ff8952"></i>
                                        </div>
                                        <div class="name"><strong
                                                style="color: #ff8952">@lang('lang.sale_return')</strong>
                                        </div>
                                        <div class="count-number sell_return-data">
                                            {{ @num_format(0) }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <!-- Count item widget-->
                            @if (auth()->user()->can('superadmin') || auth()->user()->is_admin)
                                <div class="col-sm-3">
                                    <div class="wrapper count-title text-center">
                                        <div class="icon"><i class="dripicons-media-loop"
                                                style="color: #00c689"></i>
                                        </div>
                                        <div class="name"><strong
                                                style="color: #00c689">@lang('lang.purchase_return')</strong>
                                        </div>
                                        <div class="count-number purchase_return-data">
                                            {{ @num_format(0) }}</div>
                                    </div>
                                </div>
                            @endif
                            <!-- Count item widget-->
                            @if (auth()->user()->can('superadmin') || auth()->user()->is_admin)
                                <div class="col-sm-3">
                                    <div class="wrapper count-title text-center">
                                        <div class="icon"><i class="dripicons-trophy" style="color: #297ff9"></i>
                                        </div>
                                        <div class="name"><strong
                                                style="color: #297ff9">@lang('lang.profit')</strong>
                                        </div>
                                        <div class="count-number profit-data">{{ @num_format(0) }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-fluid" id="chart_and_table_section">
                {{-- @include('home.partials.chart_and_table') --}}
            </div>
        @endif
    @endif
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            $('#store_id').change();
        });
        $(document).on("change", '.filter, #store_id', function() {
            var store_id = $('select#store_id').val();
            var start_date = $('#from_date').val();
            var start_time = $('#start_time').val();
            var end_time = $('#end_time').val();
            if (!start_date) {
                start_date = 0;
            }
            var end_date = $('#to_date').val();
            if (!end_date) {
                end_date = 0;
            }
            getDashboardData(store_id, start_date, end_date, start_time, end_time);
        });
        $('#start_time, #end_time').focusout(function(event) {
            var store_id = $('#store_id').val();
            var start_date = $('#from_date').val();
            var start_time = $('#start_time').val();
            var end_time = $('#end_time').val();
            if (!start_date) {
                start_date = 0;
            }
            var end_date = $('#to_date').val();
            if (!end_date) {
                end_date = 0;
            }

            getDashboardData(store_id, start_date, end_date, start_time, end_time)
        })

        function getDashboardData(store_id, start_date, end_date, start_time, end_time) {
            console.log(store_id, 'store_id');
            $.ajax({
                method: 'get',
                url: '/get-dashboard-data/' + start_date + '/' + end_date,
                data: {
                    store_id: store_id,
                    start_time: start_time,
                    end_time: end_time,
                },
                // processData: false,
                success: function(result) {
                    console.log(result, 'result');
                    $('.revenue-data').hide();
                    // $(".revenue-data").text(__currency_trans_from_en(result.revenue, false));

                    let currenct_stock_string = '<div>';
                    let revenue_string = '<div>';
                    let sell_return_string = '<div>';
                    let purchase_return_string = '<div>';
                    let profit_string = '<div>';
                    result.forEach(element => {
                        currenct_stock_string += `<h3 class="dashboard_currency currency_total_${element.currency.currency_id}"
                                            data-currency_id="${element.currency.currency_id}"
                                            data-is_default="${element.currency.is_default}"
                                            data-conversion_rate="${element.currency.conversion_rate}"
                                            data-base_conversion="${element.currency.conversion_rate * element.data.current_stock_value}"
                                            data-orig_value="${element.data.current_stock_value}">
                                            <span class="symbol" style="padding-right: 10px;">
                                                ${element.currency.symbol}</span>
                                            <span
                                                class="total">${__currency_trans_from_en(element.data.current_stock_value, false)}</span>
                                        </h3>`;

                        revenue_string += `<h3 class="dashboard_currency currency_total_${element.currency.currency_id}"
                                            data-currency_id="${element.currency.currency_id}"
                                            data-is_default="${element.currency.is_default}"
                                            data-conversion_rate="${element.currency.conversion_rate}"
                                            data-base_conversion="${element.currency.conversion_rate * element.data.revenue}"
                                            data-orig_value="${element.data.revenue}">
                                            <span class="symbol" style="padding-right: 10px;">
                                                ${element.currency.symbol}</span>
                                            <span
                                                class="total">${__currency_trans_from_en(element.data.revenue, false)}</span>
                                        </h3>`;


                        sell_return_string += `<h3 class="dashboard_currency currency_total_${element.currency.currency_id}"
                                            data-currency_id="${element.currency.currency_id}"
                                            data-is_default="${element.currency.is_default}"
                                            data-conversion_rate="${element.currency.conversion_rate}"
                                            data-base_conversion="${element.currency.conversion_rate * element.data.sell_return}"
                                            data-orig_value="${element.data.sell_return}">
                                            <span class="symbol" style="padding-right: 10px;">
                                                ${element.currency.symbol}</span>
                                            <span
                                                class="total">${__currency_trans_from_en(element.data.sell_return, false)}</span>
                                        </h3>`;
                        purchase_return_string += `<h3 class="dashboard_currency currency_total_${element.currency.currency_id}"
                                            data-currency_id="${element.currency.currency_id}"
                                            data-is_default="${element.currency.is_default}"
                                            data-conversion_rate="${element.currency.conversion_rate}"
                                            data-base_conversion="${element.currency.conversion_rate * element.data.purchase_return}"
                                            data-orig_value="${element.data.purchase_return}">
                                            <span class="symbol" style="padding-right: 10px;">
                                                ${element.currency.symbol}</span>
                                            <span
                                                class="total">${__currency_trans_from_en(element.data.purchase_return, false)}</span>
                                        </h3>`;
                        profit_string += `<h3 class="dashboard_currency currency_total_${element.currency.currency_id}"
                                            data-currency_id="${element.currency.currency_id}"
                                            data-is_default="${element.currency.is_default}"
                                            data-conversion_rate="${element.currency.conversion_rate}"
                                            data-base_conversion="${element.currency.conversion_rate * element.data.profit}"
                                            data-orig_value="${element.data.profit}">
                                            <span class="symbol" style="padding-right: 10px;">
                                                ${element.currency.symbol}</span>
                                            <span
                                                class="total">${__currency_trans_from_en(element.data.profit, false)}</span>
                                        </h3>`;
                    });
                    currenct_stock_string += `</div>`;
                    revenue_string += `</div>`;
                    sell_return_string += `</div>`;
                    purchase_return_string += `</div>`;
                    profit_string += `</div>`;
                    $(".revenue-data").html(revenue_string);


                    $('.revenue-data').show(500);

                    $('.current_stock_value-data').hide();
                    $(".current_stock_value-data").html(currenct_stock_string);
                    $('.current_stock_value-data').show(500);

                    $('.sell_return-data').hide();
                    $(".sell_return-data").html(sell_return_string);
                    $('.sell_return-data').show(500);

                    $('.purchase_return-data').hide();
                    $(".purchase_return-data").html(purchase_return_string);
                    $('.purchase_return-data').show(500);

                    $('.profit-data').hide();
                    $(".profit-data").html(profit_string);
                    $('.profit-data').show(500);
                },
            });
            getChartAndTableSection(start_date, end_date, store_id);
        }
        @if (auth()->user()->can('superadmin') || auth()->user()->is_admin)
            function getChartAndTableSection(start_date, end_date, store_id) {
                $("#chart_and_table_section").css("text-align", "center");
                $("#chart_and_table_section").html(
                    `<div><i class="fa fa-circle-o-notch fa-spin fa-fw"></i></div>`
                );
                $.ajax({
                    method: 'get',
                    url: '/get-chart-and-table-section',
                    data: {
                        start_date,
                        end_date,
                        store_id
                    },
                    success: function(result) {
                        if (result) {
                            $('#chart_and_table_section').html(result);
                            initializeChart()
                        }
                    },
                });
            }
        @endif

        function initializeChart() {
            var brandPrimary;
            var brandPrimaryRgba;

            // ------------------------------------------------------- //
            // Line Chart
            // ------------------------------------------------------ //
            var CASHFLOW = $("#cashFlow");
            if (CASHFLOW.length > 0) {
                var recieved = CASHFLOW.data("recieved");
                brandPrimary = CASHFLOW.data("color");
                brandPrimaryRgba = CASHFLOW.data("color_rgba");
                var sent = CASHFLOW.data("sent");
                var month = CASHFLOW.data("month");
                var label1 = CASHFLOW.data("label1");
                var label2 = CASHFLOW.data("label2");
                var cashFlow_chart = new Chart(CASHFLOW, {
                    type: "line",
                    data: {
                        labels: [
                            month[0] ?? '',
                            month[1] ?? '',
                            month[2] ?? '',
                            month[3] ?? '',
                            month[4] ?? '',
                            month[5] ?? '',
                            month[6] ?? '',
                        ],
                        datasets: [{
                                label: label1,
                                fill: true,
                                lineTension: 0.3,
                                backgroundColor: "transparent",
                                borderColor: brandPrimary,
                                borderCapStyle: "butt",
                                borderDash: [],
                                borderDashOffset: 0.0,
                                borderJoinStyle: "miter",
                                borderWidth: 3,
                                pointBorderColor: brandPrimary,
                                pointBackgroundColor: "#fff",
                                pointBorderWidth: 5,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: brandPrimary,
                                pointHoverBorderColor: "rgba(220,220,220,1)",
                                pointHoverBorderWidth: 2,
                                pointRadius: 1,
                                pointHitRadius: 10,
                                data: [
                                    recieved[0] ?? 0,
                                    recieved[1] ?? 0,
                                    recieved[2] ?? 0,
                                    recieved[3] ?? 0,
                                    recieved[4] ?? 0,
                                    recieved[5] ?? 0,
                                    recieved[6] ?? 0,
                                ],
                                spanGaps: false,
                            },
                            {
                                label: label2,
                                fill: true,
                                lineTension: 0.3,
                                backgroundColor: "transparent",
                                borderColor: "rgba(255, 137, 82, 1)",
                                borderCapStyle: "butt",
                                borderDash: [],
                                borderDashOffset: 0.0,
                                borderJoinStyle: "miter",
                                borderWidth: 3,
                                pointBorderColor: "#ff8952",
                                pointBackgroundColor: "#fff",
                                pointBorderWidth: 5,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: "#ff8952",
                                pointHoverBorderColor: "rgba(220,220,220,1)",
                                pointHoverBorderWidth: 2,
                                pointRadius: 1,
                                pointHitRadius: 10,
                                data: [
                                    sent[0],
                                    sent[1],
                                    sent[2],
                                    sent[3],
                                    sent[4],
                                    sent[5],
                                    sent[6],
                                ],
                                spanGaps: false,
                            },
                        ],
                    },
                });
            }

            var SALECHART = $("#saleChart");

            if (SALECHART.length > 0) {
                var yearly_sale_amount = SALECHART.data("sale_chart_value");
                var yearly_purchase_amount = SALECHART.data("purchase_chart_value");
                var label1 = SALECHART.data("label1");
                var label2 = SALECHART.data("label2");
                var saleChart = new Chart(SALECHART, {
                    type: "bar",
                    data: {
                        labels: [
                            "January",
                            "February",
                            "March",
                            "April",
                            "May",
                            "June",
                            "July",
                            "August",
                            "September",
                            "October",
                            "November",
                            "December",
                        ],
                        datasets: [{
                                label: label1,
                                backgroundColor: [
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                    brandPrimaryRgba,
                                ],
                                borderColor: [
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                    brandPrimary,
                                ],
                                borderWidth: 1,
                                data: [
                                    yearly_purchase_amount[0],
                                    yearly_purchase_amount[1],
                                    yearly_purchase_amount[2],
                                    yearly_purchase_amount[3],
                                    yearly_purchase_amount[4],
                                    yearly_purchase_amount[5],
                                    yearly_purchase_amount[6],
                                    yearly_purchase_amount[7],
                                    yearly_purchase_amount[8],
                                    yearly_purchase_amount[9],
                                    yearly_purchase_amount[10],
                                    yearly_purchase_amount[11],
                                    0,
                                ],
                            },
                            {
                                label: label2,
                                backgroundColor: [
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                ],
                                borderColor: [
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                    "rgba(255, 137, 82, 1)",
                                ],
                                borderWidth: 1,
                                data: [
                                    yearly_sale_amount[0],
                                    yearly_sale_amount[1],
                                    yearly_sale_amount[2],
                                    yearly_sale_amount[3],
                                    yearly_sale_amount[4],
                                    yearly_sale_amount[5],
                                    yearly_sale_amount[6],
                                    yearly_sale_amount[7],
                                    yearly_sale_amount[8],
                                    yearly_sale_amount[9],
                                    yearly_sale_amount[10],
                                    yearly_sale_amount[11],
                                    0,
                                ],
                            },
                        ],
                    },
                });
            }

            var BESTSELLER = $("#bestSeller");

            if (BESTSELLER.length > 0) {
                var sold_qty = BESTSELLER.data("sold_qty");
                brandPrimary = BESTSELLER.data("color");
                brandPrimaryRgba = BESTSELLER.data("color_rgba");
                var product_info = BESTSELLER.data("product");
                var bestSeller = new Chart(BESTSELLER, {
                    type: "bar",
                    data: {
                        labels: [product_info[0], product_info[1], product_info[2]],
                        datasets: [{
                            label: "Sale Qty",
                            backgroundColor: [
                                brandPrimaryRgba,
                                brandPrimaryRgba,
                                brandPrimaryRgba,
                                brandPrimaryRgba,
                            ],
                            borderColor: [
                                brandPrimary,
                                brandPrimary,
                                brandPrimary,
                                brandPrimary,
                            ],
                            borderWidth: 1,
                            data: [sold_qty[0], sold_qty[1], sold_qty[2], 0],
                        }, ],
                    },
                });
            }

            var PIECHART = $("#pieChart");
            if (PIECHART.length > 0) {
                var brandPrimary = PIECHART.data("color");
                var brandPrimaryRgba = PIECHART.data("color_rgba");
                var price = PIECHART.data("price");
                var cost = PIECHART.data("cost");
                var label1 = PIECHART.data("label1");
                var label2 = PIECHART.data("label2");
                var label3 = PIECHART.data("label3");
                var myPieChart = new Chart(PIECHART, {
                    type: "pie",
                    data: {
                        labels: [label1, label2, label3],
                        datasets: [{
                            data: [price, cost, price - cost],
                            borderWidth: [1, 1, 1],
                            backgroundColor: [
                                brandPrimary,
                                "#ff8952",
                                "#858c85",
                            ],
                            hoverBackgroundColor: [
                                brandPrimaryRgba,
                                "rgba(255, 137, 82, 0.8)",
                                "rgb(133, 140, 133, 0.8)",
                            ],
                            hoverBorderWidth: [4, 4, 4],
                            hoverBorderColor: [
                                brandPrimaryRgba,
                                "rgba(255, 137, 82, 0.8)",
                                "rgb(133, 140, 133, 0.8)",
                            ],
                        }, ],
                    },
                    options: {
                        //rotation: -0.7*Math.PI
                    },
                });
            }

            var TRANSACTIONCHART = $("#transactionChart");
            if (TRANSACTIONCHART.length > 0) {
                brandPrimary = TRANSACTIONCHART.data("color");
                brandPrimaryRgba = TRANSACTIONCHART.data("color_rgba");
                var revenue = TRANSACTIONCHART.data("revenue");
                var purchase = TRANSACTIONCHART.data("purchase");
                var expense = TRANSACTIONCHART.data("expense");
                var label1 = TRANSACTIONCHART.data("label1");
                var label2 = TRANSACTIONCHART.data("label2");
                var label3 = TRANSACTIONCHART.data("label3");
                var myTransactionChart = new Chart(TRANSACTIONCHART, {
                    type: "doughnut",
                    data: {
                        labels: [label1, label2, label3],
                        datasets: [{
                            data: [purchase, revenue, expense],
                            borderWidth: [1, 1, 1],
                            backgroundColor: [
                                brandPrimary,
                                "#ff8952",
                                "#858c85",
                            ],
                            hoverBackgroundColor: [
                                brandPrimaryRgba,
                                "rgba(255, 137, 82, 0.8)",
                                "rgb(133, 140, 133, 0.8)",
                            ],
                            hoverBorderWidth: [4, 4, 4],
                            hoverBorderColor: [
                                brandPrimaryRgba,
                                "rgba(255, 137, 82, 0.8)",
                                "rgb(133, 140, 133, 0.8)",
                            ],
                        }, ],
                    },
                });
            }
        }
    </script>
@endsection
