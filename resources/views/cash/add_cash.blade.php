@extends('layouts.app')
@section('title', __('lang.add_cash'))
@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.add_cash')</h4>
                    </div>
                    <div class="card-body">
                        <p class="italic"><small>@lang('lang.required_fields_info')</small></p>
                        {!! Form::open(['url' => action('CashController@saveAddCash'), 'id' => 'add-cash-form',
                        'method' =>
                        'POST', 'class' => '', 'enctype' => 'multipart/form-data']) !!}

                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('amount', __( 'lang.amount' ) . ':*') !!}
                                {!! Form::text('amount', null, ['class' => 'form-control', 'placeholder' =>
                                __(
                                'lang.amount' ), 'required' ]);
                                !!}
                            </div>
                        </div>

                        <input type="hidden" value="{{$cash_register->id}}" name="cash_register_id" id="cash_register_id">
                        <br>
                        <div class="col-md-12">
                            <div class="form-group">
                                <input type="submit" value="{{trans('lang.submit')}}" id="submit-btn"
                                    class="btn btn-primary">
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
    $('#supplier-type-form').submit(function(){
        $(this).validate();
        if($(this).valid()){
            $(this).submit();
        }
    })
</script>
@endsection
