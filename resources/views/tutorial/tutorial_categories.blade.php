@extends('layouts.app')
@section('title', __('lang.content'))
@section('content')
<div class="container-fluid">

    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4> @lang('lang.content') </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse ($tutorialsCategoryDataArray as $item)
                    <div class="col-md-3">
                        <a href="{{action('TutorialController@getTutorialsGuideByCategory', $item['id'])}}">
                            <div class="card " style="width: 18rem;">
                                <img class="card-img-top"
                                    src="@if(!empty($item['thumbnail'])){{$item['thumbnail']}}@else{{asset('/uploads/' . session('logo'))}}@endif"
                                    alt="{{$item['name']}}">
                                <div class="card-body">
                                    <h5 class="card-title">{{$item['name']}}</h5>
                                    <p class="card-text">{{$item['description']}}</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    @empty
                    <div class="col-md-12 text-center">
                        <p class="text-center">@lang('lang.no_item_found')</p>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>

</script>
@endsection
