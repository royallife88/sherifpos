<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CouponController@store'), 'method' => 'post', 'id' => $quick_add ?
        'quick_add_coupon_form' : 'coupon_add_form' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.generate_coupon' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('coupon_code', __( 'lang.coupon_code' ) . ':*') !!}
                <div class="input-group">
                    {!! Form::text('coupon_code',  \Keygen\Keygen::alphanum(10)->generate(), ['class' => 'form-control', 'placeholder' => __(
                    'lang.coupon_code' ), 'required' ]);
                    !!}
                    <div class="input-group-append">
                        <button type="button"
                            class="btn btn-default btn-sm refresh_code"><i class="fa fa-refresh"></i></button>
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('type', __( 'lang.type' ) . ':*') !!}
                {!! Form::select('type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], false, ['class' =>
                'form-control', 'data-live-search' => 'true']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('amount', __( 'lang.amount' ) . ':*') !!}
                {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' => __( 'lang.amount' ),
                'required' ]);
                !!}
            </div>
            <div class="form-group">
                <label class="checkbox-inline">
                    <input type="checkbox" class="amount_to_be_purchase_checkbox" name="amount_to_be_purchase_checkbox"
                        value="1">
                    @lang('lang.amount_to_be_purchase')
                </label>
                {!! Form::text('amount_to_be_purchase', null, ['class' => 'form-control amount_to_be_purchase' ,
                'placeholder' => __( 'lang.amount_to_be_purchase' ) ]);
                !!}
            </div>
            <div class="form-group">
                <label class="checkbox-inline">
                    <input type="checkbox" class="all_products" name="all_products" value="1" checked>
                    @lang('lang.all_products')
                </label>
                {!! Form::select('product_ids[]', $products, false, ['class' => 'form-control selectpicker', 'multiple',
                'data-live-search' => 'true', 'required']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('expiry_date', __( 'lang.expiry_date' ) . ':*') !!}
                {!! Form::text('expiry_date', null, ['class' => 'form-control datepicker', 'placeholder' => __(
                'lang.expiry_date' )]);
                !!}
            </div>
            <input type="hidden" name="quick_add" value="{{$quick_add }}">
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.datepicker').datepicker();
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
        console.log('asdf');
        $.ajax({
            method: 'get',
            url: '/coupon/generate-code',
            data: {  },
            success: function(result) {
                $('#coupon_code').val(result);
            },
        });
    })
</script>
