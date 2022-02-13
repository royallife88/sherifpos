@extends('layouts.app')
@section('title', __('lang.purchase_return'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.purchase_return')</h4>
                </div>
                <div class="card-body">
                    {!! Form::open(['url' => action('PurchaseReturnController@update', $purchase_return->id), 'method' => 'put', 'files' =>
                    true, 'class' => 'pos-form', 'id' => 'purchase_return_form']) !!}
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="supplier_id">@lang('lang.supplier')</label>
                                    {!! Form::select('supplier_id', $suppliers, $purchase_return->supplier_id, ['class' => 'form-control',
                                    'data-live-search' => 'true', 'placeholder' => __('lang.please_select'),
                                    'required']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="store_id">@lang('lang.store')</label>
                                    {!! Form::select('store_id', $stores, $purchase_return->store_id, ['class' => 'form-control',
                                    'data-live-search' => 'true', 'placeholder' => __('lang.please_select'),
                                    'required']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="search-box input-group">
                                <button type="button" class="btn btn-secondary btn-lg" id="search_button"><i
                                        class="fa fa-search"></i></button>
                                <input type="text" name="search_product" id="search_product"
                                    placeholder="@lang('lang.enter_product_name_to_print_labels')"
                                    class="form-control ui-autocomplete-input" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-12" style="margin-top: 20px ">
                                <div class="table-responsive">
                                    <table id="product_table" style="width: 100% " class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th style="width: 30%">{{__('lang.product')}}</th>
                                                <th style="width: 20%">{{__('lang.returned_quantity')}}</th>
                                                <th style="width: 20%">{{__('lang.price')}}</th>
                                                <th style="width: 20%">{{__('lang.current_stock')}}</th>
                                                <th class="sum" style="width: 10%">{{__('lang.sub_total')}}</th>
                                                <th style="width: 20%"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @include('purchase_return.partials.edit_product_row', ['products' => $purchase_return->purchase_return_lines])
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <th style="text-align: right">@lang('lang.total')</th>
                                                <th></th>
                                                <th><span class="grand_total_span">{{@num_format($purchase_return->final_total)}}</span></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="row" style="display: none;">
                                <div class="col-md-2">
                                    <div class="form-group">

                                        <input type="hidden" id="final_total" name="final_total" value="{{$purchase_return->final_total}}" />
                                        <input type="hidden" id="grand_total" name="grand_total" value="{{$purchase_return->grand_total}}" />

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('payment_status', __('lang.payment_status'), []) !!}
                                    {!! Form::select('payment_status', $payment_status_array, $purchase_return->payment_status, ['class' =>
                                    'form-control', 'placeholder' => __('lang.please_select'), 'required']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            @if(!empty($purchase_return))
                            @if($purchase_return->transaction_payments->count() > 0)
                            @include('add_stock.partials.payment_form', ['payment' =>
                            $purchase_return->transaction_payments->first()])
                            @endif
                            @else
                            @include('add_stock.partials.payment_form')
                            @endif
                        </div>
                    </div>


                    <div class="col-md-3 due_fields hide">
                        <div class="form-group">
                            {!! Form::label('due_date', __('lang.due_date'). ':', []) !!} <br>
                            {!! Form::text('due_date', !empty($purchase_return) ? $purchase_return->due_date : null, ['class' =>
                            'form-control datepicker', 'readonly',
                            'placeholder' => __('lang.due_date')]) !!}
                        </div>
                    </div>

                    <div class="col-md-3 due_fields hide">
                        <div class="form-group">
                            {!! Form::label('notify_before_days', __('lang.notify_before_days'). ':', []) !!} <br>
                            {!! Form::text('notify_before_days', !empty($purchase_return) ? $purchase_return->notify_before_days : null, ['class' =>
                            'form-control',
                            'placeholder' => __('lang.notify_before_days')]) !!}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            {!! Form::label('notes', __('lang.notes'). ':', []) !!} <br>
                            {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => 3]) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="sbumit" class="btn btn-primary save-btn">@lang('lang.save')</button>
                        </div>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- This will be printed -->
<section class="invoice print_section print-only" id="receipt_section"> </section>
@endsection

@section('javascript')
<script src="{{asset('js/purchase_return.js')}}"></script>
<script>
    $(document).ready(function(){
        $('#payment_status').change()
        calculate_sub_totals();
    })
</script>
@endsection
