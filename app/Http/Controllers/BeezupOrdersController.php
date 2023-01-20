<?php

namespace App\Http\Controllers;

class BeezupOrdersController
{
    use CurlConfig;

    protected string $login = 'info@milanihome.it';
    protected string $password = 'Milani2020';
    public string $apiKey;

    protected string $type = 'Content-Type: application/json';
    protected string $key = 'Ocp-Apim-Subscription-Key: ';

    public string $user;

    public function __construct()
    {
       $this->user = $this->login($this->login, $this->password);
       $this->apiKey = $this->subscriptionKey()['key'];
    }
    private function login($login , $password): bool|string
    {
        $ch = curl_init();
        $this->config($ch);
        curl_setopt($ch, CURLOPT_URL, "https://api.beezup.com/v2/public/security/login");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  "login={$login}&password={$password}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $user = curl_exec($ch);
        curl_close($ch);
        return $user;
    }
    public function subscriptionKey(): array
    {
        $user = json_decode($this->user);
        $getKey = [];
        foreach ($user as $item) {
            $getKey['key'] = $item[0]->primaryToken;
        }
        return $getKey;
    }

    public function getOrders()
    {
        $ch = curl_init();
        $this->config($ch);
        curl_setopt($ch, CURLOPT_URL, "https://api.beezup.com/orders/v3/list/full");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,  '');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            $this->type,
            $this->key.$this->apiKey
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $orders = curl_exec($ch);
        curl_close($ch);

        return $orders;
    }


}
