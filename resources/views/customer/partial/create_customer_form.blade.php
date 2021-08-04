<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('customer_type_id', __( 'lang.customer_type' ) . ':*') !!}
            {!! Form::select('customer_type_id', $customer_types, false, ['class' => 'selectpicker
            form-control', 'data-live-search' => "true", 'required', 'placeholder' => __('lang.please_select')]) !!}
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('name', __( 'lang.name' ). ':*' ) !!}
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __(
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
            {!! Form::label('mobile_number', __( 'lang.mobile_number' ) . ':*') !!}
            {!! Form::text('mobile_number', null, ['class' => 'form-control', 'placeholder' =>
            __(
            'lang.mobile_number' ), 'required' ]);
            !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('address', __( 'lang.address' ) . ':') !!}
            {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => __(
            'lang.address' ) ]);
            !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('email', __( 'lang.email' ) . ':') !!}
            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => __(
            'lang.email' ) ]);
            !!}
        </div>
    </div>
    {{-- <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('fixed_discount', __( 'lang.fixed_discount' ) . ':') !!}
            {!! Form::text('fixed_discount', null, ['class' => 'form-control', 'placeholder' =>
            __(
            'lang.fixed_discount' ) ]);
            !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('opening_balance', __( 'lang.balance' ) . ':') !!}
            {!! Form::text('opening_balance', null, ['class' => 'form-control', 'placeholder' =>
            __(
            'lang.balance' ) ]);
            !!}
        </div>
    </div> --}}
</div>
<input type="hidden" name="quick_add" value="{{$quick_add}}">
