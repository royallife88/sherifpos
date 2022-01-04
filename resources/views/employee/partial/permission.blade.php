<table class="table" id="permission_table">
    <thead>
        <tr>
            <th class="">
                @lang('lang.module') {!! Form::checkbox('all_module_check_all', 1, false, ['class' =>
                'all_module_check_all']) !!}
            </th>
            <th>
                @lang('lang.sub_module')
            </th>
            <th class="">
                @lang('lang.select_all')
            </th>
            <th class="">
                @lang('lang.view')
            </th>
            <th class="">
                @lang('lang.create_and_edit')
            </th>
            <th class="">
                @lang('lang.delete')
            </th>
        </tr>

    <tbody>
        @foreach ($modulePermissionArray as $key_module => $moudle)
        <div>
            <tr=class="module_permission" data-moudle="{{$key_module}}">
                <td class="">{{$moudle}} {!! Form::checkbox('module_check_all', 1, false, ['class' =>
                    'module_check_all']) !!}</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr=class=>
            @if(!empty($subModulePermissionArray[$key_module]))
            @php
            $sub_module_permission_array = $subModulePermissionArray[$key_module];
            @endphp
            @if(session('system_mode') == 'restaurant')
            @if($key_module == 'product_module')
            @php
            unset($sub_module_permission_array['category']);
            unset($sub_module_permission_array['sub_category']);
            unset($sub_module_permission_array['brand']);
            unset($sub_module_permission_array['color']);
            unset($sub_module_permission_array['grade']);
            @endphp
            @endif
            @endif
            @foreach ( $sub_module_permission_array as $key_sub_module => $sub_module)
            <tr class="sub_module_permission_{{$key_module}}">
                <td class=""></td>
                <td>{{$sub_module}}</td>
                <td class="">
                    {!! Form::checkbox('checked_all', 1, false, ['class' => 'checked_all']) !!}
                </td>
                <td class="">
                    @php
                    $view_permission = $key_module.'.'.$key_sub_module.'.view';
                    $create_and_edit_permission = $key_module.'.'.$key_sub_module.'.create_and_edit';
                    $delete_permission = $key_module.'.'.$key_sub_module.'.delete';
                    @endphp
                    {!! Form::checkbox('permissions['.$view_permission.']', 1, !empty($user) &&
                    !empty($user->hasPermissionTo($view_permission)) ? true : false, ['class' => 'check_box']) !!}
                </td>
                <td class="">
                    {!! Form::checkbox('permissions['.$create_and_edit_permission.']', 1, !empty($user) &&
                    !empty($user->hasPermissionTo($create_and_edit_permission)) ? true : false, ['class' =>
                    'check_box']) !!}
                </td>
                <td class="">
                    @if($delete_permission != 'sale.pos.delete' && $delete_permission != 'sale.sale.delete' &&
                    $delete_permission != 'stock.add_stock.delete')
                    {!! Form::checkbox('permissions['.$delete_permission.']', 1, !empty($user) &&
                    !empty($user->hasPermissionTo($delete_permission)) ? true : false, ['class' => 'check_box']) !!}
                    @endif
                </td>
            </tr>

            @endforeach
            @endif
        </div>
        @endforeach
    </tbody>
    </thead>
</table>
