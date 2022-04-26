<!-- customer_details modal -->
<div id="contact_details_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
    class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('lang.customer_details') }}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                        aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <b>@lang('lang.name'):</b> <span class="customer_name_span"></span>
                    </div>

                    <div class="col-md-4">
                        <b>@lang('lang.mobile'):</b> <span class="customer_mobile_span"></span>
                    </div>
                    <div class="col-md-4">
                        <b>@lang('lang.due_sale_list'):</b> <span class="customer_due_span"></span>
                    </div>
                    <div class="col-md-4">
                        <b>@lang('lang.points'):</b> <span class="customer_points_span"></span>
                        <input type="hidden" name="customer_points" class="customer_points" value="0">
                    </div>
                    <div class="col-md-4">
                        <b>@lang('lang.points_value'):</b> <span class="customer_points_value_span"></span>
                        <input type="hidden" name="customer_points_value" id="customer_points_value"
                            class="customer_points_value" value="0">
                    </div>
                    <div class="col-md-4">
                        <b>@lang('lang.total_redeemable_value'):</b> <span
                            class="customer_total_redeemable_span"></span>
                        <input type="hidden" name="customer_total_redeemable" id="customer_total_redeemable"
                            class="customer_total_redeemable" value="0">
                        <input type="hidden" name="rp_redeemed" id="rp_redeemed" class="rp_redeemed" value="0">
                        <input type="hidden" name="rp_redeemed_value" id="rp_redeemed_value" class="rp_redeemed_value"
                            value="0">
                    </div>
                    <input type="hidden" name="is_redeem_points" id="is_redeem_points" value="0">
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="button" class="btn btn-primary redeem_btn pull-right" id="redeem_btn"
                            disabled>{{ __('lang.redeem') }}</button>
                    </div>
                </div>
                <div class="col-md-12 mb-5">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="customer_address">@lang('lang.address')</label>
                            {!! Form::textarea('customer_address', null, ['class' => 'form-control', 'rows' => 3, 'id' => 'customer_address']) !!}
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary" style="margin-top: 30px;"
                                id="update_customer_address">@lang('lang.update_address')</button>
                        </div>
                        <div class="col-md-3">
                            <button type="button" data-href="" class="btn btn-primary btn-modal text-white" data-container=".view_modal"  style="margin-top: 30px;"
                                id="pay_customer_due_btn">@lang('lang.pay_customer_due')</button>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <h4>@lang('lang.sales')</h4>
                        <div class="table-responsive">
                            <table id="customer_sales_table" class="table">
                                <thead>
                                    <tr>
                                        <th>@lang('lang.date_and_time')</th>
                                        <th>@lang('lang.invoice_no')</th>
                                        <th class="sum">@lang('lang.value')</th>
                                        <th>@lang('lang.payment_type')</th>
                                        <th>@lang('lang.ref_number')</th>
                                        <th>@lang('lang.status')</th>
                                        <th>@lang('lang.delivery_man')</th>
                                        <th>@lang('lang.cashier')</th>
                                        <th>@lang('lang.canceled_by')</th>
                                        <th class="notexport">@lang('lang.action')</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <th style="text-align: right"> @lang('lang.total')</th>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary " data-dismiss="modal">{{ __('lang.close') }}</button>
            </div>
        </div>
    </div>
</div>
