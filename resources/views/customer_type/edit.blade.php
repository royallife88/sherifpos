@extends('layouts.app')
@section('title', __('lang.customer_type'))
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.edit')</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                        {!! Form::open(['url' => action('CustomerTypeController@update', $customer_type->id), 'id' =>
                        'customer-type-form',
                        'method' =>
                        'PUT', 'class' => '', 'enctype' => 'multipart/form-data']) !!}
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                                    {!! Form::text('name', $customer_type->name, ['class' => 'form-control',
                                    'placeholder' => __(
                                    'lang.name' ), 'required' ]);
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('store', __( 'lang.store' ) . ':*') !!}
                                    {!! Form::select('stores[]', $stores, $customer_type->customer_type_store->pluck('store_id'), ['class' => 'selectpicker
                                    form-control', 'data-live-search' => "true", 'multiple', 'required']) !!}
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="submit" value="{{trans('lang.submit')}}" id="submit-btn"
                                        class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).on('click', '.remove_row', function (){
        $(this).closest('tr').remove();
    })

    $(document).on('click', '.add_row_point', function(){
        var row_id = parseInt($('#row_id_point').val()) + 1;
        $.ajax({
            method: 'get',
            url: '/customer-type/get-product-point-row?row_id='+row_id,
            data: {  },
            contentType: 'html',
            success: function(result) {
                $('#product_point_table tbody').append(result);
                $('.row_'+row_id).find('.product_id_'+row_id).selectpicker('refresh');
                $('#row_id_point').val(row_id);
            },
        });
    })

    $('#customer-type-form').submit(function(){
        $(this).validate();
        if($(this).valid()){
            $(this).submit();
        }
    })
</script>
@endsection
