@extends('layouts.app')
@section('title', __('lang.sms'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.sms')</h4>
                    </div>
                    <div class="col-md-12">
                        <input id="select_all" name="select_all" type="checkbox" value="1"
                        class="form-control-custom">
                        <label for="select_all"><strong>@lang('lang.select_all')</strong></label>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('employee_id', __('lang.employee'), []) !!}
                                    {!! Form::select('employee_id[]', ['select_all' => __('lang.select_all')] + $employees, !empty($employee_mobile_number) ?
                                    [$employee_mobile_number] : false, ['class' => 'form-control selectpicker',
                                    'multiple',
                                    'data-live-search' =>'true' ,'id' => 'employee_id']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('customer_id', __('lang.customer'), []) !!}
                                    {!! Form::select('customer_id[]', ['select_all' => __('lang.select_all')] + $customers, !empty($customer_mobile_number) ?
                                    [$customer_mobile_number] : false, ['class' => 'form-control selectpicker',
                                    'multiple',
                                    'data-live-search' =>'true' ,'id' => 'customer_id']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('supplier_id', __('lang.supplier'), []) !!}
                                    {!! Form::select('supplier_id[]', ['select_all' => __('lang.select_all')] + $suppliers, !empty($supplier_mobile_number) ?
                                    [$supplier_mobile_number] : false, ['class' => 'form-control selectpicker',
                                    'multiple',
                                    'data-live-search' =>'true' ,'id' => 'supplier_id']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::open(['url' => action('SmsController@store'), 'method' => 'post', 'id' => 'sms_form'
                    ]) !!}
                    <div class="col-md-12">
                        <div class=" row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="to">{{__('lang.to')}}:
                                        <small>@lang('lang.separated_by_comma')</small></label>
                                    <input type="text" class="form-control" id="to" name="to" required
                                        value="@if(!empty($number_string)){{$number_string}}@endif">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="message">{{__('lang.message')}}:</label>
                                    <textarea name="message" id="message" cols="30" rows="6" required
                                        class="form-control"></textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="body">{{__('lang.notes')}}:</label> <br>
                                    <textarea name="notes" id="notes" cols="30" rows="3"
                                        class="form-control"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <button type="submit" name="submit" id="print" style="margin: 10px" value="save"
                            class="btn btn-primary pull-right btn-flat submit">@lang( 'lang.send' )</button>

                    </div>
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">
    $(document).ready(function(){
        $('#employee_id').change()
    })
    $('#employee_id').change(function(){
        let numbers = $(this).val()
        if(numbers.includes('select_all')){
            $('#employee_id').selectpicker('selectAll')
        }
        get_numbers()
    })
    $('#customer_id').change(function(){
        let numbers = $(this).val()
        if(numbers.includes('select_all')){
            $('#customer_id').selectpicker('selectAll')
        }
        get_numbers()
    })
    $('#supplier_id').change(function(){
        let numbers = $(this).val()
        if(numbers.includes('select_all')){
            $('#supplier_id').selectpicker('selectAll')
        }
        get_numbers()
    })



    $('#select_all').change(function(){
        if($(this).prop('checked')){
            $('#employee_id').selectpicker('selectAll')
            $('#customer_id').selectpicker('selectAll')
            $('#supplier_id').selectpicker('selectAll')
        }else{
            $('#employee_id').selectpicker('deselectAll')
            $('#customer_id').selectpicker('deselectAll')
            $('#supplier_id').selectpicker('deselectAll')
        }
        get_numbers()
    })

    function get_numbers(){
        let employee_numbers = $('#employee_id').val();
        let customer_numbers = $('#customer_id').val();
        let supplier_numbers = $('#supplier_id').val();
        let numbers = employee_numbers.concat(customer_numbers).concat(supplier_numbers);
        var list_numbers = numbers.filter(function(e) { return e !== 'select_all' })

        list_numbers =  list_numbers.filter(e =>  e);
        $('#to').val(list_numbers.join())
    }
</script>
@endsection
