<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_brand' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('name', __( 'lang.name' )) !!} : {{$customer_type->name}}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('store', __( 'lang.store' )) !!} :
                        @foreach ($customer_type->customer_type_store as $item)
                        {{$stores[$item->store_id]}}@if($customer_type->customer_type_store->count() > 1),@endif

                        @endforeach
                    </div>
                </div>

                <div class="col-md-8">
                    <table class="table" id="product_point_table">
                        <thead>
                            <tr>
                                <th>
                                    @lang('lang.nubmer_of_points') <i class="dripicons-question" data-toggle="tooltip"
                                        title="@lang('lang.nubmer_of_points_info')"></i>
                                </th>
                                <th>
                                    @lang('lang.product')
                                </th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($customer_type->customer_type_points->count() > 0)
                                <tr>
                                    <td>{{$data->point}}</td>
                                    <td>{{$data->product_id}}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                    <br>

                </div>
            </div>
            <div class="col-md-12">
                {!! Form::label('value_of_1000_points', __( 'lang.value_of_1000_points' )) !!}: {{$customer_type->value_of_1000_points}}

            </div>
            <br>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
