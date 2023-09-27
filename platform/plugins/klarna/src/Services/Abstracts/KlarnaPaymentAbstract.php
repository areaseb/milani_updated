<?php

namespace Botble\Klarna\Services\Abstracts;

use Botble\Language\Facades\LanguageFacade;
use Botble\Payment\Services\Traits\PaymentErrorTrait;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

abstract class KlarnaPaymentAbstract
{
    use PaymentErrorTrait;

    public const PRODUCTION_API_URL = 'https://api.klarna.com/';
    public const PLAYGROUND_API_URL = 'https://api.playground.klarna.com/';

    /**
     * @var array
     */
    protected $itemList;

    /**
     * @var string
     */
    protected $paymentCurrency;

    /**
     * @var int
     */
    protected $totalAmount;

    /**
     * @var string
     */
    protected $returnUrl;

    /**
     * @var string
     */
    protected $cancelUrl;

    /**
     * @var string
     */
    protected $transactionDescription;

    /**
     * @var string
     */
    protected $customer;

    /**
     * @var bool
     */
    protected $supportRefundOnline;

    /**
     * Order
     */
    protected $order;

    /**
     * KlarnaPaymentAbstract constructor.
     */
    public function __construct()
    {
        $this->paymentCurrency = config('plugins.payment.payment.currency');

        $this->totalAmount = 0;

        $this->supportRefundOnline = false;
    }

    /**
     * @return bool
     */
    public function getSupportRefundOnline()
    {
        return $this->supportRefundOnline;
    }

    public function getUsername()
    {
        return setting('payment_klarna_username', '<<USERNAME>>');
    }

    public function getPassword()
    {
        return setting('payment_klarna_password', '<<PASSWORD>>');
    }

    public function getAPIUrl()
    {
        return setting('payment_klarna_mode') == 1
            ? self::PRODUCTION_API_URL
            : self::PLAYGROUND_API_URL;
    }

    /**
     * Set payment currency
     *
     * @param string $currency String name of currency
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->paymentCurrency = $currency;

        return $this;
    }

    /**
     * Get current payment currency
     *
     * @return string Current payment currency
     */
    public function getCurrency()
    {
        return $this->paymentCurrency;
    }

    /**
     *
     * @return string
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param string $customer
     * @return self
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Add item to list
     *
     * @param array $itemData Array item data
     * @return self
     */
    public function setItem($itemData)
    {
        if (count($itemData) === count($itemData, COUNT_RECURSIVE)) {
            $itemData = [$itemData];
        }

        foreach ($itemData as $data) {
            $amount = $data['price'] * $data['quantity'];

            $item = [
                'name' => $data['name'],
                'sku' => $data['sku'],
                'unit_amount' => [
                    'currency_code' => $this->paymentCurrency,
                    'value' => $amount,
                ],
                'quantity' => $data['quantity'],
            ];

            if ($description = Arr::get($data, 'description')) {
                $item['description'] = $description;
            }

            if ($tax = Arr::get($data, 'tax')) {
                $item['tax'] = [
                    'currency_code' => $this->paymentCurrency,
                    'value' => $tax,
                ];
            }

            if ($category = Arr::get($data, 'category')) {
                $item['category'] = $category;
            }

            $this->itemList[] = $item;
            $this->totalAmount += $amount;
        }

        // issue https://developer.paypal.com/docs/api/orders/v2/#error-DECIMAL_PRECISION
        $this->totalAmount = round((float)$this->totalAmount, $this->isSupportedDecimals() ? 2 : 0);

        return $this;
    }

    /**
     * Get list item
     *
     * @return array
     */
    public function getItemList()
    {
        return $this->itemList;
    }

    /**
     * Get total amount
     *
     * @return mixed Total amount
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    /**
     * Set return URL
     *
     * @param string $url Return URL for payment process complete
     * @return self
     */
    public function setReturnUrl($url)
    {
        $this->returnUrl = $url;

        return $this;
    }

    /**
     * Get return URL
     *
     * @return string Return URL
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * Set cancel URL
     *
     * @param string $url Cancel URL for payment
     * @return self
     */
    public function setCancelUrl($url)
    {
        $this->cancelUrl = $url;

        return $this;
    }

    /**
     * Get cancel URL of payment
     *
     * @return string Cancel URL
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * Set order
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     */
    public function getOrder()
    {
        return $this->order;
    }

    public function getClient()
    {
        return new Client([
            'base_uri' => $this->getAPIUrl(),
            'auth' => [
                $this->getUsername(),
                $this->getPassword(),
            ]
        ]);
    }

    /**
     * Create payment
     *
     * @param string $transactionDescription Description for transaction
     * @return mixed Klarna checkout URL or false
     * @throws Exception
     */
    public function createPayment($transactionDescription)
    {
        $this->transactionDescription = $transactionDescription;

        $checkoutUrl = '';
        $paymentId = $this->getOrder()->id . '-' . time();
        $sessionId = '';
        try {
            $orderAddress = $this->getOrder()->address;
            $price = (int) $this->totalAmount * 100;

            // First let's create a KP session
            $data = [
                'purchase_country' => $orderAddress->country,
                'purchase_currency' => $this->paymentCurrency,
                'locale' => str_replace('_', '-', LanguageFacade::getCurrentLocaleCode()),
                'order_amount' => $price,
                'order_lines' => [
                    [
                        'reference' => $paymentId,
                        'name' => $this->transactionDescription,
                        'quantity' => 1,
                        'unit_price' => $price,
                        'total_amount' => $price,
                    ]
                ],
            ];

            $response = $this->getClient()->post('payments/v1/sessions', [
                'json' => $data,
            ]);

            $body = json_decode($response->getBody() . '', true);
            $sessionId = $body['session_id'];

            // Then let's get a redirect URL to the HPP
            // First let's create a KP session
            $data = [
                'payment_session_url' => $this->getAPIUrl() . '/payments/v1/sessions/' . $sessionId,
                'merchant_urls' => [
                    'success' => $this->getReturnUrl() . '&sid={{session_id}}&authorization_token={{authorization_token}}',
                    'cancel' => $this->getCancelUrl() . '&sid={{session_id}}',
                    'back' => $this->getCancelUrl() . '&sid={{session_id}}',
                    'failure' => $this->getCancelUrl() . '&sid={{session_id}}',
                    'error' => $this->getCancelUrl() . '&sid={{session_id}}',
                ]
            ];

            $response = $this->getClient()->post('/hpp/v1/sessions', [
                'json' => $data,
            ]);

            $body = json_decode($response->getBody() . '', true);

            // HPP session ID
            $sessionId = $body['session_id'];

            $checkoutUrl = $body['redirect_url'];

        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception, 1);

            return false;
        }

        if ($checkoutUrl && $sessionId) {
            session(['klarna_payment_id' => $sessionId]);

            return $checkoutUrl;
        }

        session()->forget('klarna_payment_id');

        return null;
    }

    /**
     * Get payment status
     *
     * @param Request $request
     * @return mixed Object payment details or false
     */
    public function getPaymentStatus(Request $request)
    {
        try {
            $response = $this->getClient()->get('hpp/v1/sessions/' . $request->input('sid'));
            $body = json_decode($response->getBody() . '', true);

            return $body['status'] == 'COMPLETED';
        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception, 1);
        }

        return false;
    }

    /**
     * Get payment details
     *
     * @param string $paymentId Klarna payment Id
     * @return mixed Object payment details
     */
    public function getPaymentDetails($paymentId)
    {
        try {
            $response = $this->getClient()->get('hpp/v1/sessions/' . $paymentId);
            $body = json_decode($response->getBody() . '', true);
            return $body;
        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception, 1);

            return false;
        }

        return false;
    }

    /**
     * Execute main service
     *
     * @param array $data
     * @return mixed
     */
    public function execute(array $data)
    {
        try {
            return $this->makePayment($data);
        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception, 1);

            return false;
        }
    }

    /**
     * @return bool
     */
    public function isSupportedDecimals()
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function supportedCurrencyCodes(): array
    {
        return [
            'AUD',
            'CAD',
            'CHF',
            'DKK',
            'EUR',
            'GBP',
            'NOK',
            'SEK',
            'USD',
        ];
    }

    /**
     * Make a payment
     *
     * @param array $data
     * @return mixed
     */
    abstract public function makePayment(array $data);

    /**
     * Use this function to perform more logic after user has made a payment
     *
     * @param array $data
     *
     * @return mixed
     */
    abstract public function afterMakePayment(array $data);
}
