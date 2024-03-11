<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Botble\Ecommerce\Enums\ShippingStatusEnum;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\Shipment;
use Botble\Ecommerce\Models\ShipmentHistory;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function updateTracking(Request $request, $code)
    {
        $order = Order::where('code', '#' . $code)->first();
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $shipment = Shipment::where('order_id', $order->id)->first();

        $shipment->update([
            'tracking_id' => $request->input('tracking_id'),
            'tracking_link' => $request->input('tracking_link'),
            'status' => 'picked'
        ]);

        ShipmentHistory::create([
            'action'      => 'update_status',
            'description' => trans('plugins/ecommerce::shipping.changed_shipping_status', [
                'status' => ShippingStatusEnum::getLabel(ShippingStatusEnum::APPROVED),
            ]),
            'shipment_id' => $shipment->id,
            'order_id'    => $order->id,
            'user_id'     => 0,
            'created_at'  => now(),
        ]);

        return response()->json(['message' => 'Tracking updated']);
    }
}
