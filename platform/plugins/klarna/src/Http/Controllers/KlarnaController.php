<?php

namespace Botble\Klarna\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Klarna\Http\Requests\KlarnaPaymentCallbackRequest;
use Botble\Klarna\Services\Gateways\KlarnaPaymentService;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class KlarnaController extends Controller
{
    public function getCallback(
        Request $request,
        KlarnaPaymentService $klarnaPaymentService,
        BaseHttpResponse $response
    ) {
        $status = $klarnaPaymentService->getPaymentStatus($request);

        if (! $status) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage(__('Payment failed!'));
        }

        $klarnaPaymentService->afterMakePayment($request->input());

        return $response
            ->setNextUrl(PaymentHelper::getRedirectURL())
            ->setMessage(__('Checkout successfully!'));
    }
}
