<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.customer_size' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="well">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('customer', __( 'lang.customer' )) !!}: <b>{{$customer_size->customer->name
                                ?? ''}}</b>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('mobile', __( 'lang.mobile' )) !!}:
                            <b>{{$customer_size->customer->mobile_number ?? ''}}</b>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('name', __( 'lang.size_name' )) !!}: <b>{{$customer_size->name ?? ''}}</b>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <thead>
                            <tr class="">
                                <th>@lang('lang.length_of_the_dress')</th>
                                <th>@lang('lang.cm')</th>
                                <th>@lang('lang.inches')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($getAttributeListArray as $key => $value)
                            <tr>
                                <td>
                                    <label for="">{{$value}}</label>
                                </td>
                                <td>
                                    {{@num_format($customer_size->$key['cm'])}}
                                </td>
                                <td>
                                    {{@num_format($customer_size->$key['inches'])}}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    @include('customer_size.partial.body_graph', ['customer_size' => $customer_size])
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default close-btn" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>

</script>
