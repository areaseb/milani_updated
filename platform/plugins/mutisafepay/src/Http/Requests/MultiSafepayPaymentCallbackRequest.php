<?php

namespace Botble\MultiSafepay\Http\Requests;

use Botble\Support\Http\Requests\Request;

class MultiSafepayPaymentCallbackRequest extends Request
{
    public function rules(): array
    {
        return [
            'amount' => 'required|numeric',
            'currency' => 'required',
        ];
    }
}
