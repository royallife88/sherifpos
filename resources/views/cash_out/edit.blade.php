<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CashOutController@update', $cash_out->id), 'method' => 'put', 'id' =>
        'add_cash_out_form',
        'files' => true
        ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_cash_out' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <input type="hidden" name="cash_register_id" value="{{$cash_out->cash_register_id}}">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('amount', __( 'lang.amount' ) . ':*') !!}
                            {!! Form::text('amount', @num_format($cash_out->amount), ['class' => 'form-control', 'placeholder' =>
                            __(
                            'lang.amount' ), 'required' ]);
                            !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('source_id', __('lang.receiver'), []) !!}
                            {!! Form::select('source_id', $users,
                            $cash_out->source_id, ['class' => 'selectpicker form-control', 'data-live-search'=>"true",
                            'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('notes', __('lang.notes'), []) !!}
                            {!! Form::textarea('notes',
                            $cash_out->notes, ['class' => 'form-control',
                            'placeholder' => __('lang.notes'), 'rows' => 3]) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker('render')
</script>
