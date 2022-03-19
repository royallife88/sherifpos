@extends('layouts.app')
@section('title', __('lang.size'))

@section('content')
<div class="container-fluid">

    <div class="col-md-12  no-print">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                @can('product_module.size.create_and_edit')
                <a style="color: white" data-href="{{action('SizeController@create')}}"
                    data-container=".view_modal" class="btn btn-modal btn-info"><i class="dripicons-plus"></i>
                    @lang('lang.add_size')</a>
                @endcan
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="store_table" class="table dataTable">
                        <thead>
                            <tr>
                                <th>@lang('lang.name')</th>
                                <th>@lang('lang.size_code')</th>
                                <th class="notexport">@lang('lang.action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sizes as $size)
                            <tr>
                                <td>{{$size->name}}</td>
                                <td>{{$size->size_code}}</td>

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
                                            @can('product_module.size.create_and_edit')
                                            <li>

                                                <a data-href="{{action('SizeController@edit', $size->id)}}"
                                                    data-container=".view_modal" class="btn btn-modal"><i
                                                        class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                                            </li>
                                            <li class="divider"></li>
                                            @endcan
                                            @can('product_module.size.delete')
                                            <li>
                                                <a data-href="{{action('SizeController@destroy', $size->id)}}"
                                                    data-check_password="{{action('UserController@checkPassword', Auth::user()->id)}}"
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

@endsection
