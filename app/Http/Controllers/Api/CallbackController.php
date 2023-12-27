<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Midtrans\CallbackService;
use App\Models\User;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function sendNotificationToUser($userId, $message)
    {
        // untuk mendapatkan fmc token dari table user
        $user = User::find($userId);
        $token = $user->fcm_token;

        // mengirim notif ke perangkat android
        $messaging = app('firebase.messaging');
        $notification = Notification::create('Order SUdah Di Bayar', $message);

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification);
        $messaging->send($message);
    }
    public function callback()
    {
        $callback = new CallbackService;

        // if ($callback->isSignatureKeyVerified()) {
        $notification = $callback->getNotification();
        $order = $callback->getOrder();

        if ($callback->isSuccess()) {
            Order::where('id', $order->id)->update([
                'payment_status' => 2,
            ]);
        }

        if ($callback->isExpire()) {
            Order::where('id', $order->id)->update([
                'payment_status' => 3,
            ]);
        }

        if ($callback->isCancelled()) {
            Order::where('id', $order->id)->update([
                'payment_status' => 3,
            ]);
        }

        $this->sendNotificationToUser($order->seller_id, 'Pesanan Baru' . $order->total_price . 'Sudah Di Bayar');

        return response()
            ->json([
                'success' => true,
                'message' => 'Notification successfully processed',
            ]);
        // } else {
        //     return response()
        //         ->json([
        //             'error' => true,
        //             'message' => 'Signature key not verified',
        //         ], 403);
        // }
    }
}
