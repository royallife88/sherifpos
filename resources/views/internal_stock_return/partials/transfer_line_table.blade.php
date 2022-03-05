<!DOCTYPE html>
<html>

<head></head>

<body>
    <div style="margin-top: 10px; margin-bottom: 10px;">
        @lang('lang.details'):
        <table class="table"
            style="text-align: center; width: 100%; border-collapse: collapse; border: 1px solid #999;">
            <thead>
                <tr>
                    <th style="width: 25%; border: 1px solid #999;" class="col-sm-8">@lang( 'lang.products' )</th>
                    <th style="width: 25%; border: 1px solid #999;" class="col-sm-4">@lang( 'lang.sku' )</th>
                    <th style="width: 25%; border: 1px solid #999;" class="col-sm-4">@lang( 'lang.quantity' )</th>
                    <th style="width: 12%; border: 1px solid #999;" class="col-sm-4">@lang( 'lang.purchase_price' )</th>
                    <th style="width: 12%; border: 1px solid #999;" class="col-sm-4">@lang( 'lang.sub_total' )</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->transfer_lines as $product)
                <tr>
                    <td style="border: 1px solid #999;">
                        {{$product->product->name}}

                        @if($product->variation->name != "Default")
                        <b>{{$product->variation->name}}</b>
                        @endif
                    </td>
                    <td style="border: 1px solid #999;">
                        {{$product->variation->sub_sku}}
                    </td>
                    <td style="border: 1px solid #999;">
                        @if(isset($product->quantity)){{@num_format($product->quantity)}}@else{{0}}@endif
                    </td>
                    <td style="border: 1px solid #999;">
                        @if(isset($product->purchase_price)){{@num_format($product->purchase_price)}}@else{{0}}@endif
                    </td>
                    <td style="border: 1px solid #999;">
                        <span class="sub_total_span">{{@num_format($product->sub_total)}}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <th class="text-right">@lang( 'lang.total' ):</th>
                    <th class="total_span">{{@num_format($transaction->final_total)}}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</body>

</html>
