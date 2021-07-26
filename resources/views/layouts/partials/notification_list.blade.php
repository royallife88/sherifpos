@php
$notifications = App\Models\Notification::where('user_id', Auth::user()->id)->where('status',
'unread')->orderBy('created_at', 'desc')->get();
@endphp
<li class="nav-item" id="notification-icon">
    <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-item"><i class="dripicons-bell"></i>
        @if($notifications->count() > 0)
        <span class="badge badge-danger notification-number">{{$notifications->count()}}</span>
        @endif
    </a>
    <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default notifications" user="menu">
        @forelse($notifications as $notification)
        @if($notification->type == 'purchase_order')
        <li>
            <a href="{{action('PurchaseOrderController@edit', $notification->transaction_id)}}">
                <p style="margin:0px"><i class="dripicons-card"></i> @lang('lang.purchase_order') #
                    {{$notification->transaction->po_no}}</p>
                <span class="text-muted">@lang('lang.new_purchase_order_created_by')
                    {{$notification->created_by_user->name}}</span>
            </a>
            <div class="mark-read">
                <a style="width: 40px;" data-toggle="tooltip" title="@lang('lang.mark_as_read')"
                    href="{{action('NotificationController@markAsRead', $notification->id)}}" class=""><i
                        class="fa fa-envelope-open"></i></a>

            </div>
        </li>
        @elseif($notification->type == 'quantity_alert')
        <li>
            <a href="#">
                <p style="margin:0px"><i class="fa fa-exclamation-triangle " style="color: rgb(255, 187, 60)"></i>
                    @lang('lang.alert_quantity')
                    {{$notification->product->name}} ({{$notification->product->sku}})</p>
                <br>
                <span class="text-muted">@lang('lang.alert_quantity'):
                    {{@num_format($notification->alert_quantity)}}</span> <br>
                <span class="text-muted">@lang('lang.in_stock'):
                    {{@num_format($notification->qty_available)}}</span>
            </a>
            <div class="mark-read">
                <a style="width: 40px;" data-toggle="tooltip" title="@lang('lang.mark_as_read')"
                    href="{{action('NotificationController@markAsRead', $notification->id)}}" class=""><i
                        class="fa fa-envelope-open"></i></a>
            </div>
        </li>
        @elseif($notification->type == 'expiry_alert')
        <li>
            <a href="#">
                <p style="margin:0px"><i class="fa fa-exclamation-triangle " style="color: rgb(255, 187, 60)"></i>
                    @lang('lang.expiry_alert')</p>
                <br>
                <span class="text-muted">@lang('lang.product'):
                    {{$notification->product->name}} ({{$notification->product->sku}}) @lang('lang.will_be_exired_in')
                    {{$notification->days}} @lang('lang.days')</span>
                <span class="text-muted">@lang('lang.in_stock'):
                    {{@num_format($notification->qty_available)}}</span>
            </a>
            <div class="mark-read">
                <a style="width: 40px;" data-toggle="tooltip" title="@lang('lang.mark_as_read')"
                    href="{{action('NotificationController@markAsRead', $notification->id)}}" class=""><i
                        class="fa fa-envelope-open"></i></a>
            </div>
        </li>
        @elseif($notification->type == 'expired')
        <li>
            <a href="#">
                <p style="margin:0px"><i class="fa fa-exclamation-triangle " style="color: rgb(255, 19, 19)"></i>
                    @lang('lang.expired')</p>
                <br>
                <span class="text-muted">@lang('lang.product'):
                    {{$notification->product->name}} ({{$notification->product->sku}})
                    {{strtolower(__('lang.expired'))}} {{$notification->days}} @lang('lang.days_ago')</span>
                <span class="text-muted">@lang('lang.expired_quantity'):
                    {{@num_format($notification->qty_available)}}</span>
            </a>
            <div class="mark-read">
                <a style="width: 40px;" data-toggle="tooltip" title="@lang('lang.mark_as_read')"
                    href="{{action('NotificationController@markAsRead', $notification->id)}}" class=""><i
                        class="fa fa-envelope-open"></i></a>
            </div>
        </li>
        @elseif($notification->type == 'add_stock_due')
        <li>
            <a href="{{action('AddStockController@show', $notification->transaction_id)}}">
                <p style="margin:0px"><i class="fa fa-money " style="color: rgb(255, 19, 19)"></i>
                    @lang('lang.due_date_for_purchase_payment')</p>
                <br>
                <span class="text-muted">@lang('lang.invoice_no'):
                    {{$notification->transaction->invoice_no}}
                    </span> <br>
                <span class="text-muted">@lang('lang.due_date'):
                    {{@format_date($notification->transaction->due_date)}}</span>
            </a>
            <div class="mark-read">
                <a style="width: 40px;" data-toggle="tooltip" title="@lang('lang.mark_as_read')"
                    href="{{action('NotificationController@markAsRead', $notification->id)}}" class=""><i
                        class="fa fa-envelope-open"></i></a>
            </div>
        </li>
        @elseif($notification->type == 'expense_due')
        <li>
            <a href="#">
                <p style="margin:0px"><i class="fa fa-money " style="color: rgb(255, 19, 19)"></i>
                    @lang('lang.due_date_for_expense')</p>
                <br>
                <span class="text-muted">@lang('lang.invoice_no'):
                    {{$notification->transaction->invoice_no}}
                    </span> <br>
                <span class="text-muted">@lang('lang.due_date'):
                    {{@format_date($notification->transaction->next_payment_date)}}</span>
            </a>
            <div class="mark-read">
                <a style="width: 40px;" data-toggle="tooltip" title="@lang('lang.mark_as_read')"
                    href="{{action('NotificationController@markAsRead', $notification->id)}}" class=""><i
                        class="fa fa-envelope-open"></i></a>
            </div>
        </li>
        @endif
        @empty
            <div class="text-center">
                <span class="text-muted" style="font-size: 12px">@lang('lang.no_new_notification')</span>
            </div>
        @endforelse
    </ul>
</li>
