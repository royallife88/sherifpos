@foreach($products as $product)
<tr>
    {!! Form::hidden('row_index', $loop->index, ['class' => 'row_index']) !!}
    {!! Form::hidden('product_ids['.$loop->index.']', $product->id, ['class' => 'product_id']) !!}
    {!! Form::hidden('variation_id['.$loop->index.']', $product->variation_id, ['class' => 'variation_id']) !!}
    {!! Form::hidden('store_ids['.$loop->index.']', $product->store_id, ['class' => 'store_id']) !!}
    {!! Form::hidden('purchase_price['.$loop->index.']', $product->purchase_price, ['class' => 'purchase_price']) !!}
    <td>
        {!! Form::checkbox('product_selected[]', $loop->index, false, ['class' => 'product_checkbox']) !!}
    </td>
    <td><img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
            alt="photo" width="50" height="50"></td>
    <td>{{$product->name}}</td>
    <td>@if(!empty($product->variations))
        {{implode(', ', $product->variations->pluck('sub_sku')->toArray())}} @else {{$product->sku}} @endif
    </td>
    <td><p class="text-center" style="line-height: 15px; padding-bottom: 2px; margin: 0">{{$product->name}}</p><img class="center-block" style="width:250px; !important;height: {{2*0.24}}in !important;"
            src="data:image/png;base64,{{DNS1D::getBarcodePNG($product->sku,$product->barcode_type, 3,30,array(39, 48, 54), true)}}">
    </td>
    <td>{{$product->store_name}}</td>
    <td>@if($product->is_service){{'-'}}@else{{@num_format($product->current_stock)}}@endif <input type="hidden"
            class="current_stock" name="current_stock"
            value="@if($product->is_service){{0}}@else{{$product->current_stock}}@endif"></td>
    <td style="width: 100px;">{!! Form::text('qty['.$loop->index.']', 0, ['class' => 'form-control qty', 'style' =>
        'width: 100px !important; border: 1px solid #999', 'placeholder' => __('lang.qty')]) !!}
        <span class="error stock_error hide">@lang('lang.request_stock_should_not_greater_than_current_stock')</span>
    </td>
    <td>@if(!empty($product->product_class)){{$product->product_class->name}}@endif</td>
    <td>@if(!empty($product->category)){{$product->category->name}}@endif</td>
    <td>@if(!empty($product->sub_category)){{$product->sub_category->name}}@endif</td>
    <td><a data-href="{{action('ProductController@getPurchaseHistory', $product->id)}}" data-container=".view_modal"
            class="btn btn-modal">@lang('lang.view')</a></td>
    <td>{{$product->batch_number}}</td>
    <td>{{@num_format($product->sell_price)}}</td>
    <td>@if(!empty($product->tax->name)){{$product->tax->name}}@endif</td>
    <td>@if(!empty($product->brand)){{$product->brand->name}}@endif</td>
    <td>{{implode(', ', $product->units->pluck('name')->toArray())}}</td>
    <td>{{implode(', ', $product->colors->pluck('name')->toArray())}}</td>
    <td>{{implode(', ', $product->sizes->pluck('name')->toArray())}}</td>
    <td>{{implode(', ', $product->grades->pluck('name')->toArray())}}</td>
    <td>{{$product->customer_type}}</td>
    <td>@if(!empty($product->expiry_date)){{@format_date($product->expiry_date)}}@endif</td>
    <td>@if(!empty($product->manufacturing_date)){{@format_date($product->manufacturing_date)}}@endif
    </td>
    <td>{{@num_format($product->discount)}}</td>
    @can('product_module.purchase_price.view')
    <td>{{@num_format($product->purchase_price)}}</td>
    @endcan
</tr>
@endforeach
