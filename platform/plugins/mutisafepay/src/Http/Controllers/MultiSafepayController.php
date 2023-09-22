<?php

namespace Botble\MultiSafepay\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\MultiSafepay\Http\Requests\MultiSafepayPaymentCallbackRequest;
use Botble\MultiSafepay\Services\Gateways\MultiSafepayPaymentService;
use Botble\Payment\Supports\PaymentHelper;
use Illuminate\Routing\Controller;

class MultiSafepayController extends Controller
{
    public function getCallback(
        MultiSafepayPaymentCallbackRequest $request,
        MultiSafepayPaymentService $multiSafepayPaymentService,
        BaseHttpResponse $response
    ) {
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
}
