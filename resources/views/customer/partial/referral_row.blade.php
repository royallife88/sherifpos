<div class="row referred_row">
    <input type="hidden" name="" class="ref_row_index" value="{{ $index }}">
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('referred_type', __('lang.referred_type'), []) !!}
            {!! Form::select('ref[' . $index . '][referred_type]', ['customer' => __('lang.customer'), 'supplier' => 'Supplier', 'employee' => __('lang.employee')], 'customer', ['class' => 'form-control selectpicker referred_type', 'data-live-search' => 'true']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('referred_by', __('lang.referred_by'), []) !!}
            {!! Form::select('ref[' . $index . '][referred_by][]', $customers, false, ['class' => 'form-control selectpicker referred_by', 'data-live-search' => 'true', 'data-actions-box' => 'true', 'multiple']) !!}
        </div>
    </div>
    <div class="col-md-3">
    </div>
    <div class="col-md-12 referred_details mb-4">
    </div>
</div>
