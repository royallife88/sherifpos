@extends('layouts.app')
@section('title', __('lang.statement'))

@section('content')
    <div class="container-fluid">

        <div class="col-md-12  no-print">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                    {!! Form::text('start_date', request()->start_date, ['class' => 'form-control sale_filter']) !!}
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                    {!! Form::text('end_date', request()->end_date, ['class' => 'form-control sale_filter']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="button"
                                    class="btn btn-danger mt-4 ml-2 clear_filter">@lang('lang.clear_filter')</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="safe_statement_table">
                            <thead>
                                <tr>
                                    <th>@lang('lang.date')</th>
                                    <th>@lang('lang.source')</th>
                                    <th>@lang('lang.job')</th>
                                    <th>@lang('lang.store')</th>
                                    <th>@lang('lang.comments')</th>
                                    <th>@lang('lang.amount')</th>
                                    <th class="balance">@lang('lang.balance')</th>
                                    <th>@lang('lang.created_by')</th>
                                    <th>@lang('lang.date_and_time')</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <th>@lang('lang.total')</th>
                                    <td></td>
                                    <td class="footer_balance">{{ @num_format($balance) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            safe_statement_table = $("#safe_statement_table").DataTable({
                lengthChange: false,
                paging: false,
                searching: false,
                info: false,
                bAutoWidth: false,
                language: {
                    url: dt_lang_url,
                },
                dom: "lBfrtip",
                stateSave: true,
                buttons: buttons,
                processing: true,
                serverSide: true,
                ordering: false,
                aaSorting: [
                    // [0, "desc"]
                ],
                initComplete: function() {
                    $(this.api().table().container()).find('input').parent().wrap('<form>').parent()
                        .attr('autocomplete', 'off');
                },
                ajax: {
                    url: "/money-safe-transfer/get-statement/{{ $money_safe->id }}",
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    },
                },
                columns: [{
                        data: "transaction_date",
                        name: "transaction_date"
                    },
                    {
                        data: "source",
                        name: "source"
                    },
                    {
                        data: "job_type",
                        name: "job_type.job_title"
                    },
                    {
                        data: "store_name",
                        name: "stores.name"
                    },
                    {
                        data: "comments",
                        name: "comments"
                    },
                    {
                        data: "amount",
                        name: "amount"
                    },
                    {
                        data: "balance",
                        name: "balance"
                    },
                    {
                        data: "created_by_user",
                        name: "created_by_user.name"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                ],
                createdRow: function(row, data, dataIndex) {},
                footerCallback: function(row, data, start, end, display) {
                    var intVal = function(i) {
                        return typeof i === "string" ?
                            i.replace(/[\$,]/g, "") * 1 :
                            typeof i === "number" ?
                            i :
                            0;
                    };
                    var balance = 0;
                    if (this.api().row(':last').data()) {
                        let last_balance = this.api().row(':last').data().balance;
                        balance = $(last_balance).text();
                    }
                    $('.footer_balance').html(
                        __currency_trans_from_en(balance, false)
                    );
                },
            });
            $(document).on('click', '.clear_filter', function() {
                $('.sale_filter').val('');
                $('.sale_filter').selectpicker('refresh');
                safe_statement_table.ajax.reload();
            });
            $(document).on('change', '.sale_filter', function() {
                safe_statement_table.ajax.reload();
            });
        })
    </script>
@endsection
