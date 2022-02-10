<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('TutorialController@store'), 'method' => 'post', 'id' =>'tutorial_add_form',
        'files' => true ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_tutorial' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('tutorial_category_id', __( 'lang.content' ) . ':*') !!}
                {!! Form::select('tutorial_category_id', $tutorial_categories, false, ['class' => 'form-control
                selectpicker', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'lang.name' ), 'required'
                ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('description', __( 'lang.description' )) !!}
                {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => __(
                'lang.description' )
                ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('link', __( 'lang.link' ) . ':*') !!}
                {!! Form::text('link', null, ['class' => 'form-control', 'placeholder' => __( 'lang.link' ), 'required'
                ]);
                !!}
            </div>
            {{-- <div class="form-group">
                {!! Form::label('video', __( 'lang.video' ) . ':*') !!} <br>
                {!! Form::file('video', null, ['class' => '', 'required'
                ]);
                !!}
            </div>
            <div class="form-group">
                {!! Form::label('thumbnail', __( 'lang.thumbnail' ) . ':*') !!} <br>
                {!! Form::file('thumbnail', null, ['class' => '', 'required'
                ]);
                !!}
            </div> --}}

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('.selectpicker').selectpicker('render');
</script>
