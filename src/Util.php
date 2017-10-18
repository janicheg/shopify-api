<?php

namespace ShopifyApi;

use Guzzle\Http\Message\Response;

/**
 * Class Util
 */
class Util
{

    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    public static function snake($value, $delimiter = '_')
    {
        if (! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', $value);

            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1'.$delimiter, $value));
        }

        return $value;
    }

    /**
     * Convert the given string to lower-case.
     *
     * @param  string  $value
     * @return string
     */
    public static function lower($value)
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * @param string $hmac
     * @param string $token
     * @param string $data
     * @return bool
     */
    public static function validWebhookHmac($hmac, $token, $data)
    {
        $calculated_hmac = hash_hmac(
            $algorithm = 'sha256',
            $data,
            $token,
            $raw_output = true
        );

        return $hmac == base64_encode($calculated_hmac);
    }

    /**
     * @param $hmac
     * @param $secret
     * @param array $data
     * @return bool
     */
    public static function validAppHmac($hmac, $secret, array $data)
    {
        $message = [];

        $keys = array_keys($data);
        sort($keys);
        foreach($keys as $key) {
            $message[] = "{$key}={$data[$key]}";
        }

        $message = implode('&', $message);

        $calculated_hmac = hash_hmac(
            $alorithm = 'sha256', 
            $message, 
            $secret
        );

        return $hmac == $calculated_hmac;
    }

    /**
     * @param Response $response
     * @return mixed
     */
    public static function getContent(Response $response)
    {
        $body    = $response->getBody(true);

        $content = json_decode($body, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return $body;
        }

        return $content;
    }

    /**
     * Generate Oauth url to install application
     *
     * @param $apiKey
     * @param $scope
     * @param $shopDomain
     * @param null $redirectUri
     * @param null $nonce
     * @return string
     */
    public static function getAuthorizeUrl($apiKey, $scope, $shopDomain, $redirectUri = null, $nonce = null)
    {
        $url = "http://{$shopDomain}/admin/oauth/authorize?client_id={$apiKey}&scope=" . urlencode($scope);
        if ($redirectUri) {
            $url .= "&redirect_uri=" . urlencode($redirectUri);
        }
        if ($nonce) {
            $url .= "&nonce=" . urlencode($nonce);
        }

        return $url;
    }

}