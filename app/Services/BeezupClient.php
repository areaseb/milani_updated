<?php

namespace App\Services;

use GuzzleHttp\Client;

class BeezupClient
{
    protected const API_ENDPOINT = "https://api.beezup.com";

    protected $apiKey = '';
    protected $storeId = '';

    public function autoimport()
    {
        $this->client()->post("/v2/user/catalogs/{$this->storeId}/autoImport/start");
    }

    protected function client()
    {
        return new Client([
            'base_uri' => self::API_ENDPOINT,
            'headers' => [
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => config('beezup.api_key')
            ]
        ]);
    }
}
