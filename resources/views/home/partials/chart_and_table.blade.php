@if (auth()->user()->can('superadmin') || auth()->user()->is_admin)
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-7 mt-4">
                <div class="card line-chart-example">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.cash_flow')</h4>
                    </div>
                    <div class="card-body">
                        @php
                            $color = '#733686';
                            $color_rgba = 'rgba(115, 54, 134, 0.8)';

                        @endphp
                        <canvas id="cashFlow" data-color="{{ $color }}" data-color_rgba="{{ $color_rgba }}"
                            data-recieved="{{ json_encode($payment_received) }}"
                            data-sent="{{ json_encode($payment_sent) }}" data-month="{{ json_encode($month) }}"
                            data-label1="@lang('lang.payment_received')" data-label2="@lang('lang.payment_sent')"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-5 mt-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4>{{ @format_date($start_date) }} - {{ @format_date($end_date) }}</h4>
                    </div>
                    <div class="pie-chart mb-2">
                        <canvas id="transactionChart" data-color="{{ $color }}"
                            data-color_rgba="{{ $color_rgba }}" data-revenue={{ $dashboard_data['revenue'] }}
                            data-purchase={{ $dashboard_data['purchase'] }}
                            data-expense={{ $dashboard_data['expense'] }} data-label1="@lang('lang.purchase')"
                            data-label2="@lang('lang.revenue')" data-label3="@lang('lang.expense')" width="100" height="95">
                        </canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4>@lang('lang.yearly_report')</h4>
                </div>
                <div class="card-body">
                    <canvas id="saleChart" data-sale_chart_value="{{ json_encode($yearly_sale_amount) }}"
                        data-purchase_chart_value="{{ json_encode($yearly_purchase_amount) }}"
                        data-label1="@lang('lang.purchased_amount')" data-label2="@lang('lang.sold_amount')"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>@lang('lang.recent_transactions')</h4>
                    <div class="right-column">
                        <div class="badge badge-primary">@lang('lang.latest') 5</div>
                    </div>
                </div>
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" href="#sale-latest" role="tab"
                            data-toggle="tab">@lang('lang.sale')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#purchase-latest" role="tab"
                            data-toggle="tab">@lang('lang.purchase')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#quotation-latest" role="tab"
                            data-toggle="tab">@lang('lang.quotation')</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#payment-latest" role="tab"
                            data-toggle="tab">@lang('lang.payments')</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade show active" id="sale-latest">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.reference_no')</th>
                                        <th>@lang('lang.customer')</th>
                                        <th>@lang('lang.grand_total')</th>
                                        <th>@lang('lang.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sales as $sale)
                                        <tr>
                                            <td>{{ @format_date($sale->transaction_date) }}</td>
                                            <td>{{ $sale->invoice_no }}</td>
                                            <td>
                                                @if (!empty($sale->customer))
                                                    {{ $sale->customer->name }}
                                                @endif
                                            </td>
                                            <td>{{ @num_format($sale->final_total) }}</td>
                                            <td>
                                                @if ($sale->status == 'final')
                                                    <span class="badge badge-success">@lang('lang.completed')</span>
                                                @else
                                                    {{ ucfirst($sale->status) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="purchase-latest">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.reference_no')</th>
                                        <th>@lang('lang.supplier')</th>
                                        <th>@lang('lang.grand_total')</th>
                                        <th>@lang('lang.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($add_stocks as $add_stock)
                                        <tr>
                                            <td>{{ @format_date($add_stock->transaction_date) }}</td>
                                            <td>{{ $add_stock->invoice_no }}</td>
                                            <td>
                                                @if (!empty($add_stock->supplier))
                                                    {{ $add_stock->supplier->name }}
                                                @endif
                                            </td>
                                            <td>{{ @num_format($add_stock->final_total) }}</td>
                                            <td>
                                                @if ($add_stock->status == 'received')
                                                    <span class="badge badge-success">@lang('lang.completed')</span>
                                                @else
                                                    {{ ucfirst($add_stock->status) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="quotation-latest">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.reference_no')</th>
                                        <th>@lang('lang.customer')</th>
                                        <th>@lang('lang.grand_total')</th>
                                        <th>@lang('lang.status')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quotations as $quotation)
                                        <tr>
                                            <td>{{ @format_date($quotation->transaction_date) }}</td>
                                            <td>{{ $quotation->invoice_no }}</td>
                                            <td>
                                                @if (!empty($quotation->customer))
                                                    {{ $quotation->customer->name }}
                                                @endif
                                            </td>
                                            <td>{{ @num_format($quotation->final_total) }}</td>
                                            <td>
                                                @if ($quotation->status == 'final')
                                                    <span class="badge badge-success">@lang('lang.completed')</span>
                                                @else
                                                    {{ ucfirst($quotation->status) }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane fade" id="payment-latest">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date')</th>
                                        <th>@lang('lang.payment_ref')</th>
                                        <th>@lang('lang.paid_by')</th>
                                        <th>@lang('lang.amount')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>{{ @format_date($payment->paid_on) }}</td>
                                            <td>{{ $payment->invoice_no }}</td>
                                            <td>
                                                @if (!empty($payment_types[$payment->method]))
                                                    {{ $payment_types[$payment->method] }}
                                                @endif
                                            </td>
                                            <td>{{ @num_format($payment->amount) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>@lang('lang.best_seller')</h4>
                    <div class="right-column">
                        <div class="badge badge-primary">@lang('lang.top') 5</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>@lang('lang.product_details')</th>
                                <th>@lang('lang.qty')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($best_sellings as $best_selling)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $best_selling->product->name }} [{{ $best_selling->product->sku }}]</td>
                                    <td>{{ @num_format($best_selling->qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>@lang('lang.best_seller') (@lang('lang.qty'))</h4>
                    <div class="right-column">
                        <div class="badge badge-primary">@lang('lang.top') 5</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>@lang('lang.product_details')</th>
                                <th>@lang('lang.qty')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($yearly_best_sellings_qty as $best_sellings_qty)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $best_sellings_qty->product->name }}
                                        [{{ $best_sellings_qty->product->sku }}]</td>
                                    <td>{{ @num_format($best_sellings_qty->qty) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>@lang('lang.best_seller') (@lang('lang.price'))</h4>
                    <div class="right-column">
                        <div class="badge badge-primary">@lang('lang.top') 5</div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>SL No</th>
                                <th>@lang('lang.product_details')</th>
                                <th>@lang('lang.qty')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($yearly_best_sellings_price as $best_sellings_price)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $best_sellings_price->product->name }}
                                        [{{ $best_sellings_price->product->sku }}]
                                    </td>
                                    <td>{{ @num_format($best_sellings_price->total_price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif
