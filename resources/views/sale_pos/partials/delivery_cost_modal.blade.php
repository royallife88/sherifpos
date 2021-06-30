<!-- shipping_cost modal -->
<div id="delivery-cost-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
aria-hidden="true" class="modal fade text-left">
<div role="document" class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">{{__('lang.Shipping Cost')}}</h5>
            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <input type="text" name="shipping_cost" class="form-control numkey" step="any">
            </div>
            <button type="button" name="shipping_cost_btn" class="btn btn-primary"
                data-dismiss="modal">{{__('lang.submit')}}</button>
        </div>
    </div>
</div>
</div>
