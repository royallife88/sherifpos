@forelse ($products as $product)
@php
$i = $index;
@endphp
<tr class="product_row">
    <td class="row_number"></td>
    <td><img src="@if(!empty($product->getFirstMediaUrl('product'))){{$product->getFirstMediaUrl('product')}}@else{{asset('/uploads/'.session('logo'))}}@endif"
        alt="photo" width="50" height="50"></td>
    <td>
        @if($product->variation_name != "Default")
        <b>{{$product->variation_name}} {{$product->sub_sku}}</b>
        @else
        {{$product->product_name}}
        @endif
        <input type="hidden" name="add_stock_lines[{{$i}}][is_service]" class="is_service"
            value="{{$product->is_service}}">
        <input type="hidden" name="add_stock_lines[{{$i}}][product_id]" class="product_id"
            value="{{$product->product_id}}">
        <input type="hidden" name="add_stock_lines[{{$i}}][variation_id]" class="variation_id"
            value="{{$product->variation_id}}">
    </td>
    <td>
        {{$product->sub_sku}}
    </td>
    <td>
        <input type="text" class="form-control quantity" min=1 name="add_stock_lines[{{$i}}][quantity]" required
            value="@if(isset($product->quantity)){{@num_format($product->quantity)}}@else{{1}}@endif">
    </td>
    <td>
        {{$product->units->pluck('name')[0]??''}}
    </td>
    <td>
        <input type="text" class="form-control purchase_price" name="add_stock_lines[{{$i}}][purchase_price]" required
            value="@if(isset($product->default_purchase_price)){{@num_format($product->default_purchase_price / $exchange_rate)}}@else{{0}}@endif">
            <input class="final_cost" type="hidden" name="add_stock_lines[{{$i}}][final_cost]" value="@if(isset($product->default_purchase_price)){{@num_format($product->default_purchase_price / $exchange_rate)}}@else{{0}}@endif">
    </td>
    <td>
        <span class="sub_total_span"></span>
        <input type="hidden" class="form-control sub_total" name="add_stock_lines[{{$i}}][sub_total]" value="">
    </td>
    <td>
        <input type="hidden" name="current_stock" class="current_stock"
            value="@if($product->is_service) {{0}} @else @if(isset($product->qty_available)){{$product->qty_available}}@else{{0}}@endif @endif">
        <span
            class="current_stock_text">@if($product->is_service) {{'-'}} @else @if(isset($product->qty_available)){{@num_format($product->qty_available)}}@else{{0}}@endif @endif</span>
    </td>
    <td rowspan="2">
        <button style="margin-top: 33px;" type="button" class="btn btn-danger btn-sx remove_row" data-index="{{$i}}"><i
                class="fa fa-times"></i></button>
    </td>
</tr>
<tr class="row_details_{{$i}}">
    <td> {!! Form::label('', __('lang.batch_number'), []) !!} <br> {!!
        Form::text('add_stock_lines['.$i.'][batch_number]', null, ['class' => 'form-control']) !!}</td>
    <td> {!! Form::label('', __('lang.manufacturing_date'), []) !!}<br>
        {!! Form::text('add_stock_lines['.$i.'][manufacturing_date]', null, ['class' => 'form-control datepicker',
        'readonly']) !!}
    </td>
    <td> {!! Form::label('', __('lang.expiry_date'), []) !!}<br>
        {!! Form::text('add_stock_lines['.$i.'][expiry_date]', null, ['class' => 'form-control datepicker expiry_date',
        'readonly']) !!}
    </td>
    <td> {!! Form::label('', __('lang.days_before_the_expiry_date'), []) !!}<br>
        {!! Form::text('add_stock_lines['.$i.'][expiry_warning]', null, ['class' => 'form-control days_before_the_expiry_date']) !!}
    </td>
    <td> {!! Form::label('', __('lang.convert_status_expire'), []) !!}<br>
        {!! Form::text('add_stock_lines['.$i.'][convert_status_expire]', null, ['class' => 'form-control']) !!}
    </td>
</tr>
@empty

@endforelse
<script>
    $('.datepicker').datepicker({
        language: '{{session('language')}}',
    })
</script>
