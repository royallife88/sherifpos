@extends('layouts.app')
@section('title', __('lang.dining_table'))

@section('content')
    <div class="container-fluid">

        <div class="col-md-12  no-print">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    @can('settings.dining_table.create_and_edit')
                        <a style="color: white" data-href="{{ action('DiningTableController@create') }}?from_setting=true"
                            data-container=".view_modal" class="btn btn-modal btn-info"><i class="dripicons-plus"></i>
                            @lang('lang.add')</a>
                    @endcan
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="store_table" class="table dataTable">
                            <thead>
                                <tr>
                                    <th>@lang('lang.name')</th>
                                    <th>@lang('lang.dining_room')</th>
                                    <th class="notexport">@lang('lang.action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($dining_tables as $dining_table)
                                    <tr>
                                        <td>{{ $dining_table->name }}</td>
                                        <td>{{ $dining_table->dining_room->name }}</td>
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
                                                    @can('settings.dining_table.create_and_edit')
                                                        <li>

                                                            <a data-href="{{ action('DiningTableController@edit', $dining_table->id) }}"
                                                                data-container=".view_modal" class="btn btn-modal"><i
                                                                    class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                                                        </li>
                                                        <li class="divider"></li>
                                                    @endcan
                                                    @can('settings.dining_table.delete')
                                                        <li>
                                                            <a data-href="{{ action('DiningTableController@destroy', $dining_table->id) }}"
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
        $(document).on("click", "#add_dining_table_btn", function() {
            var form = $("#dining_table_form");
            var data = form.serialize();
            $.ajax({
                url: "/dining-table",
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
