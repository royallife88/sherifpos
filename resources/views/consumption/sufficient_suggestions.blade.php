<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.remaining_qty_sufficient_for' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('raw_material', __('lang.raw_material'). ':', []) !!} {{$raw_material->name
                            ??
                            ''}}
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('current_stock', __('lang.current_stock'). ':', []) !!}
                            {{@num_format($current_stock)}}
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <table class="table table-bordered" id="consumption_table">
                    <thead>
                        <tr>
                            <td style="width: 20%;">@lang('lang.products')</td>
                            <td style="width: 20%;">@lang('lang.quantity')</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($consumption_products as $consumption_product)
                        <tr>
                            <td>
                                {{$consumption_product->variation->product->name ?? ''}}
                                @if ($consumption_product->variation->name != 'Default')
                                {{$consumption_product->variation->name}}
                                @endif
                            </td>
                            <td>{{@num_format(floor($current_stock / $consumption_product->amount_used))}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default close-btn" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>

</script>
