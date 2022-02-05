@extends('layouts.app')
@section('title', __('lang.invoice_no'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="col-md-12 print-only">
            @include('layouts.partials.print_header')
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center no-print">
                        <h4>@lang('lang.invoice_no'): {{$add_stock->invoice_no}}</h4>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                {!! Form::label('supplier_name', __('lang.supplier_name'), []) !!}:
                                <b>{{$supplier->name ?? ''}}</b>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('email', __('lang.email'), []) !!}: <b>{{$supplier->email ?? ''}}</b>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('mobile_number', __('lang.mobile_number'), []) !!}:
                                <b>{{$supplier->mobile_number ?? ''}}</b>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('address', __('lang.address'), []) !!}: <b>{{$supplier->address ?? ''}}</b>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('store', __('lang.store'), []) !!}: <b>{{$add_stock->store->name ??
                                    ''}}</b>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-condensed" id="product_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%" class="col-sm-8">@lang( 'lang.products' )</th>
                                            <th style="width: 25%" class="col-sm-4">@lang( 'lang.sku' )</th>
                                            <th style="width: 25%" class="col-sm-4">@lang( 'lang.quantity' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.purchase_price' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.final_cost' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.sub_total' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.batch_number' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.manufacturing_date' )
                                            </th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.expiry_date' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang(
                                                'lang.days_before_the_expiry_date' )</th>
                                            <th style="width: 12%" class="col-sm-4">@lang( 'lang.convert_status_expire'
                                                )</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($add_stock->add_stock_lines as $line)
                                        <tr>
                                            <td>
                                                {{$line->product->name ?? ''}}

                                                @if(!empty($line->variation))
                                                @if($line->variation->name != "Default")
                                                <b>{{$line->variation->name}}</b>
                                                @endif
                                                @endif
                                                @if(empty($line->variation) && empty($line->product))
                                                <span class="text-red">@lang('lang.deleted')</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($line->variation))
                                                {{$line->variation->sub_sku}}
                                                @endif
                                            </td>
                                            <td>
                                                @if(isset($line->quantity)){{@num_format($line->quantity)}}@else{{1}}@endif
                                            </td>
                                            <td>
                                                @if(isset($line->purchase_price)){{@num_format($line->purchase_price)}}@else{{0}}@endif
                                            </td>
                                            <td>
                                                @if(isset($line->final_cost)){{@num_format($line->final_cost)}}@else{{0}}@endif
                                            </td>
                                            <td>
                                                {{@num_format($line->sub_total)}}
                                            </td>
                                            <td>{{$line->batch_number}}</td>
                                            <td>@if(!empty($line->manufacturing_date)){{@format_date($line->manufacturing_date)}}@endif
                                            </td>
                                            <td>@if(!empty($line->expiry_date)){{@format_date($line->expiry_date)}}@endif
                                            </td>
                                            <td>{{$line->expiry_warning}}</td>
                                            <td>{{$line->convert_status_expire}}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="col-md-3 offset-md-8 text-right">
                                <h3> @lang('lang.total'): <span
                                        class="final_total_span">{{@num_format($add_stock->add_stock_lines->sum('sub_total'))}}</span>
                                </h3>

                            </div>
                        </div>
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-md-3">
                                {!! Form::label('other_expenses', __('lang.other_expenses'), []) !!}:
                                <b>{{@num_format($add_stock->other_expenses)}}</b>
                            </div>
                            <div class="col-md-3">
                                {!! Form::label('discount_amount', __('lang.discount'), []) !!}:
                                <b>{{@num_format($add_stock->discount_amount)}}</b>
                            </div>
                            <div class="col-md-3">
                                {!! Form::label('other_payments', __('lang.other_payments'), []) !!}:
                                <b>{{@num_format($add_stock->other_payments)}}</b>
                            </div>
                            <div class="col-md-3">
                                {!! Form::label('source_of_payment', __('lang.source_of_payment'), []) !!}:
                                <b>{{$add_stock->source_name}}</b>
                            </div>
                        </div>
                        <br>
                        <br>
                        @include('transaction_payment.partials.payment_table', ['payments' =>
                        $add_stock->transaction_payments])

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('notes', __('lang.notes'), []) !!}: <br>
                                    {{$add_stock->notes}}

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    {!! Form::label('files', __('lang.files'), []) !!}: <br>
                                    @php
                                        $mediaItems = $add_stock->getMedia('add_stock');
                                    @endphp
                                    @if(!empty($mediaItems))
                                    @foreach ($mediaItems as $item)
                                        <a href="{{$item->getUrl()}}">{{$item->name}}</a> <br>
                                    @endforeach

                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 print-only">
            @include('layouts.partials.print_footer')
        </div>
    </div>


</section>
@endsection

@section('javascript')
<script type="text/javascript">
    @if(!empty(request()->print))
        $(document).ready(function(){
            setTimeout(() => {
                window.print();
            }, 1000);
        })
    @endif
</script>
@endsection
