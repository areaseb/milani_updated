<?php

namespace Botble\Klarna;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove()
    {
        Setting::query()
            ->whereIn('key', [
                'payment_klarna_name',
                'payment_klarna_description',
                'payment_klarna_api_key',
                'payment_klarna_mode',
                'payment_klarna_status',
            ])
            ->delete();
    }
}
