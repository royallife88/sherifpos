<!-- Modal -->
<div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="uploaded_files">@lang('lang.uploaded_files')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>@lang('lang.content')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($uploaded_files as $file)
                                @if (!empty($file))
                                    @if (strpos($file, 'jpg') > 0 || strpos($file, 'png') > 0 || strpos($file, 'jpeg') > 0)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td> <img src="{{ $file->getUrl() }}"
                                                    style="width: 250px; border: 2px solid #fff; padding: 4px;" /></td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <a href="{{ $file->getUrl() }}">{{ $file->file_name }}</a>
                                            </td>
                                        </tr>
                                    @endif
                                @endif
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">@lang('lang.no_file_uploaded')</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

    </div>
</div>
