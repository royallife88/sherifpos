<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">

            <h4 class="modal-title">@lang( 'lang.customer_size' )</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('store_id', __('lang.store'). ':', []) !!} {{$consumption->store->name ??
                            ''}}
                        </div>
                    </div>
                    <div
                        class="col-md-3">
                        <div class="form-group">
                            {!! Form::label('created_by', __('lang.chef'). ':', []) !!}
                            {{$consumption->created_by_user->name ?? ''}}
                        </div>
                    </div>
                    <div class="col-md-6">
                        {!! Form::label('date_and_time', __('lang.date_and_time') . ':', []) !!}
                        {{@format_datetime($consumption->date_and_time)}}
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <table class="table table-bordered" id="consumption_table">
                    <thead>
                        <tr>
                            <td style="width: 20%;">@lang('lang.raw_material')</td>
                            <td style="width: 20%;">@lang('lang.products')</td>
                            <td style="width: 20%;">@lang('lang.quantity')</td>
                            <td style="width: 20%;">@lang('lang.unit')</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {{$consumption->raw_material->name ?? ''}}
                            </td>
                            <td colspan="3">
                                <table style="border: 0px; width: 100%;">
                                    <tr>
                                        <td colspan="2"><label for="">@lang('lang.current_stock'):
                                                {{@num_format($current_stock)}}</label></td>
                                    </tr>
                                    @foreach ($raw_material_details as $raw_material_detail)
                                    <tr>
                                        @if(!empty($raw_material_detail->batch_number))
                                        <td><label for="">@lang('lang.batch_number'):
                                                {{$raw_material_detail->batch_number}}</label></td>
                                        @endif
                                        @if(!empty($raw_material_detail->expiry_date))
                                        <td><label for="">@lang('lang.expiry_date'):
                                                @if(!empty($raw_material_detail->expiry_date)){{@format_date($raw_material_detail->expiry_date)}}@endif</label>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </table>
                                <table style="border: 0px; width: 100%;">
                                    @foreach ($consumption->consumption_details as $consumption_product)
                                    <tr>
                                        @if(!empty($edit))
                                        <input type="hidden" name="consumption_details[{{$loop->index}}][id]"
                                            value="{{$consumption_product->id}}">
                                        @endif
                                        <td style="width: 33%;">
                                            {{$consumption_product->variation->product->name ??
                                            ''}}
                                        </td>
                                        @if(!empty($edit))
                                        <td style="width: 33%;">
                                            {{@num_format($consumption_product->quantity)}}
                                        </td>
                                        @else
                                        <td style="width: 33%;">
                                            {{@num_format($consumption_product->quantity)}}
                                        </td>
                                        @endif
                                        <td style="width: 33%;">
                                            {{$consumption_product->unit->name ?? ''}}
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default close-btn" data-dismiss="modal">@lang( 'lang.close' )</button>
        </div>

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>

</script>
