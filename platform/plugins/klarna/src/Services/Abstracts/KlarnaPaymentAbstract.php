<?php

namespace Botble\Klarna\Services\Abstracts;

use Botble\Language\Facades\LanguageFacade;
use Botble\Payment\Services\Traits\PaymentErrorTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Klarna\Api\Transactions\OrderRequest;
use Klarna\Api\Transactions\OrderRequest\Arguments\CustomerDetails;
use Klarna\Api\Transactions\OrderRequest\Arguments\PaymentOptions;
use Klarna\Api\Transactions\OrderRequest\Arguments\PluginDetails;
use Klarna\Sdk;
use Klarna\ValueObject\Customer\Address;
use Klarna\ValueObject\Customer\Country;
use Klarna\ValueObject\Customer\EmailAddress;
use Klarna\ValueObject\Customer\PhoneNumber;
use Klarna\ValueObject\Money;

abstract class KlarnaPaymentAbstract
{
    use PaymentErrorTrait;

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
     * @var object
     */
    protected $client;

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

        $this->setClient();

        $this->supportRefundOnline = false;
    }

    /**
     * @return bool
     */
    public function getSupportRefundOnline()
    {
        return $this->supportRefundOnline;
    }

    /**
     * Returns Klarna SDK
     */
    public function setClient(): self
    {
        $apiKey = setting('payment_klarna_api_key', '<<API-KEY>>');
        $paymentMode = setting('payment_klarna_mode');

        $this->client = new Sdk($apiKey, $paymentMode == '1');

        return $this;
    }

    public function getClient()
    {
        return $this->client;
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
        try {
            $orderAddress = $this->getOrder()->address;

            $amount = new Money((int) $this->totalAmount * 100, $this->paymentCurrency); // Amount must be in cents!!

            $address = (new Address())
                ->addStreetName($orderAddress->address)
                ->addZipCode($orderAddress->zip_code)
                ->addCity($orderAddress->city)
                ->addState($orderAddress->state)
                ->addCountry(new Country($orderAddress->country));

            $customer = (new CustomerDetails())
                ->addFirstName($orderAddress->name)
                ->addAddress($address)
                ->addEmailAddress(new EmailAddress($orderAddress->email))
                ->addPhoneNumber(new PhoneNumber($orderAddress->phone))
                ->addLocale(LanguageFacade::getCurrentLocaleCode());

            $pluginDetails = (new PluginDetails())
                ->addApplicationName('Milani')
                ->addApplicationVersion('1.0.0')
                ->addPluginVersion('1.0.0');

            $paymentOptions = (new PaymentOptions())
                ->addNotificationUrl($this->getReturnUrl())
                ->addRedirectUrl($this->getReturnUrl())
                ->addCancelUrl($this->getCancelUrl())
                ->addCloseWindow(true);

            $orderRequest = (new OrderRequest())
                ->addType('redirect')
                ->addOrderId($paymentId)
                ->addDescriptionText($this->transactionDescription)
                ->addMoney($amount)
                ->addCustomer($customer)
                ->addDelivery($customer)
                ->addPluginDetails($pluginDetails)
                ->addPaymentOptions( $paymentOptions);

            $transactionManager = $this->getClient()->getTransactionManager()->create($orderRequest);
            $checkoutUrl = $transactionManager->getPaymentUrl();

        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception, 1);

            return false;
        }

        if ($checkoutUrl && $paymentId) {
            session(['klarna_payment_id' => $paymentId]);

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
            $transactionManager = $this->getClient()->getTransactionManager();
            $transaction = $transactionManager->get($request->input('transactionid'));

            return $transaction->getStatus() == 'completed';
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
            $transactionManager = $this->getClient()->getTransactionManager();
            return $transactionManager->get($paymentId);
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
            'EUR',
            'PLN',
            'CAD',
            'GBP',
            'SEK',
            'CHF',
            'HKD',
            'USD',
            'DKK',
            'NOK',
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
