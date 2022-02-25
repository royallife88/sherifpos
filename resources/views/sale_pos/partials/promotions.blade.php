@forelse ($sales_promotions->chunk(4) as $chunk)
<tr>
    @foreach ($chunk as $promotion)
    <td class="product-img sound-btn promotion_add" data-sale_promotion_id="{{$promotion->id}}">
        <img src="{{asset('/uploads/'.session('logo'))}}"
            width="100%" />
        <p>{{$promotion->name}} <br> <span>{{@num_format($promotion->discount_value)}}</span></p>
    </td>
    @endforeach
</tr>
@empty
<tr class="text-center">
    <td colspan="5">@lang('lang.no_item_found')</td>
</tr>
@endforelse
