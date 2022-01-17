<section class="forms">
    <div class="container-fluid">
        <div class="col-md-12 print-only">
            @include('layouts.partials.print_header')
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4" style="33%">
                                <div class="form-group">
                                    {!! Form::label('customer', __( 'lang.customer' )) !!}:
                                    <b>{{$customer_size->customer->name ?? ''}}</b>
                                </div>
                            </div>
                            <div class="col-md-4" style="33%">
                                <div class="form-group">
                                    {!! Form::label('mobile', __( 'lang.mobile' )) !!}:
                                    <b>{{$customer_size->customer->mobile_number ?? ''}}</b>
                                </div>
                            </div>
                            <div class="col-md-4" style="33%">
                                <div class="form-group">
                                    {!! Form::label('name', __( 'lang.size_name' )) !!}: <b>{{$customer_size->name}}</b>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6" style="width: 50%;">
                                <table class="table table-bordered table-striped table-condensed">
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
                            <div class="col-md-6" style="width: 50%;">
                                @include('customer_size.partial.body_graph')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 print-only">
            @include('layouts.partials.print_footer')
        </div>
    </div>


</section>
