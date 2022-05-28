<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('SupplierController@store'), 'id' => $quick_add ? 'quick_add_supplier_form' : 'supplier-form', 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data']) !!}

        <div class="modal-header">
            <h4 class="modal-title">@lang('lang.add_supplier')</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            @include('supplier.partial.create_supplier_form')
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="save-supplier">@lang('lang.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lang.close')</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker('refresh');
</script>
