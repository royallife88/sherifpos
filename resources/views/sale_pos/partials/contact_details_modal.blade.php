<!-- customer_details modal -->
<div id="contact_details_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
    class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('lang.customer_details')}}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true"><i
                            class="dripicons-cross"></i></span></button>
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
                        <b>@lang('lang.due'):</b> <span class="customer_due_span"></span>
                    </div>
                    <div class="col-md-4">
                        <b>@lang('lang.points'):</b> <span class="customer_points_span"></span>
                        <input type="hidden" name="customer_points" class="customer_points" value="0">
                    </div>
                    <div class="col-md-4">
                        <b>@lang('lang.points_value'):</b> <span class="customer_points_value_span"></span>
                        <input type="hidden" name="customer_points_value" id="customer_points_value" class="customer_points_value" value="0">
                    </div>
                    <div class="col-md-4">
                        <b>@lang('lang.total_redeemable_value'):</b> <span class="customer_total_redeemable_span"></span>
                        <input type="hidden" name="customer_total_redeemable" id="customer_total_redeemable" class="customer_total_redeemable" value="0">
                        <input type="hidden" name="rp_redeemed" id="rp_redeemed" class="rp_redeemed" value="0">
                        <input type="hidden" name="rp_redeemed_value" id="rp_redeemed_value" class="rp_redeemed_value" value="0">
                    </div>
                    <input type="hidden" name="is_redeem_points" id="is_redeem_points" value="0">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary redeem_btn" id="redeem_btn" disabled>{{__('lang.redeem')}}</button>
                <button type="button" class="btn btn-secondary " data-dismiss="modal">{{__('lang.close')}}</button>
            </div>
        </div>
    </div>
</div>
