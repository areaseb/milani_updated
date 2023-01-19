<?php

namespace App\Http\Controllers;

trait CurlConfig
{
    public function config($ch)
    {
        curl_setopt( $ch, CURLOPT_ENCODING, 'UTF-8' );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, true );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
    }
}
