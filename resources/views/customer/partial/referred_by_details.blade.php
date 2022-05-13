<div class="row mb-4">
    @foreach ($data as $key => $value)
        <div class="col-md-12 ref_details_row"
            style="border-bottom: 1px solid rgb(192, 192, 192); margin-top: 15px; margin-bottom: 15px;">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3 text-center mt-4">
                        <h6>{{ $value }}</h6>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('reward_system', __('lang.reward_system'), []) !!}
                            {!! Form::select('referred[' . $index . '][' . $key . '][reward_system][]', ['money' => __('lang.money'), 'loyalty_point' => __('lang.loyalty_point'), 'gift_card' => __('lang.gift_card'), 'discount' => __('lang.discount')], false, ['class' => 'form-control selectpicker reward_system', 'data-live-search' => 'true', 'multiple']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="row hidden_fields money_fields hide">
                    <div class="col-md-12">
                        <h6>@lang('lang.money')</h6>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('source_type', __('lang.source_type'), []) !!} <br>
                            {!! Form::select('reward_system[' . $index . '][' . $key . '][money][source_type]', ['user' => __('lang.user'), 'pos' => __('lang.pos'), 'store' => __('lang.store')], 'user', ['class' => 'selectpicker form-control source_type', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('source_of_payment', __('lang.source_of_payment'), []) !!} <br>
                            {!! Form::select('reward_system[' . $index . '][' . $key . '][money][source_id]', $users, null, ['class' => 'selectpicker form-control source_id', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select'), 'id' => 'source_id']) !!}
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('payment_status', __('lang.payment_status') . ':*', []) !!}
                            {!! Form::select('reward_system[' . $index . '][' . $key . '][money][payment_status]', $payment_status_array, null, ['class' => 'selectpicker form-control payment_status', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>

                    <div class="col-md-3 payment_fields hide">
                        <div class="form-group">
                            {!! Form::label('amount', __('lang.amount') . ':*', []) !!} <br>
                            {!! Form::text('reward_system[' . $index . '][' . $key . '][money][amount]', !empty($payment) ? $payment->amount : null, ['class' => 'form-control', 'placeholder' => __('lang.amount')]) !!}
                        </div>
                    </div>

                    <div class="col-md-3 payment_fields hide">
                        <div class="form-group">
                            {!! Form::label('method', __('lang.payment_type') . ':*', []) !!}
                            {!! Form::select('reward_system[' . $index . '][' . $key . '][money][method]', $payment_type_array, !empty($payment) ? $payment->method : 'cash', ['class' => 'selectpicker form-control method', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>

                    <div class="col-md-3 payment_fields hide">
                        <div class="form-group">
                            {!! Form::label('paid_on', __('lang.payment_date') . ':', []) !!} <br>
                            {!! Form::text('reward_system[' . $index . '][' . $key . '][money][paid_on]', !empty($payment) ? @format_date($payment->paid_on) : @format_date(date('Y-m-d')), ['class' => 'form-control datepicker', 'placeholder' => __('lang.payment_date')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3 not_cash_fields hide">
                        <div class="form-group">
                            {!! Form::label('ref_number', __('lang.ref_number') . ':', []) !!} <br>
                            {!! Form::text('reward_system[' . $index . '][' . $key . '][money][ref_number]', !empty($payment) ? $payment->ref_number : null, ['class' => 'form-control not_cash', 'placeholder' => __('lang.ref_number')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3 not_cash_fields hide">
                        <div class="form-group">
                            {!! Form::label('bank_deposit_date', __('lang.bank_deposit_date') . ':', []) !!} <br>
                            {!! Form::text('reward_system[' . $index . '][' . $key . '][money][bank_deposit_date]', !empty($payment) ? @format_date($payment->bank_deposit_date) : null, ['class' => 'form-control not_cash datepicker', 'placeholder' => __('lang.bank_deposit_date')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3 not_cash_fields hide">
                        <div class="form-group">
                            {!! Form::label('bank_name', __('lang.bank_name') . ':', []) !!} <br>
                            {!! Form::text('reward_system[' . $index . '][' . $key . '][money][bank_name]', !empty($payment) ? $payment->bank_name : null, ['class' => 'form-control not_cash', 'placeholder' => __('lang.bank_name')]) !!}
                        </div>
                    </div>
                    <hr>
                </div>

                <div class="row hidden_fields loyalty_point_fields hide">
                    <div class="col-md-12">
                        <h6>@lang('lang.loyalty_point')</h6>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('points', __('lang.points') . ':', []) !!} <br>
                            {!! Form::text('reward_system[' . $index . '][' . $key . '][loyalty_point][loyalty_points]', 0, ['class' => 'form-control', 'placeholder' => __('lang.points')]) !!}
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="row hidden_fields gift_card_fields hide mb-2">
                    <div class="col-md-12">
                        <h6>@lang('lang.gift_card')</h6>
                    </div>
                    <div class="col-md-2">
                        <input type="hidden"
                            name="reward_system[{{$index}}][{{ $key }}][gift_card][gift_card_id]"
                            class="gift_card_id" value="">
                        @can('coupons_and_gift_cards.gift_card.create_and_edit')
                            <a style="color: white" data-href="{{ action('GiftCardController@create') }}"
                                data-container=".view_modal" class="btn add-gift-card btn-info"><i
                                    class="dripicons-plus"></i>
                                @lang('lang.generate_gift_card')</a>
                        @endcan
                    </div>
                    <div class="col-md-3">
                        <h6>@lang('lang.card_number'): <span class="gift_card_number"></span></h6>
                    </div>
                    <hr>
                </div>
                <div class="row hidden_fields discount_fields hide">
                    <div class="col-md-12">
                        <h6>@lang('lang.discount')</h6>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('lang.store'), []) !!}
                            {!! Form::select('reward_system[' . $index . '][' . $key . '][discount][store_id]', $stores, false, ['class' => 'selectpicker form-control store_id', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        @include('customer.partial.product_selection_tree', [
                            'index' => $index, 'key' => $key,
                        ])
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('discount_type', __('lang.discount_type'), []) !!}
                            {!! Form::select('reward_system[' . $index . '][' . $key . '][discount][discount_type]', ['fixed' => __('lang.fixed'), 'percentage' => __('lang.percentage')], false, ['class' => 'selectpicker form-control discount_type', 'data-live-search' => 'true', 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('discount_amount', __('lang.amount'), []) !!}
                            {!! Form::text('reward_system[' . $index . '][' . $key . '][discount][discount]', null, ['class' => 'form-control discount', 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('discount_expiry', __('lang.expiry'), []) !!}
                            {!! Form::text('reward_system[' . $index . '][' . $key . '][discount][discount_expiry]', null, ['class' => 'datepicker form-control discount_expiry', 'placeholder' => __('lang.please_select')]) !!}
                        </div>
                    </div>

                    <hr>
                </div>
            </div>
        </div>
    @endforeach
</div>
<script>
    $('.datepicker').datepicker({
        language: '{{ session('language') }}',
    });
</script>
