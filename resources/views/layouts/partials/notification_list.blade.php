<style>
    .unread {
        color: black !important;
        font-weight: bold !important;
        cursor: pointer;
    }

    .read {
        opacity: 0.8 !important;
        cursor: pointer;
    }
</style>
@php
$new_notifications = App\Models\Notification::where('user_id', Auth::user()->id)->whereDate('created_at',
date('Y-m-d'))->orderBy('created_at', 'desc')->with(['created_by_user', 'product', 'transaction'])->get();
$new_count = $new_notifications->where('is_seen', 0)->count();
$earlier_notifications = App\Models\Notification::where('user_id', Auth::user()->id)->whereDate('created_at', '<', date('Y-m-d'))->orderBy('created_at', 'desc')->with(['created_by_user', 'product', 'transaction'])->limit(10)->get();
@endphp
<li class="nav-item" id="notification-icon">
    <a rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
        class="nav-link dropdown-ite notification-list"><i class="dripicons-bell"></i>
        @if($new_count > 0)
        <span class="badge badge-danger notification-number">{{$new_count}}</span>
        @endif
    </a>
    <ul class="dropdown-menu edit-options dropdown-menu-right dropdown-default notifications" user="menu">
        @forelse($new_notifications as $notification)
        @include('layouts.partials.notification_data_list', ['notification' => $notification])
        @empty
        <div class="text-center no_new_notification_div">
            <span class="text-muted" style="font-size: 12px">@lang('lang.no_new_notification')</span>
        </div>
        @endforelse

        @if($earlier_notifications->count() > 0)
        <p style="padding: 10px 10px 0px 10px; margin: 0; font-size: 14px; font-weight: bold;">@lang('lang.earlier')
        </p>
        <hr style="margin: 0px">
        @endif
        @foreach($earlier_notifications as $notification)
        @include('layouts.partials.notification_data_list', ['notification' => $notification])
        @endforeach
    </ul>
</li>
