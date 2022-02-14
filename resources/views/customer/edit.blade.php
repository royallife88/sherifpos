@extends('layouts.app')
@section('title', __('lang.customer'))
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.edit_customer')</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                        {!! Form::open(['url' => action('CustomerController@update', $customer->id), 'id' =>
                        'customer-form',
                        'method' =>
                        'PUT', 'class' => '', 'enctype' => 'multipart/form-data']) !!}

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('customer_type_id', __( 'lang.customer_type' ) . ':*') !!}
                                    {!! Form::select('customer_type_id', $customer_types, $customer->customer_type_id,
                                    ['class' =>
                                    'selectpicker
                                    form-control', 'data-live-search' => "true", 'required', 'placeholder' =>
                                    __('lang.please_select')]) !!}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'lang.name' ) . ':') !!}
                                    {!! Form::text('name', $customer->name, ['class' => 'form-control', 'placeholder' =>
                                    __(
                                    'lang.name' )]);
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('photo', __( 'lang.photo' ) . ':') !!} <br>
                                    {!! Form::file('image', ['class']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('mobile_number', __( 'lang.mobile_number' ) . ':') !!}
                                    {!! Form::text('mobile_number', $customer->mobile_number, ['class' =>
                                    'form-control', 'placeholder' =>
                                    __(
                                    'lang.mobile_number' ), 'required' ]);
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('address', __( 'lang.address' ) . ':') !!}
                                    {!! Form::textarea('address', $customer->address, ['class' => 'form-control', 'rows'
                                    => 3, 'placeholder' => __(
                                    'lang.address' ) ]);
                                    !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('email', __( 'lang.email' ) . ':') !!}
                                    {!! Form::email('email', $customer->email, ['class' => 'form-control', 'placeholder'
                                    => __(
                                    'lang.email' ) ]);
                                    !!}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <h3>@lang('lang.important_dates')</h3>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered" id="important_date_table">
                                    <thead>
                                        <tr>
                                            <th>@lang('lang.important_date')</th>
                                            <th>@lang('lang.date')</th>
                                            <th>@lang('lang.notify_before_days')</th>
                                            <th><button type="button" class="add_date btn btn-success btn-xs"><i
                                                        class="fa fa-plus"></i></button></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customer->customer_important_dates as $important_date)
                                        @include('customer.partial.important_date_row', ['index' => $loop->index,
                                        'important_date' => $important_date])
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <input type="hidden" name="important_date_index" id="important_date_index" value="{{$customer->customer_important_dates->count()}}">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <input type="submit" value="{{trans('lang.save')}}" id="submit-btn"
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
    })

    $(document).on('click', '.add_date', function(){
        let index = __read_number($('#important_date_index'));
        console.log(index);
        $('#important_date_index').val(index+1);

        $.ajax({
            method: 'GET',
            url: '/customer/get-important-date-row',
            data: { index: index },
            success: function(result) {
                $('#important_date_table tbody').append(result);
                $('.datepicker').datepicker()
            },
        });
    })
</script>
@endsection
