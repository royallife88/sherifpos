
            <!-- order_tax modal -->
            <div id="tax_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
                class="modal fade text-left">
                <div role="document" class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">@lang('lang.tax')</h5>
                            <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                                    aria-hidden="true"><i class="dripicons-cross"></i></span></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <select class="form-control" name="tax_id" id="tax_id">
                                    <option value="" selected>No Tax</option>
                                    @foreach ($taxes as $tax)
                                    <option data-rate="{{$tax->rate}}" value="{{$tax->id}}">{{$tax->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="hidden" name="tax_amount" id="tax_amount">
                            <button type="button" name="tax_btn" id="tax_btn" class="btn btn-primary"
                                data-dismiss="modal">@lang('lang.submit')</button>
                        </div>
                    </div>
                </div>
            </div>
