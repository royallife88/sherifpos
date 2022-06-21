@extends('layouts.app')
@section('title', __('lang.tax'))

@section('content')
    <div class="container-fluid">
        <a style="color: white" data-href="{{ action('TaxController@create') }}?type={{ $type }}"
            data-container=".view_modal" class="btn btn-modal btn-info"><i class="dripicons-plus"></i>
            @lang('lang.add')</a>

    </div>
    <div class="table-responsive">
        <table id="store_table" class="table dataTable">
            <thead>
                <tr>
                    <th>@lang('lang.name')</th>
                    <th>@lang('lang.rate_percentage')</th>
                    @if ($type == 'general_tax')
                        <th>@lang('lang.tax_method')</th>
                        <th>@lang('lang.status')</th>
                        <th>@lang('lang.status_for_stores')</th>
                    @endif
                    <th class="notexport">@lang('lang.action')</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($taxes as $tax)
                    <tr>
                        <td>{{ $tax->name }}</td>
                        <td>{{ $tax->rate }}</td>
                        @if ($type == 'general_tax')
                            <td>{{ ucfirst($tax->tax_method) }}</td>
                            <td>
                                @if ($tax->status == 1)
                                    @lang('lang.enable')
                                @else
                                    @lang('lang.disabled')
                                @endif
                            </td>
                            <td>
                                @if (!empty($tax->store_ids))
                                    {{ implode(',', $tax->stores->pluck('name')->toArray()) }}
                                @else
                                    @lang('lang.all_stores')
                                @endif
                            </td>
                        @endif
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                    @can('product_module.tax.create_and_edit')
                                        <li>
                                            <a data-href="{{ action('TaxController@edit', $tax->id) }}" class="btn-modal"
                                                data-container=".view_modal"><i
                                                    class="dripicons-document-edit btn"></i>@lang('lang.edit')</a>
                                        </li>
                                        <li class="divider"></li>
                                    @endcan
                                    @can('product_module.tax.delete')
                                        <li>
                                            <a data-href="{{ action('TaxController@destroy', $tax->id) }}"
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
            <tfoot>
            </tfoot>
        </table>
    </div>
@endsection

@section('javascript')
    <script></script>
@endsection
