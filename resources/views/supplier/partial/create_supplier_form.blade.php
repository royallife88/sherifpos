<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
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
            {!! Form::label('company_name', __( 'lang.company_name' ) . ':*') !!}
            {!! Form::text('company_name', null, ['class' => 'form-control', 'placeholder' =>
            __(
            'lang.company_name' ), 'required' ]);
            !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('vat_number', __( 'lang.vat_number' ) . ':') !!}
            {!! Form::text('vat_number', null, ['class' => 'form-control', 'placeholder' => __(
            'lang.vat_number' ) ]);
            !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('email', __( 'lang.email' ) . ':*') !!}
            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => __(
            'lang.email' ), 'required' ]);
            !!}
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
            {!! Form::label('address', __( 'lang.address' ) . ':*') !!}
            {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' =>
            __(
            'lang.balance' ), 'required' ]);
            !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('city', __( 'lang.city' ) . ':') !!}
            {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' =>
            __(
            'lang.balance' ) ]);
            !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('state', __( 'lang.state' ) . ':') !!}
            {!! Form::text('state', null, ['class' => 'form-control', 'placeholder' =>
            __(
            'lang.balance' ) ]);
            !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('postal_code', __( 'lang.postal_code' ) . ':') !!}
            {!! Form::text('postal_code', null, ['class' => 'form-control', 'placeholder' =>
            __(
            'lang.balance' ) ]);
            !!}
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-group">
            {!! Form::label('country    ', __( 'lang.country' ) . ':') !!}
            {!! Form::text('country ', null, ['class' => 'form-control', 'placeholder' =>
            __(
            'lang.balance' ) ]);
            !!}
        </div>
    </div>
</div>
<input type="hidden" name="quick_add" value="{{$quick_add}}">
