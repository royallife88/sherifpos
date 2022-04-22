@extends('layouts.app')
@section('title', __('lang.dining_room'))

@section('content')
    <div class="container-fluid">

        <div class="col-md-12  no-print">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    @can('settings.dining_room.create_and_edit')
                        <a style="color: white" data-href="{{ action('DiningRoomController@create') }}"
                            data-container=".view_modal" class="btn btn-modal btn-info"><i class="dripicons-plus"></i>
                            @lang('lang.add_dining_room')</a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="store_table" class="table dataTable">
                            <thead>
                                <tr>
                                    <th>@lang('lang.name')</th>
                                    <th class="notexport">@lang('lang.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dining_rooms as $dining_room)
                                    <tr>
                                        <td>{{ $dining_room->name }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-default btn-sm dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true"
                                                    aria-expanded="false">@lang('lang.action')
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default"
                                                    user="menu">
                                                    @can('settings.dining_room.create_and_edit')
                                                        <li>

                                                            <a data-href="{{ action('DiningRoomController@edit', $dining_room->id) }}"
                                                                data-container=".view_modal" class="btn btn-modal"><i
                                                                    class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                    @endcan
                                                    @can('settings.dining_room.delete')
                                                        <li>
                                                            <a data-href="{{ action('DiningRoomController@destroy', $dining_room->id) }}"
                                                                data-check_password="{{ action('UserController@checkPassword', Auth::user()->id) }}"
                                                                class="btn text-red delete_item"><i class="fa fa-trash"></i>
                                                                @lang('lang.delete')</a>
                                                        </li>
                                                    @endcan
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
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
        $(document).on("click", "#add_dining_room_btn", function() {
            var form = $("#dining_room_form");
            var data = form.serialize();
            $.ajax({
                url: "/dining-room",
                type: "POST",
                data: data,
                success: function(result) {
                    if (result.success === true) {
                        swal('Success', result.msg, 'success')
                        $(".view_modal").modal("hide");
                        window.location.reload();
                    } else {
                        swal('Error', result.msg, 'error')
                    }
                },
            });
        });
    </script>
@endsection
