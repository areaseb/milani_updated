<?php

namespace App\Services;

use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GSLinkClient
{
    protected $endpoint = '';
    protected $key = '';
    protected $iv = '';

    public function __construct()
    {
        $this->endpoint = config('gslink.endpoint');
        $this->key = config('gslink.key');
        $this->iv = config('gslink.iv');
    }

    public function export(Collection $lines, $forceUpdate = false)
    {
        $payload = [
            'righe' => $lines->toArray(),
        ];
        if ($forceUpdate) {
            $payload['ricaricaSeEsiste'] = true;
        } else {
        	 $payload['ricaricaSeEsiste'] = false;
        }

        try {
            $response = $this->client()->post('/api/sugo/DropShipping/caricaOrdini', [
                'json' => $payload,
            ]);

            $body = json_decode($response->getBody()->getContents());
            Log::info(print_r($body, true));

            return (bool) $body->success;
        } catch (Exception $e) {
            Log::info(print_r($e, true));
            return false;
        }
    }

    public function updateCustomer(Collection $line)
    {
        try {
            $response = $this->client()->post('/api/sugo/DropShipping/aggiornaCliente', [
                'json' => $line->toArray()
            ]);

            $body = json_decode($response->getBody()->getContents());           
            return (bool) $body->success;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function client()
    {
        return new Client([
            'base_uri' => $this->endpoint,
            'headers' => [
                'Content-Type' => 'application/json',
                'sugo-auth-key' => $this->getAuthKey(),
            ]
        ]);
    }

    protected function getAuthKey()
    {
        $data = [
            'randomKey' => Str::random(16),
            'timestampUTC' => Carbon::now()->setTimezone('UTC')->format('Y-m-d\TH:i:s.000\Z')
        ];

        return openssl_encrypt(json_encode($data), "aes-256-ecb", $this->key, 0);
    }
}
