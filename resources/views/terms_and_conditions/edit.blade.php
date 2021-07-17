<!-- Modal -->
<div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="edit">@lang('lang.edit')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {!! Form::open(['url' => action('TermsAndConditionsController@update', $terms_and_condition->id), 'method' =>
        'put']) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name">@lang('lang.name')</label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="{{$terms_and_condition->name}}" required>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name">@lang('lang.description')</label>
                        <textarea name="description" id="description" rows="4"
                            class="form-control">{{$terms_and_condition->description}}</textarea>
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('lang.save')</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<script>
    ed1 = CKEDITOR.replace( 'description' );
</script>
