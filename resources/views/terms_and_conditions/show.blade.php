<!-- Modal -->
<div class="modal-dialog  modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="add_terms_and_condition">@lang('lang.terms_and_conditions')</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="name">@lang('lang.name'):</label><br>
                        <b>{{$terms_and_conditions->name}}</b>

                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="description">@lang('lang.description'):</label> <br>
                        <b>{!! $terms_and_conditions->description !!}</b>

                    </div>
                </div>
                <div class="col-md-12">
                        <label for="name">@lang('lang.customer_that_receive_that_tac'):</label>
                    <table class="table">
                        @foreach ($transactions as $item)
                        <tr>
                            <td> {{$item->customer->name}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>

            </div>
        </div>
        <div class="modal-footer">

            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>

    </div>
</div>
