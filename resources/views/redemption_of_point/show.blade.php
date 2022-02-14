<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.redemption_of_point_system' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <b> {!! Form::label('number', __( 'lang.number' ) . ':') !!} </b> {{$redemption_of_point->number}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('store_ids', __( 'lang.store' ) . ':') !!}</b> {{implode(', ', $redemption_of_point->stores->pluck('name')->toArray())}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('earning_of_points', __( 'lang.earning_of_points' ) . ':') !!}</b> {{implode(', ', $redemption_of_point->earning_of_points->pluck('number')->toArray())}}

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('product_ids', __( 'lang.product' ) . ':') !!}</b> {{implode(', ', $redemption_of_point->products->pluck('name')->toArray())}}

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('value_of_1000_points', __( 'lang.value_of_1000_points' ) . ':') !!}</b> {{@num_format($redemption_of_point->value_of_1000_points)}}
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('start_date', __( 'lang.start_date' ) . ':') !!}</b> @if(!empty($redemption_of_point->start_date)){{@format_date($redemption_of_point->start_date)}}@endif

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <b>{!! Form::label('expiry_date', __( 'lang.expiry_date' ) . ':') !!}</b> @if(!empty($redemption_of_point->end_date)){{@format_date($redemption_of_point->end_date)}}@endif

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
