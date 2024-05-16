@if ($payment)
    @php
        $result = $payment->getData();
        $payer = $result['customer'];
        list($order_id, $code) = explode('-', $result['order_id']);
        $order = \Botble\Ecommerce\Models\Order::find($order_id);
    @endphp
    
    <div class="alert alert-success" role="alert">
        <p class="mb-2">{{ trans('plugins/payment::payment.payment_id') }}: <strong>{{ explode('/', Request::url())[6] }}</strong></p>

        <p class="mb-2">
            {{ trans('plugins/payment::payment.details') }}:
            <strong>
                {{ $result['amount'] }} {{ $result['currency'] }} @if (!empty($result['description'])) ({{ $result['description'] }}) @endif
            </strong>
        </p>

        <p class="mb-2">{{ trans('plugins/payment::payment.payer_name') }}
            : {{ $payer['first_name'] }} {{ $payer['last_name'] }}</p>
        <p class="mb-2">{{ trans('plugins/payment::payment.email') }}: {{ $payer['email'] }}</p>
        @if (!empty($payer['phone1']))
            <p class="mb-2">{{ trans('plugins/payment::payment.phone')  }}: {{ $payer['phone1'] }}</p>
        @endif
	    <p class="mb-2">{{ trans('plugins/payment::payment.country') }}: {{ $payer['country'] }}</p>
        <p class="mb-0">
            {{ trans('plugins/payment::payment.shipping_address') }}:
            {{ $order->fullAddress }}
        </p>
    </div>

    @php
        $payment_id = $order->payment_id ?? explode('/', Request::url())[6];
        $payment_db = Botble\Payment\Models\Payment::find($payment_id);
    @endphp
    @if (!is_null($payment_db->refunded_amount))
        <br />
        <h6 class="alert-heading">{{ trans('plugins/payment::payment.refunds.title') }}</h6>
        <hr class="m-0 mb-4">
        <div class="alert alert-warning" role="alert">
            <p>{{ trans('plugins/payment::payment.amount') }}: {{ $payment_db->refunded_amount }} {{ $$result['currency'] }}</p>
            <p>{{ trans('plugins/payment::payment.refunds.status') }}: {{ $payment_db->refund_note }}</p>
        </div>
        <br />
    @endif

    @include('plugins/payment::partials.view-payment-source')

@endif
