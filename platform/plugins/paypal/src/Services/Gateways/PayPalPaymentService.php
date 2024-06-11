<?php

namespace Botble\Paypal\Services\Gateways;

use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Paypal\Services\Abstracts\PayPalPaymentAbstract;
use Exception;
use Illuminate\Support\Arr;

class PayPalPaymentService extends PayPalPaymentAbstract
{
    /**
     * Make a payment
     *
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function makePayment(array $data)
    {\Log::info('Paypal: '.print_r($data, true));
        $amount = round((float)$data['amount'], $this->isSupportedDecimals() ? 2 : 0);

        $currency = $data['currency'];
        $currency = strtoupper($currency);

        $queryParams = [
            'type' => PAYPAL_PAYMENT_METHOD_NAME,
            'amount' => $amount,
            'currency' => $currency,
            'order_id' => $data['order_id'],		//[0 => $data['orders']['items'][0]->code]
            'customer_id' => Arr::get($data, 'customer_id'),
            'customer_type' => Arr::get($data, 'customer_type'),
        ];

        if ($cancelUrl = $data['return_url'] ?: PaymentHelper::getCancelURL()) {
            $this->setCancelUrl($cancelUrl);
        }
		
		$product_list = array();
		foreach($data['products'] as $product){
			$product_list[] = [
                'name' => $product['name'],		//$data['description'],
                'description' => $product['name'],
                /*
                'quantity' => 1,
                'price' => round((float)$product['price_per_order'], $this->isSupportedDecimals() ? 2 : 0),
                */
                'quantity' => $product['qty'],
                'price' => round((float)$product['price'], $this->isSupportedDecimals() ? 2 : 0),

                'sku' => $product['sku'],
                'type' => PAYPAL_PAYMENT_METHOD_NAME,

                /*
                'shipping' => $data['shipping_amount'],
                'discount' => $data['discount_amount']
                */
            ];
		}
		
		$description = 'Pagamento del tuo ordine numero #1'.str_pad($data['order_id'][0], 7, '0', STR_PAD_LEFT).' effettuato su www.milanihome.it';

        $custom = $description . "(E-mail: " . Arr::get($data, 'address.email') . ')';
		
        return $this
            ->setReturnUrl($data['callback_url'] . '?' . http_build_query($queryParams))
            ->setCurrency($currency)
            ->setShippingAmount($data['shipping_amount'])
            ->setTaxAmount($data['tax_amount'])
            ->setDiscountAmount($data['discount_amount'])
            ->setCustomer($custom)
            ->setItem($product_list)
            ->createPayment($description);
    }

    /**
     * Use this function to perform more logic after user has made a payment
     *
     * @param array $data
     * @return mixed
     */
    public function afterMakePayment(array $data)
    {
        $status = PaymentStatusEnum::COMPLETED;

        $chargeId = session('paypal_payment_id');

        $orderIds = (array)Arr::get($data, 'order_id', []);

        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'charge_id' => $chargeId,
            'order_id' => $orderIds,
            'customer_id' => Arr::get($data, 'customer_id'),
            'customer_type' => Arr::get($data, 'customer_type'),
            'payment_channel' => PAYPAL_PAYMENT_METHOD_NAME,
            'status' => $status,
        ]);

        session()->forget('paypal_payment_id');

        return $chargeId;
    }
}
