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
                    <table class="table table-bordered" id="content_table">
                        <thead>
                            <tr>
                                <th>@lang('lang.content')</th>
                                <th>@lang('lang.added_at')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tutorialsCategoryDataArray as $item)
                            <tr class="tr"
                                data-href="{{action('TutorialController@getTutorialsGuideByCategory', $item['id'])}}">
                                <td>{{$item['name']}}</td>
                                <td></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2">@lang('lang.no_item_found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{-- @forelse ($tutorialsCategoryDataArray as $item)
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
                    @endforelse --}}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
    $('#content_table').DataTable( {
        "paging":   false,
        "searching": false,
        "info":     false
    } );
    $(document).on('click', '.tr', function () {
        window.location = $(this).data('href');
    });
</script>
@endsection
