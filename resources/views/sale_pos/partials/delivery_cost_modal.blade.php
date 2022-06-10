<!-- shipping_cost modal -->
<div id="delivery-cost-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"
    class="modal fade text-left">
    <div role="document" class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('lang.delivery') }}</h5>
                <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span
                        aria-hidden="true"><i class="dripicons-cross"></i></span></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_name">@lang('lang.customer_name'): <span
                                    class="customer_name"></span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address">@lang('lang.address'): <span class="customer_address"></span></label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="due" style="color: red;">@lang('lang.due'): <span
                                    class="customer_due"></span></label>
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <label for="delivery_zone_id">@lang('lang.delivery_zone'):</label>
                        <div class="form-group">
                            <select class="form-control selectpicker" name="delivery_zone_id" id="delivery_zone_id"
                                data-live-search="true">
                                <option value="" selected>@lang('lang.select_the_zone')</option>
                                @foreach ($delivery_zones as $key => $name)
                                    <option @if (!empty($transaction) && $transaction->delivery_zone_id == $key) selected @endif
                                        value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="manual_delivery_zone"
                                id="manual_delivery_zone" placeholder="@lang('lang.or_enter_manually')"
                                value="@if (!empty($transaction->manual_delivery_zone)) {{ $transaction->manual_delivery_zone }} @endif">
                        </div>
                    </div>
                    <div class="col-md-6"></div>
                    <div class="col-md-6">
                        <label for="deliveryman_id">@lang('lang.deliveryman'):</label>
                        <div class="form-group">
                            <select class="form-control selectpicker" name="deliveryman_id" id="deliveryman_id"
                                data-live-search="true">
                                <option value="" selected>@lang('lang.please_select')</option>
                                @foreach ($deliverymen as $key => $name)
                                    <option @if (!empty($transaction) && $transaction->deliveryman_id == $key) selected @endif
                                        value="{{ $key }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <input type="hidden" name="deliveryman_id_hidden" id="deliveryman_id_hidden"
                            value="@if (!empty($transaction)) {{ $transaction->deliveryman_id }} @endif">
                    </div>
                    <div class="col-md-6">
                        <label for="delivery_cost">@lang('lang.delivery_cost'):</label>
                        @if (auth()->user()->can('settings.delivery_zone_cost.create_and_edit'))
                            {!! Form::text('delivery_cost', !empty($transaction) ? $transaction->delivery_cost : null, ['class' => 'form-control', 'id' => 'delivery_cost']) !!}
                        @else
                            {!! Form::text('delivery_cost', !empty($transaction) ? $transaction->delivery_cost : null, ['class' => 'form-control', 'id' => 'delivery_cost', 'readonly' => true]) !!}
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="delivery_cost_paid_by_customer"
                                name="delivery_cost_paid_by_customer"
                                @if (!empty($transaction) && $transaction->delivery_cost_paid_by_customer == 0) @else checked @endif value="1"
                                id="delivery_cost_paid_by_customer">
                            @lang('lang.delivery_cost_paid_by_customer')
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label class="checkbox-inline">
                            <input type="checkbox" class="delivery_cost_given_to_deliveryman"
                                name="delivery_cost_given_to_deliveryman"
                                @if (!empty($transaction) && $transaction->delivery_cost_given_to_deliveryman == 1) checked @endif value="1"
                                id="delivery_cost_given_to_deliveryman">
                            @lang('lang.delivery_cost_given_to_deliveryman')
                        </label>
                    </div>
                    <div class="col-md-12">
                        <label for="delivery_address">@lang('lang.delivery_address'):</label>
                        {!! Form::textarea('delivery_address', !empty($transaction->delivery_address) ? $transaction->delivery_address : null, ['class' => 'form-control delivery_address', 'rows' => 2]) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" name="delivery_cost_btn" class="btn btn-primary"
                        data-dismiss="modal">{{ __('lang.submit') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
