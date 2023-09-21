@php $multisafepayStatus = setting('payment_multisafepay_status'); @endphp
<table class="table payment-method-item">
    <tbody>
    <tr class="border-pay-row">
        <td class="border-pay-col"><i class="fa fa-theme-payments"></i></td>
        <td style="width: 20%;">
            <img class="filter-black" src="{{ url('vendor/core/plugins/multisafepay/images/multisafepay.png') }}" alt="MultiSafepay">
        </td>
        <td class="border-right">
            <ul>
                <li>
                    <a href="https://multisafepay.com" target="_blank">MultiSafepay</a>
                    <p>{{ trans('plugins/payment::payment.multisafepay_description') }}</p>
                </li>
            </ul>
        </td>
    </tr>
    <tr class="bg-white">
        <td colspan="3">
            <div class="float-start" style="margin-top: 5px;">
                <div class="payment-name-label-group  @if ($multisafepayStatus== 0) hidden @endif">
                    <span class="payment-note v-a-t">{{ trans('plugins/payment::payment.use') }}:</span> <label class="ws-nm inline-display method-name-label">{{ setting('payment_multisafepay_name') }}</label>
                </div>
            </div>
            <div class="float-end">
                <a class="btn btn-secondary toggle-payment-item edit-payment-item-btn-trigger @if ($multisafepayStatus == 0) hidden @endif">{{ trans('plugins/payment::payment.edit') }}</a>
                <a class="btn btn-secondary toggle-payment-item save-payment-item-btn-trigger @if ($multisafepayStatus == 1) hidden @endif">{{ trans('plugins/payment::payment.settings') }}</a>
            </div>
        </td>
    </tr>
    <tr class="multisafepay-online-payment payment-content-item hidden">
        <td class="border-left" colspan="3">
            {!! Form::open() !!}
            {!! Form::hidden('type', MULTISAFEPAY_PAYMENT_METHOD_NAME, ['class' => 'payment_type']) !!}
            <div class="row">
                <div class="col-sm-6">
                    <ul>
                        <li>
                            <label>{{ trans('plugins/payment::payment.configuration_instruction', ['name' => 'MultiSafepay']) }}</label>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6">
                    <div class="well bg-white">
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="multisafepay_name">{{ trans('plugins/payment::payment.method_name') }}</label>
                            <input type="text" class="next-input input-name" name="payment_multisafepay_name" id="multisafepay_name" data-counter="400" value="{{ setting('payment_multisafepay_name', trans('plugins/payment::payment.pay_online_via', ['name' => 'MultiSafepay'])) }}">
                        </div>
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="payment_multisafepay_description">{{ trans('core/base::forms.description') }}</label>
                            <textarea class="next-input" name="payment_multisafepay_description" id="payment_multisafepay_description">{{ get_payment_setting('description', 'multisafepay', __('You will be redirected to MultiSafepay to complete the payment.')) }}</textarea>
                        </div>
                        <p class="payment-note">
                            {{ trans('plugins/payment::payment.please_provide_information') }} <a target="_blank" href="//www.multisafepay.com">MultiSafepay</a>:
                        </p>
                        <div class="form-group mb-3">
                            <label class="text-title-field" for="multisafepay_api_key">{{ trans('plugins/payment::payment.api_key') }}</label>
                            <input type="text" class="next-input" name="payment_multisafepay_api_key" id="multisafepay_api_key" value="{{ app()->environment('demo') ? '*******************************' :setting('payment_multisafepay_api_key') }}">
                        </div>
                        {!! Form::hidden('payment_multisafepay_mode', 1) !!}
                        <div class="form-group mb-3">
                            <label class="next-label">
                                <input type="checkbox"  value="0" name="payment_multisafepay_mode" @if (setting('payment_multisafepay_mode') == 0) checked @endif>
                                {{ trans('plugins/payment::payment.sandbox_mode') }}
                            </label>
                        </div>

                        {!! apply_filters(PAYMENT_METHOD_SETTINGS_CONTENT, null, 'multisafepay') !!}
                    </div>
                </div>
            </div>
            <div class="col-12 bg-white text-end">
                <button class="btn btn-warning disable-payment-item @if ($multisafepayStatus == 0) hidden @endif" type="button">{{ trans('plugins/payment::payment.deactivate') }}</button>
                <button class="btn btn-info save-payment-item btn-text-trigger-save @if ($multisafepayStatus == 1) hidden @endif" type="button">{{ trans('plugins/payment::payment.activate') }}</button>
                <button class="btn btn-info save-payment-item btn-text-trigger-update @if ($multisafepayStatus == 0) hidden @endif" type="button">{{ trans('plugins/payment::payment.update') }}</button>
            </div>
            {!! Form::close() !!}
        </td>
    </tr>
    </tbody>
</table>
