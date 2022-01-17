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
                        <input type="number" data-name="{{$key}}" name="transaction_customer_size[{{$key}}][cm]" class="form-control cm_size" step="any"
                            value="{{@num_format($customer_size->$key['cm'])}}" placeholder="@lang('lang.cm')">
                    </td>
                    <td>
                        <input type="number" data-name="{{$key}}" name="transaction_customer_size[{{$key}}][inches]" class="form-control inches_size" step="any"
                            value="{{@num_format($customer_size->$key['inches'])}}" placeholder="@lang('lang.inches')">
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
