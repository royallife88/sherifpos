<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('GiftCardController@update', $gift_card->id), 'method' => 'put', 'id' => 'gift_card_add_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.generate_gift_card' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('card_number', __( 'lang.card_number' ) . ':*') !!}
                <div class="input-group">
                    {!! Form::text('card_number',  $gift_card->card_number, ['class' => 'form-control', 'placeholder' => __(
                    'lang.card_number' ), 'required' ]);
                    !!}
                    <div class="input-group-append">
                        <button type="button"
                            class="btn btn-default btn-sm refresh_code"><i class="fa fa-refresh"></i></button>
                    </div>
                </div>
            </div>

            <div class="form-group">
                {!! Form::label('amount', __( 'lang.amount' ) . ':*') !!}
                {!! Form::text('amount', @num_format($gift_card->amount), ['class' => 'form-control', 'placeholder' => __( 'lang.amount' ),
                'required' ]);
                !!}
            </div>

            <div class="form-group">
                {!! Form::label('balance', __( 'lang.balance' ) . ':*') !!}
                {!! Form::text('balance', @num_format($gift_card->balance), ['class' => 'form-control', 'placeholder' => __( 'lang.balance' ),
                'required' ]);
                !!}
            </div>

            <div class="form-group">
                {!! Form::label('expiry_date', __( 'lang.expiry_date' ) . ':*') !!}
                {!! Form::text('expiry_date', !empty($gift_card->expiry_date) ? @format_date($gift_card->expiry_date) : null, ['class' => 'form-control datepicker', 'placeholder' => __(
                    'lang.expiry_date' )]);
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
<script>
    $('.datepicker').datepicker({
        language: '{{session('language')}}',
    });
    $('.selectpicker').selectpicker('render');
    $('.selectpicker').selectpicker('selectAll');

    $('.all_products').change(function(){
        if(!$(this).prop('checked')){
            $('.selectpicker').selectpicker('deselectAll');
        }else{
            $('.selectpicker').selectpicker('selectAll');
        }
    })
    $('.amount_to_be_purchase_checkbox').change(function(){
        if($(this).prop('checked')){
            $('.amount_to_be_purchase').attr('required', true);
        }else{
            $('.amount_to_be_purchase').attr('required', false);
        }
    })

    $('.refresh_code').click()
    $(document).on('click', '.refresh_code', function(){
        $.ajax({
            method: 'get',
            url: '/coupon/generate-code',
            data: {  },
            success: function(result) {
                $('#card_number').val(result);
            },
        });
    })
</script>
