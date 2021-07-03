<table class="table">
    <thead>
        <tr>
            <th class="bg-danger" style="color: white">
                @lang('lang.module')
            </th>
            <th>
                @lang('lang.sub_module')
            </th>
            <th class="bg-success">
                @lang('lang.select_all')
            </th>
            <th class="bg-success">
                @lang('lang.view')
            </th>
            <th class="bg-success">
                @lang('lang.create_and_edit')
            </th>
            <th class="bg-success">
                @lang('lang.delete')
            </th>
        </tr>

       <tbody>
        @foreach ($modulePermissionArray as $key_module => $moudle)
            <tr>
                <td class="bg-danger"  style="color: white">{{$moudle}}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @if(!empty($subModulePermissionArray[$key_module]))
                @foreach ( $subModulePermissionArray[$key_module] as $key_sub_module =>  $sub_module)
                    <tr>
                        <td  class="bg-danger"></td>
                        <td>{{$sub_module}}</td>
                        <td class="bg-success">
                            {!! Form::checkbox('checked_all', 1, false, ['class' => 'checked_all']) !!}
                        </td>
                        <td class="bg-success">
                            @php
                                $view_permission = $key_module.'.'.$key_sub_module.'.view';
                                $create_and_edit_permission = $key_module.'.'.$key_sub_module.'.create_and_edit';
                                $delete_permission = $key_module.'.'.$key_sub_module.'.delete';
                            @endphp
                            {!! Form::checkbox('permissions['.$view_permission.']', 1, !empty($user) && !empty($user->hasPermissionTo($view_permission)) ? true : false, ['class' => 'check_box']) !!}
                        </td>
                        <td class="bg-success">
                            {!! Form::checkbox('permissions['.$create_and_edit_permission.']', 1, !empty($user) && !empty($user->hasPermissionTo($create_and_edit_permission)) ? true : false, ['class' => 'check_box']) !!}
                        </td>
                        <td class="bg-success">
                            {!! Form::checkbox('permissions['.$delete_permission.']', 1, !empty($user) && !empty($user->hasPermissionTo($delete_permission)) ? true : false, ['class' => 'check_box']) !!}
                        </td>
                        {{-- <td class="bg-success"></td> --}}
                    </tr>

                @endforeach
            @endif
        @endforeach
       </tbody>
    </thead>
</table>
