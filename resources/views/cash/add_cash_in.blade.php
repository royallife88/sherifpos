<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CashController@saveAddCashIn'), 'method' => 'post', 'id' => 'add_cash_in_form', 'files' => true]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang('lang.add_cash')</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('amount', __('lang.amount') . ':*') !!}
                            {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => __('lang.amount'), 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('source_type', __('lang.source_type'), []) !!} <br>
                            {!! Form::select('source_type', ['user' => __('lang.user'), 'safe' => __('lang.safe')], 'user', ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'style' => 'width: 80%', 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('source_id', __('lang.source'), []) !!}
                            {!! Form::select('source_id', $users, false, ['class' => 'selectpicker form-control', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select'), 'required']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('notes', __('lang.notes'), []) !!}
                            {!! Form::textarea('notes', null, ['class' => 'form-control', 'placeholder' => __('lang.notes'), 'rows' => 3]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('image', __('lang.upload_picture'), []) !!} <br>
                            {!! Form::file('image', []) !!}
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" name="cash_register_id" value="{{ $cash_register_id }}">
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('lang.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lang.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker('render');
    $('#source_type').change();
    $(document).on('change', '#source_type', function() {
        if ($(this).val() !== '') {
            $.ajax({
                method: 'get',
                url: '/add-stock/get-source-by-type-dropdown/' + $(this).val(),
                data: {},
                success: function(result) {
                    $("#source_id").empty().append(result);
                    $("#source_id").selectpicker("refresh");
                },
            });
        }
    });
</script>
