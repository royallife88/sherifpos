<tr>
    <td>
        @if(!empty(!empty($important_date)))
        <input type="hidden" name="important_dates[{{$index}}][id]" value="{{$important_date->id}}">
        @endif
        {!! Form::text('important_dates['.$index.'][details]', !empty($important_date) ? $important_date->details :
        null, ['class' => 'form-control',
        'placeholder' => __('lang.important_date'), 'required']) !!}
    </td>
    <td>
        {!! Form::text('important_dates['.$index.'][date]', !empty($important_date) ?
        @format_date($important_date->date) : null,
        ['class' => 'form-control datepicker',
        'placeholder' => __('lang.date'), 'required']) !!}
    </td>
    <td>
        {!! Form::text('important_dates['.$index.'][notify_before_days]', !empty($important_date) ?
        $important_date->notify_before_days : null, ['class' => 'form-control',
        'placeholder' => __('lang.notify_before_days')])
        !!}
    </td>
</tr>
