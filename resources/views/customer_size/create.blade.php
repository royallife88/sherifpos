<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('CustomerSizeController@store'), 'method' => 'post', 'id' => $quick_add ?
        'quick_add_customer_size_form' : 'customer_size_add_form', 'files' => true ]) !!}

        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.add_customer_size' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="form-group">
                {!! Form::label('name', __( 'lang.name' ) . ':*') !!}
                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => __( 'lang.name' ), 'required'
                ]);
                !!}
            </div>
            <div class="row">
                <div class="col-md-6">
                    <table class="table">
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
                                    <input type="number" data-name="{{$key}}" name="{{$key}}[cm]"
                                        class="form-control cm_size" step="any" placeholder="@lang('lang.cm')">
                                </td>
                                <td>
                                    <input type="number" data-name="{{$key}}" name="{{$key}}[inches]"
                                        class="form-control inches_size" step="any" placeholder="@lang('lang.inches')">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
                    @include('customer_size.partial.body_graph')
                </div>
            </div>
            <input type="hidden" name="quick_add" value="{{$quick_add }}">
            <input type="hidden" name="customer_id" value="{{$customer_id }}">
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'lang.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $(document).on('change', '.cm_size', function(){
        let row = $(this).closest('tr');
        let cm_size = __read_number(row.find('.cm_size'));
        let inches_size = cm_size * 0.393701;

        __write_number(row.find('.inches_size'), inches_size);

        let name = $(this).data('name');
        show_value(row, name)
    })
    $(document).on('change', '.inches_size', function(){
        let row = $(this).closest('tr');
        let inches_size = __read_number(row.find('.inches_size'));
        let cm_size = inches_size * 2.54;

        __write_number(row.find('.cm_size'), cm_size);

        let name = $(this).data('name');
        show_value(row, name)
    })

    function show_value(row, name){
        let cm_size = __read_number(row.find('.cm_size'));

        $('.'+name+'_span').text(cm_size);
    }

</script>
