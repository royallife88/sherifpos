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
                                    <tr class="tr" style="cursor: pointer;"
                                        data-href="{{ action('TutorialController@getTutorialsGuideByCategory', $item['id']) }}">
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ @format_date($item['created_at']) }}</td>
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
@endsection

@section('javascript')
    <script>
        $('#content_table').DataTable({
            "paging": false,
            "searching": false,
            "info": false,
            aaSorting: [],

        });
        $(document).on('click', '.tr', function() {
            window.location = $(this).data('href');
        });
    </script>
@endsection
