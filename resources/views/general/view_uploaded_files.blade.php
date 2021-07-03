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
                    @foreach ($uploaded_files as $file)
                    @if(!empty($file))
                    @if(strpos($file, 'jpg') > 0 || strpos($file, 'png') > 0 || strpos($file, 'jpeg') > 0)
                    <img src="{{asset('uploads/'.$file)}}" style="width: 100%; border: 2px solid #fff; padding: 4px;" />
                    @else
                    <a href="{{asset('uploads/'.$file)}}">@lang('lang.download')</a>
                    @endif
                    @endif
                    @endforeach
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

    </div>
</div>
