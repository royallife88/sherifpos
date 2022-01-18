@extends('layouts.app')
@section('title', __('lang.customer'))
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.add_customer')</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                        {!! Form::open(['url' => action('CustomerController@store'), 'id' => 'customer-form',
                        'method' =>
                        'POST', 'class' => '', 'enctype' => 'multipart/form-data']) !!}

                        @include('customer.partial.create_customer_form')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="submit" value="{{trans('lang.submit')}}" id="submit-btn"
                                        class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $('#customer-type-form').submit(function(){
        $(this).validate();
        if($(this).valid()){
            $(this).submit();
        }
    });
    $('.add_size_btn').click(function(){
        $('.add_size_div').removeClass('hide');
    });
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
@endsection
