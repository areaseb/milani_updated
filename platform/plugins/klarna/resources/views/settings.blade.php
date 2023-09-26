@php $klarnaStatus = setting('payment_klarna_status'); @endphp
<table class="table payment-method-item">
    <tbody>
    <tr class="border-pay-row">
        <td class="border-pay-col"><i class="fa fa-theme-payments"></i></td>
        <td style="width: 20%;">
            <img class="filter-black" src="{{ url('vendor/core/plugins/klarna/images/klarna.png') }}" alt="Klarna">
        </td>
        <td class="border-right">
            <ul>
                <li>
                    <a href="https://klarna.com" target="_blank">Klarna</a>
                    <p>{{ trans('plugins/payment::payment.klarna_description') }}</p>
                </li>
            </ul>
        </td>
    </tr>
    <tr class="bg-white">
        <td colspan="3">
            <div class="float-start" style="margin-top: 5px;">
                <div class="payment-name-label-group  @if ($klarnaStatus== 0) hidden @endif">
                    <span class="payment-note v-a-t">{{ trans('plugins/payment::payment.use') }}:</span> <label class="ws-nm inline-display method-name-label">{{ setting('payment_klarna_name') }}</label>
                </div>
            </div>
            <div class="float-end">
                <a class="btn btn-secondary toggle-payment-item edit-payment-item-btn-trigger @if ($klarnaStatus == 0) hidden @endif">{{ trans('plugins/payment::payment.edit') }}</a>
                <a class="btn btn-secondary toggle-payment-item save-payment-item-btn-trigger @if ($klarnaStatus == 1) hidden @endif">{{ trans('plugins/payment::payment.settings') }}</a>
            </div>
        </td>
    </tr>
    <tr class="klarna-online-payment payment-content-item hidden">
        <td class="border-left" colspan="3">
            {!! Form::open() !!}
            {!! Form::hidden('type', KLARNA_PAYMENT_METHOD_NAME, ['class' => 'payment_type']) !!}
            <div class="row">
                <div class="col-sm-6">
                    <ul>
                        <li>
                            <label>{{ trans('plugins/payment::payment.configuration_instruction', ['name' => 'Klarna']) }}</label>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <div class="well bg-white">
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="klarna_name">{{ trans('plugins/payment::payment.method_name') }}</label>
                            <input type="text" class="next-input input-name" name="payment_klarna_name" id="klarna_name" data-counter="400" value="{{ setting('payment_klarna_name', trans('plugins/payment::payment.pay_online_via', ['name' => 'Klarna'])) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="payment_klarna_description">{{ trans('core/base::forms.description') }}</label>
                            <textarea class="next-input" name="payment_klarna_description" id="payment_klarna_description">{{ get_payment_setting('description', 'klarna', __('You will be redirected to Klarna to complete the payment.')) }}</textarea>
                        </div>
                        <p class="payment-note">
                            {{ trans('plugins/payment::payment.please_provide_information') }} <a target="_blank" href="//www.klarna.com">Klarna</a>:
                        </p>
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="klarna_api_key">{{ trans('plugins/payment::payment.api_key') }}</label>
                            <input type="text" class="next-input" name="payment_klarna_api_key" id="klarna_api_key" value="{{ app()->environment('demo') ? '*******************************' :setting('payment_klarna_api_key') }}">
                        </div>
                        {!! Form::hidden('payment_klarna_mode', 1) !!}
                        <div class="form-group mb-3">
                            <label class="next-label">
                                <input type="checkbox"  value="0" name="payment_klarna_mode" @if (setting('payment_klarna_mode') == 0) checked @endif>
                                {{ trans('plugins/payment::payment.sandbox_mode') }}
                            </label>
                        </div>

                        {!! apply_filters(PAYMENT_METHOD_SETTINGS_CONTENT, null, 'klarna') !!}
                    </div>
                </div>
            </div>
            <div class="col-12 bg-white text-end">
                <button class="btn btn-warning disable-payment-item @if ($klarnaStatus == 0) hidden @endif" type="button">{{ trans('plugins/payment::payment.deactivate') }}</button>
                <button class="btn btn-info save-payment-item btn-text-trigger-save @if ($klarnaStatus == 1) hidden @endif" type="button">{{ trans('plugins/payment::payment.activate') }}</button>
                <button class="btn btn-info save-payment-item btn-text-trigger-update @if ($klarnaStatus == 0) hidden @endif" type="button">{{ trans('plugins/payment::payment.update') }}</button>
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    </tbody>
</table>
