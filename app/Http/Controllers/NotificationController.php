<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * change status to as read
     *
     * @param int $id
     * @return void
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::find($id);
            $notification->status = 'read';
            $notification->save();
            $output = [
                'success' => true,
                'msg' => __('lang.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('lang.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    public function notificationSeen()
    {
        Notification::where('user_id', Auth::user()->id)->where('is_seen', 0)->update(['is_seen' => 1]);

        return true;
    }
}
