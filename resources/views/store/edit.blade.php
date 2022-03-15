<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('StoreController@update', $store->id), 'method' => 'put', 'id' => 'store_add_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.edit_store' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                {!! Form::text('name', $store->name, ['class' => 'form-control', 'placeholder' => __( 'lang.name' ), 'required' ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('location', __( 'lang.location' )) !!}
                {!! Form::text('location', $store->location, ['class' => 'form-control', 'placeholder' => __( 'lang.location' ) ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('phone_number', __( 'lang.phone_number' )) !!}
                {!! Form::text('phone_number', $store->phone_number, ['class' => 'form-control', 'placeholder' => __( 'lang.phone_number' ) ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('email', __( 'lang.email' )) !!}
                {!! Form::email('email', $store->email, ['class' => 'form-control', 'placeholder' => __( 'lang.email' ) ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('manager_name', __( 'lang.manager_name' )) !!}
                {!! Form::text('manager_name', $store->manager_name, ['class' => 'form-control', 'placeholder' => __( 'lang.manager_name' ) ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('manager_mobile_number', __( 'lang.manager_mobile_number' )) !!}
                {!! Form::text('manager_mobile_number', $store->manager_mobile_number, ['class' => 'form-control', 'placeholder' => __( 'lang.manager_mobile_number' ) ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('details', __( 'lang.details' )) !!}
                {!! Form::textarea('details', $store->details, ['class' => 'form-control', 'placeholder' => __( 'lang.details' ), 'rows' => '3' ]);
                !!}
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
