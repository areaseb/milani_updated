<?php

namespace Botble\MultiSafepay;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Setting::query()
            ->whereIn('key', [
                'payment_multisafepay_name',
                'payment_multisafepay_description',
                'payment_multisafepay_api_key',
                'payment_multisafepay_mode',
                'payment_multisafepay_status',
            ])
            ->delete();
    }
}
