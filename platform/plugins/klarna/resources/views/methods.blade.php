@if (setting('payment_klarna_status') == 1)
    <li class="list-group-item">
        <input class="magic-radio js_payment_method" type="radio" name="payment_method" id="payment_klarna"
               @if ($selecting == KLARNA_PAYMENT_METHOD_NAME) checked @endif
               value="klarna" data-bs-toggle="collapse" data-bs-target=".payment_klarna_wrap" data-toggle="collapse" data-target=".payment_klarna_wrap" data-parent=".list_payment_method">
        <label for="payment_klarna" class="text-start">{{ setting('payment_klarna_name', trans('plugins/payment::payment.payment_via_klarna')) }}</label>
        <div class="payment_klarna_wrap payment_collapse_wrap collapse @if ($selecting == KLARNA_PAYMENT_METHOD_NAME) show @endif" style="padding: 15px 0;">
            <p>{!! BaseHelper::clean(setting('payment_klarna_description')) !!}</p>

            @php $supportedCurrencies = (new \Botble\Klarna\Services\Gateways\KlarnaPaymentService)->supportedCurrencyCodes(); @endphp
            @if (function_exists('get_application_currency') && !in_array(get_application_currency()->title, $supportedCurrencies) && !get_application_currency()->replicate()->where('title', 'USD')->exists())
                <div class="alert alert-warning" style="margin-top: 15px;">
                    {{ __(":name doesn't support :currency. List of currencies supported by :name: :currencies.", ['name' => 'Klarna', 'currency' => get_application_currency()->title, 'currencies' => implode(', ', $supportedCurrencies)]) }}

                    @php
                        $currencies = get_all_currencies();

                        $currencies = $currencies->filter(function ($item) use ($supportedCurrencies) { return in_array($item->title, $supportedCurrencies); });
                    @endphp
                    @if (count($currencies))
                        <div style="margin-top: 10px;">{{ __('Please switch currency to any supported currency') }}:&nbsp;&nbsp;
                            @foreach ($currencies as $currency)
                                <a href="{{ route('public.change-currency', $currency->title) }}" @if (get_application_currency_id() == $currency->id) class="active" @endif><span>{{ $currency->title }}</span></a>
                                @if (!$loop->last)
                                    &nbsp; | &nbsp;
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </li>
@endif
