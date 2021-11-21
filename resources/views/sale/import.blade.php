@extends('layouts.app')
@section('title', __('lang.import_sale'))

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.import_sale')</h4>
                </div>
                <div class="card-body">
                    {!! Form::open(['url' => action('SellController@saveImport'), 'method' => 'post', 'files' =>
                    true, 'class' => 'pos-form', 'id' => 'import_sale_form']) !!}
                    <input type="hidden" name="store_id" id="store_id" value="{{$store_pos->store_id}}">
                    <input type="hidden" name="default_customer_id" id="default_customer_id"
                        value="@if(!empty($walk_in_customer)){{$walk_in_customer->id}}@endif">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                    <div class="input-group my-group">
                                        {!! Form::select('customer_id', $customers,
                                        !empty($walk_in_customer) ? $walk_in_customer->id : null, ['class' =>
                                        'selectpicker form-control', 'data-live-search'=>"true",
                                        'style' =>'width: 80%' , 'id' => 'customer_id']) !!}
                                        <span class="input-group-btn">
                                            @can('customer_module.customer.create_and_edit')
                                            <button class="btn-modal btn btn-default bg-white btn-flat"
                                                data-href="{{action('CustomerController@create')}}?quick_add=1"
                                                data-container=".view_modal"><i
                                                    class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                            @endcan
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            {!! Form::label('file', __('lang.file'), []) !!} <br>
                                            {!! Form::file('file', []) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <a class="btn btn-block btn-primary"
                                            href="{{asset('sample_files/sales_import.csv')}}"><i
                                                class="fa fa-download"></i>@lang('lang.download_sample_file')</a>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: none;">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <input type="hidden" id="final_total" name="final_total" />
                                        <input type="hidden" id="grand_total" name="grand_total" />
                                        <input type="hidden" id="gift_card_id" name="gift_card_id" />
                                        <input type="hidden" id="coupon_id" name="coupon_id">
                                        <input type="hidden" id="total_tax" name="total_tax" value="0.00">
                                        <input type="hidden" id="is_direct_sale" name="is_direct_sale" value="1">
                                        <input type="hidden" name="discount_amount" id="discount_amount">
                                        <input type="hidden" id="store_pos_id" name="store_pos_id"
                                            value="{{$store_pos->id}}" />
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="tax_id">@lang('lang.tax')</label>
                                    <select class="form-control" name="tax_id" id="tax_id">
                                        <option value="" selected>No Tax</option>
                                        @foreach ($taxes as $tax)
                                        <option data-rate="{{$tax->rate}}" value="{{$tax->id}}">{{$tax->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_type', __( 'lang.discount_type' ) . ':*') !!}
                                    {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' =>
                                    'Percentage'],
                                    'fixed', ['class' =>
                                    'form-control', 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('discount_value', __( 'lang.discount_value' ) . ':*') !!}
                                    {!! Form::text('discount_value', null, ['class' => 'form-control', 'placeholder' =>
                                    __(
                                    'lang.discount_value' ),
                                    'required' ]);
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::label('status', __( 'lang.status' ) . ':*') !!}
                                    {!! Form::select('status', ['final' => 'Completed', 'pending' => 'Pending'],
                                    'final', ['class' =>
                                    'form-control', 'data-live-search' => 'true']) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>@lang('lang.sale_note')</label>
                                <textarea rows="3" class="form-control" name="sale_note"></textarea>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>@lang('lang.staff_note')</label>
                                <textarea rows="3" class="form-control" name="staff_note"></textarea>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('terms_and_condition_id', __('lang.terms_and_conditions'), []) !!}
                                <div class="input-group my-group">
                                    {!! Form::select('terms_and_condition_id', $tac,
                                    null, ['class' =>
                                    'selectpicker form-control', 'data-live-search'=>"true",
                                    'style' =>'width: 80%' , 'id' => 'terms_and_condition_id']) !!}
                                </div>
                                <div class="tac_description_div"><span></span></div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <br>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">@lang('lang.import')</button>
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
<script src="{{asset('js/pos.js')}}"></script>
<script>

</script>
@endsection
