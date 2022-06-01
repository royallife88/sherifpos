@extends('layouts.app')
@section('title', __('lang.modules'))

@section('content')
<div class="col-md-12  no-print">
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4>@lang('lang.modules')</h4>
        </div>
        <div class="card-body">
            {!! Form::open(['url' => action('SettingController@updateModuleSettings'), 'method' => 'post', 'enctype' =>
            'multipart/form-data']) !!}
            <div class="row">
                @foreach ($modules as $key => $name)
                @if(session('system_mode') != 'restaurant' && session('system_mode') != 'garments' && session('system_mode') != 'pos')
                @if($key == 'raw_material_module')
                @continue
                @endif
                @endif
                <div class="col-md-4">
                    <div class="i-checks">
                        <input id="{{$loop->index}}" name="module_settings[{{$key}}]" type="checkbox"
                            @if( !empty($module_settings[$key]) ) checked @endif value="1"
                            class="form-control-custom">
                        <label for="{{$loop->index}}"><strong>{{__('lang.'.$key)}}</strong></label>
                    </div>

                </div>
                @endforeach
            </div>
            <br>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">@lang('lang.save')</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection

@section('javascript')
<script>

</script>
@endsection
