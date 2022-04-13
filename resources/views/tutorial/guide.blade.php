@extends('layouts.app')
@section('title', __('lang.tutorial'))
<link href="https://vjs.zencdn.net/7.17.0/video-js.css" rel="stylesheet" />
@section('content')
<div class="container-fluid">

    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <a style="color: white" href="{{action('TutorialController@getTutorialsCategoryGuide')}}" class="btn btn-info ml-2"><i
                    class="fa fa-arrow-left"></i>
                @lang('lang.back')</a>
            </div>
            <div class="card-body">
                <div class="row">
                    <table class="table table-bordered" id="tutorial_table">
                        <thead>
                            <tr>
                                <th>@lang('lang.tutorial')</th>
                                <th>@lang('lang.added_at')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($tutorialsDataArray as $item)
                            <tr class="tr" style="cursor: pointer;"
                                data-href="{{$item['link']}}">
                                <td>{{$item['name']}}</td>
                                <td>{{@format_date($item['created_at'])}}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2">@lang('lang.no_item_found')</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade video_modal no-print" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <video id="my-video" title="asdfasfsafd" class="video-js vjs-big-play-centered" controls preload="auto"
                    poster="">
                </video>

            </div>

        </div>
    </div>
</div><!-- /.modal-dialog -->
@endsection

<script src="https://vjs.zencdn.net/7.17.0/video.min.js"></script>
@section('javascript')
<script>
    $('#tutorial_table').DataTable( {
        "paging":   false,
        "searching": false,
        "info":     false,
        aaSorting: [],
    } );
    $(document).on('click', '.tr', function () {
        window.open($(this).data('href'), '_blank');
    });
</script>
@endsection
