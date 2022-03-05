@extends('layouts.app')
@section('title', __('lang.customer_point_adjustment'))

@section('content')
<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h4>@lang('lang.customer_point_adjustment')</h4>
                    </div>

                    <table class="table dataTable">
                        <thead>
                            <tr>
                                <th>@lang('lang.date_and_time')</th>
                                <th>@lang('lang.store')</th>
                                <th>@lang('lang.customer')</th>
                                <th class="sum">@lang('lang.adjustment_value')</th>
                                <th>@lang('lang.created_by')</th>
                                <th>@lang('lang.title_of_creator')</th>
                                <th>@lang('lang.notes')</th>
                                <th>@lang('lang.action')</th>
                            </tr>
                        </thead>
                    <tbody>
                        @foreach ($customer_point_adjustments as $adjustment)
                        <tr>
                            <td>{{@format_datetime($adjustment->date_and_time)}}</td>
                            <td>{{$adjustment->store->name ?? ''}}</td>
                            <td>{{$adjustment->customer->name ?? ''}}</td>
                            <td>{{@num_format($adjustment->add_new_balance)}}</td>
                            <td>{{$adjustment->created_by_user->name ?? ''}}</td>
                            <td>@if(!empty($adjustment->created_by_user->employee->job_type)){{$adjustment->created_by_user->employee->job_type->job_title}}@endif</td>
                            <td>{{$adjustment->notes}}</td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">@lang('lang.action')
                                        <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default" user="menu">
                                        @can('adjustment.customer_point_adjustment.create_and_edit')
                                        <li>

                                            <a href="{{action('CustomerPointAdjustmentController@edit', $adjustment->id)}}"
                                                 class="btn"><i
                                                    class="dripicons-document-edit"></i> @lang('lang.edit')</a>
                                        </li>
                                        <li class="divider"></li>
                                        @endcan
                                        @can('adjustment.customer_point_adjustment.delete')
                                        <li>
                                            <a data-href="{{action('CustomerPointAdjustmentController@destroy', $adjustment->id)}}"
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
                    <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <th style="text-align: right">@lang('lang.total')</th>
                            <td></td>
                        </tr>
                    </tfoot>
                    </table>

                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('javascript')
<script type="text/javascript">

</script>
@endsection
