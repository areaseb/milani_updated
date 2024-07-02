<?php

namespace Botble\MultiSafepay\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Order;
use Botble\MultiSafepay\Http\Requests\MultiSafepayPaymentCallbackRequest;
use Botble\MultiSafepay\Services\Gateways\MultiSafepayPaymentService;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class MultiSafepayController extends Controller
{
    public function getCallback(
        Request $request,
        MultiSafepayPaymentService $multiSafepayPaymentService,
        BaseHttpResponse $response
    ) {
        Log::info('GET CALLBACK');

        $status = $multiSafepayPaymentService->getPaymentStatus($request);

        if (! $status) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage(__('Payment failed!'));
        }

        $multiSafepayPaymentService->afterMakePayment($request->input());

        return $response
            ->setNextUrl(PaymentHelper::getRedirectURL())
            ->setMessage(__('Checkout successfully!'));
    }

    public function getCallbackPost(
        Request $request,
        MultiSafepayPaymentService $multiSafepayPaymentService,
        BaseHttpResponse $response
    ) {
        $data = $request->input();

        Log::info('MULTISAFEPAY NOTIFICATION');
        Log::info(json_encode($data));

        $transaction_id = $data['order_id'];
        $status = $data['financial_status'];

        Log::info('transaction ID: ' . $transaction_id);
        Log::info('status' . $status);

        if($status) {
            $payment = Payment::where('charge_id', $transaction_id)->first();

            if($payment) {
                $payment->status = $status;
                $payment->save();

                // Update flag is_finished
                if($status == 'completed') {
                    $order = Order::find($payment->order_id);
                    if($order) {
                        $order->is_finished = true;
                        $order->save();
                    }
                }
            } else {
                Log::info('*** MULTISAFEPAY NOTIFICATION WITHOUT PAYMENT ***');
            }
        }
        
        return response('MULTISAFEPAY_OK', 200)->header('Content-Type', 'text/plain');
    }
}
