<!-- Modal -->
<div class="modal fade" id="salary_details" tabindex="-1" role="dialog" aria-labelledby="salary_detailsLabel"
    aria-hidden="true">
    <div class="modal-dialog  modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salary_details">@lang('lang.salary_details')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="i-checks" style="margin-top: 40px">
                                <input id="fixed_wage" name="fixed_wage" type="checkbox" value="1"
                                    @if (!empty($employee->fixed_wage)) checked @endif
                                    class="form-control-custom salary_checkbox">
                                <label for="fixed_wage"><strong>@lang('lang.enter_the_fixed_wage')</strong></label>
                                {!! Form::text('fixed_wage_value', !empty($employee->fixed_wage_value) ? $employee->fixed_wage_value : null, ['class' => 'form-control salary_fields', 'placeholder' => __('lang.enter_the_fixed_wage')]) !!}
                            </div>
                        </div>
                        {!! Form::select('payment_cycle', $payment_cycle, !empty($employee->payment_cycle) ? $employee->payment_cycle : null, ['class' => 'form-control salary_select selectpicker', 'data-live-search' => 'true', 'placeholder' => __('lang.select_payment_cycle')]) !!}
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="i-checks" style="margin-top: 40px">
                                <input id="commission" name="commission" type="checkbox" value="1"
                                    @if (!empty($employee->commission)) checked @endif
                                    class="form-control-custom salary_checkbox">
                                <label for="commission"><strong>@lang('lang.enter_the_commission_%')</strong></label>
                                {!! Form::text('commission_value', !empty($employee->commission_value) ? $employee->commission_value : null, ['class' => 'form-control salary_fields', 'placeholder' => __('lang.enter_the_commission_%')]) !!}
                            </div>
                        </div>
                        {!! Form::select('commission_type', $commission_type, !empty($employee->commission_type) ? $employee->commission_type : null, ['class' => 'form-control salary_select selectpicker', 'data-live-search' => 'true', 'placeholder' => __('lang.select_commission_type')]) !!}
                        <br>
                        <br>
                        {!! Form::select('commission_calculation_period', $commission_calculation_period, !empty($employee->commission_calculation_period) ? $employee->commission_calculation_period : null, ['class' => 'form-control salary_select selectpicker', 'data-live-search' => 'true', 'placeholder' => __('lang.select_commission_calculation_period')]) !!}
                        <br>
                        <br>
                        {!! Form::label('commissioned_products', __('lang.products') . ':', ['class' => 'text-muted']) !!}
                        {!! Form::select('commissioned_products[]', $products, !empty($employee->commissioned_products) ? $employee->commissioned_products : null, ['class' => 'form-control salary_select selectpicker', 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
                        <br>
                        <br>
                        {!! Form::label('commission_customer_types', __('lang.customer_types') . ':', ['class' => 'text-muted']) !!}
                        {!! Form::select('commission_customer_types[]', $customer_types, !empty($employee->commission_customer_types) ? $employee->commission_customer_types : null, ['class' => 'form-control salary_select selectpicker', 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
                        <br>
                        <br>
                        {!! Form::label('commission_stores', __('lang.stores') . ':', ['class' => 'text-muted']) !!}
                        {!! Form::select('commission_stores[]', $stores, !empty($employee->commission_stores) ? $employee->commission_stores : null, ['class' => 'form-control salary_select selectpicker', 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}
                        <br>
                        <br>
                        {!! Form::label('commission_cashiers', __('lang.cashiers') . ':', ['class' => 'text-muted']) !!}
                        {!! Form::select('commission_cashiers[]', $cashiers, !empty($employee->commission_cashiers) ? $employee->commission_cashiers : null, ['class' => 'form-control salary_select selectpicker', 'multiple', 'data-live-search' => 'true', 'data-actions-box' => 'true']) !!}

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">@lang('lang.save')</button>
                <button type="button" class="btn btn-secondary salary_cancel" data-dismiss="modal">@lang('lang.cancel')</button>
            </div>
        </div>
    </div>
</div>
