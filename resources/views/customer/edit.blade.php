@extends('layouts.app')
@section('title', __('lang.customer'))
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.edit_customer')</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                        {!! Form::open(['url' => action('CustomerController@update', $customer->id), 'id' =>
                        'customer-form',
                        'method' =>
                        'PUT', 'class' => '', 'enctype' => 'multipart/form-data']) !!}

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('customer_type_id', __( 'lang.customer_type' ) . ':*') !!}
                                    {!! Form::select('customer_type_id', $customer_types, $customer->customer_type_id, ['class' =>
                                    'selectpicker
                                    form-control', 'data-live-search' => "true", 'required', 'placeholder' =>
                                    __('lang.please_select')]) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                                    {!! Form::text('name', $customer->name, ['class' => 'form-control', 'placeholder' => __(
                                    'lang.name' ), 'required' ]);
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('photo', __( 'lang.photo' ) . ':') !!} <br>
                                    {!! Form::file('image', ['class']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('mobile_number', __( 'lang.mobile_number' ) . ':') !!}
                                    {!! Form::text('mobile_number', $customer->mobile_number, ['class' => 'form-control', 'placeholder' =>
                                    __(
                                    'lang.mobile_number' ), 'required' ]);
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('address', __( 'lang.address' ) . ':') !!}
                                    {!! Form::text('address', $customer->address, ['class' => 'form-control', 'placeholder' => __(
                                    'lang.address' ) ]);
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('email', __( 'lang.email' ) . ':') !!}
                                    {!! Form::email('email', $customer->email, ['class' => 'form-control', 'placeholder' => __(
                                    'lang.email' ) ]);
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('fixed_discount', __( 'lang.fixed_discount' ) . ':') !!}
                                    {!! Form::text('fixed_discount', $customer->fixed_discount, ['class' => 'form-control', 'placeholder' =>
                                    __(
                                    'lang.fixed_discount' ) ]);
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('opening_balance', __( 'lang.balance' ) . ':') !!}
                                    {!! Form::text('opening_balance', $customer->opening_balance, ['class' => 'form-control', 'placeholder' =>
                                    __(
                                    'lang.balance' ) ]);
                                    !!}
                                </div>
                            </div>
                        </div>


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
    $('#customer-type-form').submit(function(){
        $(this).validate();
        if($(this).valid()){
            $(this).submit();
        }
    })
</script>
@endsection
