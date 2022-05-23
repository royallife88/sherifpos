<style type="text/css">
    .text-center {
        text-align: center;
    }

    .text-uppercase {
        text-transform: uppercase;
    }

    /*Css related to printing of barcode*/
    .label-border-outer {
        border: 0.1px solid grey !important;
    }

    .label-border-internal {
        /*border: 0.1px dotted grey !important;*/
    }

    .sticker-border {
        border: 0.1px dotted grey !important;
        overflow: hidden;
        box-sizing: border-box;
    }

    #preview_box {
        padding-left: 30px !important;
    }

    @media print {
        .content-wrapper {
            border-left: none !important;
            /*fix border issue on invoice*/
        }

        .label-border-outer {
            border: none !important;
        }

        .label-border-internal {
            border: none !important;
        }

        .sticker-border {
            border: none !important;
        }

        #preview_box {
            padding-left: 0px !important;
        }

        #toast-container {
            display: none !important;
        }

        .tooltip {
            display: none !important;
        }

        .btn {
            display: none !important;
        }
    }

    @media print {
        #preview_body {
            display: block !important;
        }
    }

    @page {
        margin-top: 0in;
        margin-bottom: 0in;
        margin-left: 0in;
        margin-right: 0in;

    }

</style>

<title>{{ __('lang.print_labels') }}</title>
<button class="btn btn-success" onclick="window.print()">@lang('lang.print')</button>
<div id="preview_body">
    @php
        $loop_count = 0;
    @endphp
    @foreach ($product_details as $details)
        @while ($details['qty'] > 0)
            <div style="height:1.2in !important; line-height: {{ $page_height }}in;  display: inline-block;"
                class="sticker-border text-center">
                <div style="display:inline-block;vertical-align:middle;line-height:14px !important; font-size: 14px;">

                    <p class="text-center" style="padding: 0px !important; margin: 0px;">
                        @if (!empty($print['name']))
                            @if (!empty($print['size']) && !empty($details['details']->size_name))
                                {{ str_replace($details['details']->size_name, '', $details['details']->product_actual_name) }}
                            @elseif (!empty($print['color']) && !empty($details['details']->color_name))
                                {{ str_replace($details['details']->color_name, '', $details['details']->product_actual_name) }}
                            @else
                                {{ $details['details']->product_actual_name }}
                            @endif
                            @if (!empty($print['color']) && !empty($details['details']->color_name))
                                {{ $details['details']->color_name }}
                            @endif
                        @endif
                    </p>
                    @if (!empty($print['size']) && !empty($details['details']->size_name))
                        <p style="margin-top: -12px; text-align: right; font-weight: bold; margin-bottom: 0px;">
                            {{ $details['details']->size_name }}</p>
                    @endif

                    {{-- Grade --}}
                    <span style="display: block !important">
                        @if (!empty($print['grade']) && !empty($details['details']->grade_name))
                            @lang('lang.grade'):
                            {{ $details['details']->grade_name }}
                        @endif
                        {{-- Unit --}}
                        @if (!empty($print['unit']) && !empty($details['details']->unit_name))
                            @lang('lang.unit'):
                            {{ $details['details']->unit_name }}
                        @endif
                        {{-- Price --}}
                        @if (!empty($print['price']))
                            @lang('lang.price'):
                            {{ @num_format($details['details']->default_sell_price) }}
                        @endif
                    </span>

                    @if (!empty($print['free_text']))
                        <span style="display: block !important">
                            {{ $print['free_text'] }}
                        </span>
                    @endif

                    <img class="center-block" style="width:250px !important; height: 70px; margin: 0; padding: 0 10px;"
                        src="data:image/png;base64,{{ DNS1D::getBarcodePNG($details['details']->sub_sku, $details['details']->barcode_type, 3, 30, [39, 48, 54], true) }}">

                </div>
                <div class="row">
                    <div class="col-md-4" style="font-size: 14px; margin-top: -30px;">
                        @if (!empty($print['site_title']))
                            <p style="text-align: left; word-wrap: break-word;">
                                {{ $print['site_title'] }}
                            </p>
                        @endif
                        @if (!empty($print['store']))
                            <p style="text-align: left; word-wrap: break-word; padding-top: 3px;">
                                <br>{{ $print['store'] }}
                            </p>
                        @endif
                    </div>
                    @php
                        $product = App\Models\Product::where('id', $details['details']->product_id)
                            ->with(['colors', 'sizes'])
                            ->first();
                    @endphp
                    <div class="col-md-4" style="font-size: 14px; margin-top: -30px;"></div>
                    <div class="col-md-4" style="font-size: 14px; margin-top: -30px;">
                        @if (!empty($print['size_variations']))
                            <p style="text-align: right; word-wrap: break-word;">
                                {{ implode(', ', $product->sizes->pluck('name')->toArray()) }}
                            </p>
                        @endif
                        @if (!empty($print['color_variations']))
                            <p style="text-align: right; word-wrap: break-word; padding-top: 3px;">

                                {{ implode(', ', $product->colors->pluck('name')->toArray()) }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>



            @php
                $details['qty'] = $details['qty'] - 1;
            @endphp
        @endwhile
    @endforeach

</div>


<script type="text/javascript"></script>
