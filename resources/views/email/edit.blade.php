@extends('layouts.app')
@section('title', __('lang.email'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.email')</h4>
                    </div>
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('employee_id', __('lang.employee'), []) !!}
                                    {!! Form::select('employee_id[]', $employees, explode(',', $email->emails), ['class' => 'form-control
                                    selectpicker', 'multiple', 'id' => 'employee_id', 'placeholder' =>
                                    __('lang.please_select')]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    {!! Form::open(['url' => action('EmailController@update', $email->id), 'method' => 'put', 'id' => 'email_form', 'files' => true,
                    ]) !!}
                    <div class="col-md-12">
                        <div class=" row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="to">{{__('lang.to')}}:
                                        <small>@lang('lang.separated_by_comma')</small></label>
                                    <input type="text" class="form-control" id="to" name="to" required
                                        value="@if(!empty($email->emails)){{$email->emails}}@endif">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="subject">{{__('lang.subject')}}:</label>
                                    <input type="text" class="form-control" id="name" name="subject" required=""
                                        value="{{$email->subject}}">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="body">{{__('lang.body')}}:</label>
                                    <textarea name="body" id="body" cols="30" rows="6" class="form-control">{!!$email->body!!}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="body">{{__('lang.attachment')}}:</label> <br>
                                    <input type="file" name="attachments[]" id="attachments" class="" multiple>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="body">{{__('lang.notes')}}:</label> <br>
                                    <textarea name="notes" id="notes" cols="30" rows="3"
                                        class="form-control">{{$email->notes}}</textarea>
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
    $('#employee_id').change(function(){
        let numbers= $(this).val();
        numbers =  numbers.filter(e =>  e);
        $('#to').val(numbers.join());

    });
    tinymce.init({
        selector: "#body",
        height: 130,
        plugins: [
            "advlist autolink lists link charmap print preview anchor textcolor",
            "searchreplace visualblocks code fullscreen",
            "insertdatetime table contextmenu paste code wordcount",
        ],
        toolbar:
            "insert | undo redo |  formatselect | bold italic backcolor  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat",
        branding: false,
    });
</script>
@endsection
