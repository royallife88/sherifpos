<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CustomerController@store'), 'method' => 'post', 'id' => $quick_add ?
        'quick_add_customer_form' : 'customer_add_form', 'enctype' => 'multipart/form-data' ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_customer' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            @include('customer.partial.create_customer_form')
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $('.selectpicker').selectpicker('render');
</script>
