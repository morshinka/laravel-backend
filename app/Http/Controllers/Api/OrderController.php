<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\Midtrans\CreatePaymentUrlService;
use Illuminate\Http\Request;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class OrderController extends Controller
{
    public function sendNotificationToUser($userId, $message)
    {
        // untuk mendapatkan fmc token dari table user
        $user = User::find($userId);
        $token = $user->fcm_token;

        // mengirim notif ke perangkat android
        $messaging = app('firebase.messaging');
        $notification = Notification::create('Order Masuk', $message . 'Menunggu Pembayaran');

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification($notification);
        $messaging->send($message);
    }

    public function order(Request $request)
    {
        $order = Order::create([
            'user_id' => $request->user()->id,
            'seller_id' => $request->seller_id,
            'number' => time(),
            'total_price' => $request->total_price,
            'payment_status' => 1,
            'delivey_address' => $request->delivey_address
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
            ]);
        }

        $midtrans = new CreatePaymentUrlService();
        $paymentUrl = $midtrans->getPaymentUrl($order->load('user', 'orderItems'));
        $this->sendNotificationToUser($request->seller_id, 'Pesanan Baru' . $request->total_price . 'Menunggu Pembayaran');

        $order->update([
            'payment_url' => $paymentUrl
        ]);

        return response()->json([
            'data' => $order
        ]);
    }
}
