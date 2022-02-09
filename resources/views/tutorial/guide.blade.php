@extends('layouts.app')
@section('title', __('lang.tutorial'))
<link href="https://vjs.zencdn.net/7.17.0/video-js.css" rel="stylesheet" />
@section('content')
<div class="container-fluid">

    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h4> @lang('lang.tutorials') </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse ($tutorialsDataArray as $item)
                    <div class="col-md-3">
                        <a target="_blank" href="{{$item['link']}}">
                            <div class="card video_thumb" style="width: 18rem;" data-video_src="{{$item['video']}}"
                                data-thumbnail_src="{{$item['thumbnail']}}" data-name="{{$item['name']}}"
                                data-description="{{$item['description']}}">
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
    $(document).ready(function(){
        player = videojs('my-video', {
            controls: true,
            autoplay: false,
            preload: 'auto',
            poster: '',
            src: '',
            responsive: true,
            fluid: true,
            playbackRates: [0.5, 1, 1.5, 2]
        });
    })
    $(document).on('click', '.video_thumb', function(){
        let video_src = $(this).data('video_src');
        let thumbnail_src = $(this).data('thumbnail_src');
        let name = $(this).data('name');
        let description = $(this).data('description');

        player.src({
            src: video_src,
            type: 'video/mp4'
        });
        player.poster(thumbnail_src);
        player.autoplay(false);
        player.preload('auto');

        $('.video_modal').modal('show');

    })
</script>
@endsection
