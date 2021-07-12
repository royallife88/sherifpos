<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.sales_promotion_formal_discount' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <b> {!! Form::label('name', __( 'lang.name' ) . ':') !!} </b> {{$sales_promotion->name}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('store_ids', __( 'lang.store' ) . ':') !!}</b> {{implode(', ', $sales_promotion->stores->pluck('name')->toArray())}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('customer_type_ids', __( 'lang.customer_type' ) . ':') !!}</b> {{implode(', ', $sales_promotion->customer_types->pluck('name')->toArray())}}

                    </div>
                </div>
                @if($sales_promotion->product_condition)
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('product_ids', __( 'lang.product' ) . ':') !!}</b> {{implode(', ', $sales_promotion->products->pluck('name')->toArray())}}

                    </div>
                </div>
                @endif
                @if($sales_promotion->purchase_condition)
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('purchase_condition_amount', __( 'lang.purchase_condition_amount' ) . ':') !!}</b> {{@num_format($sales_promotion->purchase_condition_amount)}}

                    </div>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('discount_type', __( 'lang.discount_type' ) . ':') !!}</b> {{ucfirst($sales_promotion->discount_type)}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('discount_value', __( 'lang.discount' ) . ':') !!}</b> {{@num_format($sales_promotion->discount_value)}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('start_date', __( 'lang.start_date' ) . ':') !!}</b> @if(!empty($sales_promotion->start_date)){{@format_date($sales_promotion->start_date)}}@endif

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('expiry_date', __( 'lang.expiry_date' ) . ':') !!}</b> @if(!empty($sales_promotion->end_date)){{@format_date($sales_promotion->end_date)}}@endif

                    </div>
                </div>
            </div>
            <br>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
