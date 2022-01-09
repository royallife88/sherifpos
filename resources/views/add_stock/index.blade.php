@extends('layouts.app')
@section('title', __('lang.add_stock'))

@section('content')
<section class="">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <form action="">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('store_id', __('lang.store'), []) !!}
                                {!! Form::select('store_id', $stores, request()->store_id, ['class' =>
                                'form-control filters', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('supplier_id', __('lang.supplier'), []) !!}
                                {!! Form::select('supplier_id', $suppliers, request()->supplier_id, ['class' =>
                                'form-control filters', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('created_by', __('lang.added_by'), []) !!}
                                {!! Form::select('created_by', $users, request()->created_by, ['class' =>
                                'form-control filters', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('product_id', __('lang.product'), []) !!}
                                {!! Form::select('product_id', $products, request()->product_id, ['class' =>
                                'form-control filters', 'placeholder' => __('lang.all'),'data-live-search'=>"true"]) !!}
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('start_date', __('lang.start_date'), []) !!}
                                {!! Form::text('start_date', request()->start_date, ['class' => 'form-control ', 'id' => 'start_date']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                {!! Form::label('end_date', __('lang.end_date'), []) !!}
                                {!! Form::text('end_date', request()->end_date, ['class' => 'form-control ', 'id' => 'end_date']) !!}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="button"
                                class="btn btn-danger clear_filters mt-2 ml-2">@lang('lang.clear_filter')</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table" id="add_stock_table">
            <thead>
                <tr>
                    <th>@lang('lang.po_ref_no')</th>
                    <th>@lang('lang.invoice_no')</th>
                    <th>@lang('lang.date_and_time')</th>
                    <th>@lang('lang.invoice_date')</th>
                    <th>@lang('lang.supplier')</th>
                    <th>@lang('lang.created_by')</th>
                    <th class="sum">@lang('lang.value')</th>
                    <th class="sum">@lang('lang.paid_amount')</th>
                    <th class="sum">@lang('lang.pending_amount')</th>
                    <th>@lang('lang.due_date')</th>
                    <th class="notexport">@lang('lang.action')</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" style="text-align: right">@lang('lang.total')</th>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready( function(){
        add_stock_table = $('#add_stock_table').DataTable({
            lengthChange: true,
            paging: true,
            info: false,
            bAutoWidth: false,
            order: [],
            language: {
                url: dt_lang_url,
            },
            lengthMenu: [
                [10, 25, 50, 75, 100, 200, 500, -1],
                [10, 25, 50, 75, 100, 200, 500, "All"],
            ],
            dom: "lBfrtip",
            buttons: buttons,
            processing: true,
            serverSide: true,
            aaSorting: [[2, 'desc']],
             "ajax": {
                "url": "/add-stock",
                "data": function ( d ) {
                    d.store_id = $('#store_id').val();
                    d.supplier_id = $('#supplier_id').val();
                    d.created_by = $('#created_by').val();
                    d.product_id = $('#product_id').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columnDefs: [ {
                "targets": [0, 3],
                "orderable": false,
                "searchable": false
            } ],
            columns: [
                { data: 'po_no', name: 'po_no'  },
                { data: 'invoice_no', name: 'invoice_no'},
                { data: 'created_at', name: 'created_at'  },
                { data: 'transaction_date', name: 'transaction_date'  },
                { data: 'supplier', name: 'suppliers.name'  },
                { data: 'created_by', name: 'users.name'},
                { data: 'final_total', name: 'final_total'  },
                { data: 'paid_amount', name: 'paid_amount'  },
                { data: 'due', name: 'due'  },
                { data: 'due_date', name: 'due_date'},
                { data: 'action', name: 'action'},

            ],
            createdRow: function( row, data, dataIndex ) {

            },
            fnDrawCallback: function(oSettings) {
            },
        });
        $(document).on('click', '.filters', function(){
            add_stock_table.ajax.reload();
        })
        $('#end_date, #start_date').change( function(){
            add_stock_table.ajax.reload();
        })
    });


    $(document).on('click', '.clear_filters', function(){
        $('.filters').val('');
        $('.filters').selectpicker('refresh')
        add_stock_table.ajax.reload();
    })
</script>
@endsection
