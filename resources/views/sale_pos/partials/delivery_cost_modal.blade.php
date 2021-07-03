<!-- shipping_cost modal -->
<div id="delivery-cost-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true" class="modal fade text-left">
<div role="document" class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{__('lang.delivery')}}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
           <div class="row">
               <div class="col-md-6">
                <div class="form-group">
                    <label for="customer_name">@lang('lang.customer_name'): <span class="customer_name"></span></label>
                </div>
               </div>
               <div class="col-md-6">
                <div class="form-group">
                    <label for="address">@lang('lang.address'): <span class="customer_address"></span></label>
                </div>
               </div>
               <div class="col-md-6">
                <div class="form-group">
                    <label for="due">@lang('lang.due'): <span class="customer_due"></span></label>
                </div>
               </div>
               <div class="col-md-6"></div>
               <div class="col-md-6">
                <label for="deliveryman_id">@lang('lang.deliveryman'):</label>
                {!! Form::select('deliveryman_id', $deliverymen, false, ['class' => 'form-control selectpicker', 'data-live-search' => "true"]) !!}
               </div>
           </div>
            <button type="button" name="shipping_cost_btn" class="btn btn-primary"
                data-dismiss="modal">{{__('lang.submit')}}</button>
        </div>
    </div>
</div>
</div>
