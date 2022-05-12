<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('MoneySafeTransferController@postAddMoneyToSafe', $money_safe_id), 'method' => 'post', 'id' => 'add_money_form']) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_money_to_safe' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('source_type', __('lang.source_type') . ':*') !!}
                {!! Form::select('source_type', ['employee' => __('lang.employee'), 'safe' => __('lang.safe')], 'employee', ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('source_id', __('lang.source') . ':*') !!}
                {!! Form::select('source_id', $emplooyes, false, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'required', 'placeholder' => __('lang.please_select')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('job_type_id', __('lang.job') . ':*') !!}
                {!! Form::select('job_type_id', $job_types, false, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __('lang.please_select')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('store_id', __('lang.store') . ':*') !!}
                {!! Form::select('store_id', $stores, false, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'required', 'placeholder' => __('lang.please_select')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('amount', __('lang.amount')) !!}
                {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => __('lang.amount')]) !!}
            </div>
            <div class="form-group">
                {!! Form::label('currency_id', __('lang.currency') . ':*') !!}
                {!! Form::select('currency_id', $currencies, false, ['class' => 'form-control selectpicker', 'data-live-search' => 'true', 'required']) !!}
            </div>

            <div class="form-group">
                {!! Form::label('comments', __('lang.comments')) !!}
                {!! Form::text('comments', null, ['class' => 'form-control', 'placeholder' => __('lang.comments')]) !!}
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
    $('.selectpicker').selectpicker();
    $('select#source_id').change();
    $(document).on('change', 'select#source_type', function() {
        let source_type = $(this).val();

        if (source_type == 'employee') {
            $.ajax({
                method: 'get',
                url: '/hrm/employee/get-dropdown',
                data: {},
                success: function(result) {
                    $('#source_id').html(result);
                    $('#source_id').selectpicker('refresh');
                },
            });
        }
        if (source_type == 'safe') {
            $.ajax({
                method: 'get',
                url: '/money-safe/get-dropdown',
                data: {},
                success: function(result) {
                    $('#source_id').html(result);
                    $('#source_id').selectpicker('refresh');
                },
            });
        }
    })

    $(document).on('change', 'select#source_id', function() {
        let source_type = $('#source_type').val();
        let source_id = $(this).val();

        if (source_type == 'employee') {
            $.ajax({
                method: 'get',
                url: '/hrm/get-employee-details-by-id/' + source_id,
                data: {},
                success: function(result) {
                    console.log(result.employee.store_id[0], 'result.employee.store_id');
                    $('#store_id').val(result.employee.store_id[0]);
                    $('#store_id').selectpicker('refresh');
                    $('#job_type_id').val(result.employee.job_type_id);
                    $('#job_type_id').selectpicker('refresh');
                    if ($('select#job_type_id :selected').text() == 'Cashier') {
                        $('#comments').val("{{__('lang.closing_cash') }}");
                    } else {
                        $('#comments').val("");
                    }
                },
            });
        }
        if (source_type == 'safe') {
            $.ajax({
                method: 'get',
                url: '/money-safe/get-details-by-id/' + source_id,
                data: {},
                success: function(result) {
                    $('#store_id').val(result.store_id);
                    $('#store_id').selectpicker('refresh');
                    $('#job_type_id').val('');
                    $('#job_type_id').selectpicker('refresh');
                },
            });
        }
    })
</script>
