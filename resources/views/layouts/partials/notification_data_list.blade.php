@if($notification->type == 'purchase_order')
    @if(!empty($notification->transaction))
    <li>
        <a class="{{$notification->status}} notification_item" data-mark-read-action="{{action('NotificationController@markAsRead', $notification->id)}}" data-href="{{action('PurchaseOrderController@edit', $notification->transaction_id)}}">
            <p style="margin:0px"><i class="dripicons-card"></i> @lang('lang.purchase_order') #
                {{$notification->transaction->po_no}}</p>
            <span class="text-muted">@lang('lang.new_purchase_order_created_by')
                @if(!empty($notification->created_by_user)){{$notification->created_by_user->name}}@endif</span>
        </a>

    </li>
    @endif
    @elseif($notification->type == 'quantity_alert')
    @if(!empty($notification->product))
    <li>
        <a class="{{$notification->status}} notification_item" data-mark-read-action="{{action('NotificationController@markAsRead', $notification->id)}}" data-href="{{action('ProductController@index')}}?product_id={{$notification->product->id}}">
            <p style="margin:0px"><i class="fa fa-exclamation-triangle " style="color: rgb(255, 187, 60)"></i>
                @lang('lang.alert_quantity')
                {{$notification->product->name}} ({{$notification->product->sku}})</p>
            <br>
            <span class="text-muted">@lang('lang.alert_quantity'):
                {{@num_format($notification->alert_quantity)}}</span> <br>
            <span class="text-muted">@lang('lang.in_stock'):
                {{@num_format($notification->qty_available)}}</span>
        </a>
    </li>
    @endif
    @elseif($notification->type == 'expiry_alert')
    @if(!empty($notification->product))
    <li>
        <a class="{{$notification->status}} notification_item" data-mark-read-action="{{action('NotificationController@markAsRead', $notification->id)}}" data-href="{{action('ProductController@index')}}?product_id={{$notification->product->id}}">
            <p style="margin:0px"><i class="fa fa-exclamation-triangle " style="color: rgb(255, 187, 60)"></i>
                @lang('lang.expiry_alert')</p>
            <br>
            <span class="text-muted">@lang('lang.product'):
                {{$notification->product->name}} ({{$notification->product->sku}}) @lang('lang.will_be_exired_in')
                {{$notification->days}} @lang('lang.days')</span>
            <span class="text-muted">@lang('lang.in_stock'):
                {{@num_format($notification->qty_available)}}</span>
        </a>
    </li>
    @endif
    @elseif($notification->type == 'expired')
    @if(!empty($notification->product))
    <li>
        <a class="{{$notification->status}} notification_item" data-mark-read-action="{{action('NotificationController@markAsRead', $notification->id)}}" data-href="{{action('ProductController@index')}}?product_id={{$notification->product->id}}">
            <p style="margin:0px"><i class="fa fa-exclamation-triangle " style="color: rgb(255, 19, 19)"></i>
                @lang('lang.expired')</p>
            <br>
            <span class="text-muted">@lang('lang.product'):
                {{$notification->product->name}} ({{$notification->product->sku}})
                {{strtolower(__('lang.expired'))}} {{$notification->days}} @lang('lang.days_ago')</span>
            <span class="text-muted">@lang('lang.expired_quantity'):
                {{@num_format($notification->qty_available)}}</span>
        </a>
    </li>
    @endif
    @elseif($notification->type == 'add_stock_due')
    @if(!empty($notification->transaction))
    <li>
        <a class="{{$notification->status}} notification_item" data-mark-read-action="{{action('NotificationController@markAsRead', $notification->id)}}" data-href="{{action('AddStockController@show', $notification->transaction_id)}}">
            <p style="margin:0px"><i class="fa fa-money " style="color: rgb(255, 19, 19)"></i>
                @lang('lang.due_date_for_purchase_payment')</p>
            <br>
            <span class="text-muted">@lang('lang.invoice_no'):
                {{$notification->transaction->invoice_no}}
                </span> <br>
            <span class="text-muted">@lang('lang.due_date'):
                {{@format_date($notification->transaction->due_date)}}</span>
        </a>
    </li>
    @endif
    @elseif($notification->type == 'expense_due')
    @if(!empty($notification->transaction))
    <li>
        <a class="{{$notification->status}} notification_item" data-mark-read-action="{{action('NotificationController@markAsRead', $notification->id)}}" data-href="{{action('ExpenseController@index')}}?expense_id={{$notification->transaction_id}}">
            <p style="margin:0px"><i class="fa fa-money " style="color: rgb(255, 19, 19)"></i>
                @lang('lang.due_date_for_expense')</p>
            <br>
            <span class="text-muted">@lang('lang.invoice_no'):
                {{$notification->transaction->invoice_no}}
                </span> <br>
            <span class="text-muted">@lang('lang.due_date'):
                {{@format_date($notification->transaction->next_payment_date)}}</span>
        </a>
    </li>
    @endif
@endif
