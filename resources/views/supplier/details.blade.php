<!-- Modal -->
<div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="supplier">@lang('lang.supplier')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="card">
                <div class="row">
                    <div class="col-md-4">
                        {!! Form::label('name', __('lang.name'), []) !!}: <b>{{$supplier->name}}</b>
                    </div>
                    <div class="col-md-4">
                        {!! Form::label('email', __('lang.email'), []) !!}: <b>{{$supplier->email}}</b>
                    </div>
                    <div class="col-md-4">
                        {!! Form::label('mobile_number', __('lang.mobile_number'), []) !!}: <b>{{$supplier->mobile_number}}</b>
                    </div>
                    <div class="col-md-4">
                        {!! Form::label('address', __('lang.address'), []) !!}: <b>{{$supplier->address}}</b>
                    </div>
                </div>
            </div>


        </div>
        <div class="modal-footer">
            @if($is_purchase_order)
            <button type="submit" class="btn btn-primary submit"  name="submit" value="sent_supplier">@lang('lang.send')</button>
            @endif
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>


<script>

</script>
