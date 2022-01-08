<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CustomerController@postPayContactDue', $customer_details->customer_id), 'method' => 'post', 'id' =>
        'add_payment_form', 'enctype' => 'multipart/form-data' ])
        !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_payment' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="well">
                      <strong>@lang('lang.customer_name'): </strong>{{ $customer_details->name }}<br>
                      <strong>@lang('lang.mobile'): </strong>{{ $customer_details->mobile_number }}<br><br>
                    </div>
                  </div>
                <div class="col-md-6">
                    <div class="well">
                        <strong>@lang('lang.total_purchase'): </strong><span class=""
                            data-currency_symbol="true">{{ @num_format($customer_details->total_invoice) }}</span><br>
                        <strong>@lang('lang.total_paid'): </strong><span class=""
                            data-currency_symbol="true">{{ @num_format($customer_details->total_paid) }}</span><br>
                        <strong>@lang('lang.due'): </strong><span class=""
                            data-currency_symbol="true">{{ @num_format($customer_details->total_invoice - $customer_details->total_paid) }}</span><br>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <input type="hidden" name="customer_id" value="{{$customer_details->customer_id}}">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('amount', __('lang.amount'). ':*', []) !!} <br>
                        {!! Form::text('amount', @num_format($customer_details->total_invoice - $customer_details->total_paid), ['class' => 'form-control', 'placeholder'
                        => __('lang.amount')]) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('method', __('lang.payment_type'). ':*', []) !!}
                        {!! Form::select('method', $payment_type_array,
                        'cash', ['class' => 'selectpicker form-control',
                        'data-live-search'=>"true", 'required',
                        'style' =>'width: 80%' , 'placeholder' => __('lang.please_select')]) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('paid_on', __('lang.payment_date'). ':', []) !!} <br>
                        {!! Form::text('paid_on', @format_date(date('Y-m-d')), ['class' => 'form-control datepicker',
                        'readonly', 'required',
                        'placeholder' => __('lang.payment_date')]) !!}
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('upload_documents', __('lang.upload_documents'). ':', []) !!} <br>
                        <input type="file" name="upload_documents[]" id="upload_documents" multiple>
                    </div>
                </div>
                <div class="col-md-4 not_cash_fields hide">
                    <div class="form-group">
                        {!! Form::label('ref_number', __('lang.ref_number'). ':', []) !!} <br>
                        {!! Form::text('ref_number', null, ['class' => 'form-control not_cash',
                        'placeholder' => __('lang.ref_number')]) !!}
                    </div>
                </div>
                <div class="col-md-4 not_cash_fields hide">
                    <div class="form-group">
                        {!! Form::label('bank_deposit_date', __('lang.bank_deposit_date'). ':', []) !!} <br>
                        {!! Form::text('bank_deposit_date', @format_date(date('Y-m-d')), ['class' => 'form-control
                        not_cash datepicker',
                        'readonly',
                        'placeholder' => __('lang.bank_deposit_date')]) !!}
                    </div>
                </div>
                <div class="col-md-4 not_cash_fields hide">
                    <div class="form-group">
                        {!! Form::label('bank_name', __('lang.bank_name'). ':', []) !!} <br>
                        {!! Form::text('bank_name', null, ['class' => 'form-control not_cash',
                        'placeholder' => __('lang.bank_name')]) !!}
                    </div>
                </div>

                <div class="col-md-4 card_field hide">
                    <label>@lang('lang.card_number') *</label>
                    <input type="text" name="card_number" class="form-control">
                </div>
                <div class="col-md-2 card_field hide">
                    <label>@lang('lang.month')</label>
                    <input type="text" name="card_month" class="form-control">
                </div>
                <div class="col-md-2 card_field hide">
                    <label>@lang('lang.year')</label>
                    <input type="text" name="card_year" class="form-control">
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
    $('.selectpicker').selectpicker('refresh');
    $('.datepicker').datepicker({
        language: '{{session('language')}}',
    });
    $('#add_payment_form #method').change(function(){
        var method = $(this).val();

        if(method === 'card'){
            $('.card_field').removeClass('hide');
            $('.not_cash_fields').addClass('hide');
            $('.not_cash').attr('required', false);
        }
        else if(method === 'cash'){
            $('.not_cash_fields').addClass('hide');
            $('.card_field').addClass('hide');
            $('.not_cash').attr('required', false);
        }else{
            $('.not_cash_fields').removeClass('hide');
            $('.card_field').addClass('hide');
            $('.not_cash').attr('required', true);
        }
    })
</script>
