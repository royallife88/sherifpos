<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>

<body
    style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; background-color: #f5f8fa; color: #74787E; height: 100%; hyphens: auto; line-height: 1.4; margin: 0; -moz-hyphens: auto; -ms-word-break: break-all; width: 100% !important; -webkit-hyphens: auto; -webkit-text-size-adjust: none; word-break: break-word;">
    <style>
        .table {
            border: 1px solid #dee2e6 !important;
        }

        .table th {
            border: 2px solid #9e9e9e !important;
        }

        .table td {
            border: 1px solid #dee2e6 !important;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody+tbody {
            border-top: 2px solid #dee2e6;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.3rem;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .table-bordered thead th,
        .table-bordered thead td {
            border-bottom-width: 2px;
        }

        .table-borderless th,
        .table-borderless td,
        .table-borderless thead th,
        .table-borderless tbody+tbody {
            border: 0;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

    </style>
    <div class="container">
        <div class="col-md-12 print-only">
            @include('layouts.partials.print_header')
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center no-print">
                        <h4>@lang('lang.remove_stock'): {{ $remove_stock->invoice_no }}</h4>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                {!! Form::label('supplier_name', __('lang.supplier_name'), []) !!}:
                                <b>{{ $supplier->name }}</b>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('email', __('lang.email'), []) !!}: <b>{{ $supplier->email }}</b>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('mobile_number', __('lang.mobile_number'), []) !!}:
                                <b>{{ $supplier->mobile_number }}</b>
                            </div>
                            <div class="col-md-4">
                                {!! Form::label('address', __('lang.address'), []) !!}: <b>{{ $supplier->address }}</b>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-condensed" id="product_table">
                                    <thead>
                                        <tr>
                                            <th style="width: 25%" class="col-sm-8">@lang('lang.products')</th>
                                            <th style="width: 25%" class="col-sm-4">@lang('lang.sku')</th>
                                            <th style="width: 25%" class="col-sm-4">@lang('lang.quantity')</th>
                                            <th style="width: 12%" class="col-sm-4">@lang('lang.purchase_price')</th>
                                            <th style="width: 12%" class="col-sm-4">@lang('lang.sub_total')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($remove_stock->remove_stock_lines as $line)
                                            <tr>
                                                <td>
                                                    {{ $line->product->name }}
                                                    @if (!empty($line->variation))
                                                        @if ($line->variation->name != 'Default')
                                                            <b>{{ $line->variation->name }}</b>
                                                        @endif
                                                    @endif

                                                </td>
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
                                        class="final_total_span">{{ @num_format($remove_stock->final_total) }}</span>
                                </h3>

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

</body>

</html>
