<!-- Modal -->
<div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="edit">@lang('lang.edit')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        {!! Form::open(['url' => action('ExpenseCategoryController@update', $expense_category->id), 'method' => 'put']) !!}
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name">@lang('lang.name')</label>
                        <input type="text" class="form-control" value="{{$expense_category->name}}" name="name" id="name">
                    </div>
                </div>

            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang('lang.save')</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('lang.close')</button>
        </div>
        {!! Form::close() !!}
    </div>
</div>
