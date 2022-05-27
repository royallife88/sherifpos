@extends('layouts.app')
@section('title', __('lang.purchase_order'))

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
                            <h4>@lang('lang.purchase_order'): {{ $purchase_order->po_no }}</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    {!! Form::label('supplier_name', __('lang.supplier_name'), []) !!}:
                                    <b>
                                        @if (!empty($supplier))
                                            {{ $supplier->name }}
                                        @endif
                                    </b>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::label('email', __('lang.email'), []) !!}: <b>
                                        @if (!empty($supplier))
                                            {{ $supplier->email }}
                                        @endif
                                    </b>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::label('mobile_number', __('lang.mobile_number'), []) !!}:
                                    <b>
                                        @if (!empty($supplier))
                                            {{ $supplier->mobile_number }}
                                        @endif
                                    </b>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::label('address', __('lang.address'), []) !!}: <b>
                                        @if (!empty($supplier))
                                            {{ $supplier->address }}
                                        @endif
                                    </b>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <table class="table table-bordered table-striped table-condensed" id="product_table">
                                        <thead>
                                            <tr>
                                                <th style="width: 25%" class="col-sm-8">@lang('lang.products')</th>
                                                @if (session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                                                    <th style="width: 25%" class="col-sm-4">@lang('lang.sku')</th>
                                                @endif
                                                <th style="width: 25%" class="col-sm-4">@lang('lang.quantity')</th>
                                                <th style="width: 12%" class="col-sm-4">@lang('lang.purchase_price')</th>
                                                <th style="width: 12%" class="col-sm-4">@lang('lang.sub_total')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($purchase_order->purchase_order_lines as $line)
                                                <tr>
                                                    <td>
                                                        {{ $line->product->name ?? '' }}

                                                        @if (!empty($line->variation))
                                                            @if ($line->variation->name != 'Default')
                                                                <b>{{ $line->variation->name }}</b>
                                                            @endif
                                                        @endif

                                                    </td>
                                                    @if (session('system_mode') == 'pos' || session('system_mode') == 'garments' || session('system_mode') == 'supermarket')
                                                        <td>
                                                            @if (!empty($line->variation))
                                                                @if ($line->variation->name != 'Default')
                                                                    {{ $line->variation->sub_sku }}
                                                                @else
                                                                    {{ $line->product->sku ?? '' }}
                                                                @endif
                                                            @else
                                                                {{ $line->product->sku ?? '' }}
                                                            @endif
                                                        </td>
                                                    @endif
                                                    <td>
                                                        @if (isset($line->quantity))
                                                            {{ $line->quantity }}@else{{ 1 }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (isset($line->purchase_price))
                                                            {{ @num_format($line->purchase_price) }}@else{{ 0 }}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ @num_format($line->sub_total) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="col-md-3 offset-md-8 text-right">
                                    <h3> @lang('lang.total'): <span
                                            class="final_total_span">{{ @num_format($purchase_order->final_total) }}</span>
                                    </h3>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        {!! Form::label('details', __('lang.details'), []) !!}:
                                        {{ $purchase_order->details }}

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
        @if (!empty(request()->print))
            $(document).ready(function() {
                setTimeout(() => {
                    window.print();
                }, 1000);
            })
        @endif
    </script>
@endsection
