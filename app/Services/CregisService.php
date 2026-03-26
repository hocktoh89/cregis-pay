<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CregisService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.cregis.url');
        $this->apiKey = config('services.cregis.key');
    }

    /**
     * Get supported coins for the project
     * https://developer.cregis.com/api-reference/request-apis/global/currency-query
     */
    public function getCoins()
    {

        $params = [
            'pid' => config('services.cregis.project_id'),
            'timestamp' => (int) (microtime(true) * 1000),
            'nonce' => $this->generateNonce(),
        ];

        $params['sign'] = $this->generateSignature($params);

        // Log::info('.       getCoins', $params);
        // Log::info('Checking baseUrl ', ['baseUrl' => $this->baseUrl]);
        // Log::info('Checking api key ', ['api' => $this->apiKey]);

        return Http::post("{$this->baseUrl}/api/v1/coins", $params)->json();
    }

    /**
     * Create a payout request
     */
    public function createPayout($currency, $address, $amount, $thirdPartyId, $callbackUrl = null, $remark = null)
    {
        $params = [
            'pid' => config('services.cregis.project_id'),
            'currency' => $currency,
            'address' => $address,
            'amount' => (string) $amount,
            'third_party_id' => $thirdPartyId,
            'timestamp' => (int) (microtime(true) * 1000),
            'nonce' => $this->generateNonce(),
        ];

        if ($callbackUrl) {
            $params['callback_url'] = $callbackUrl;
        }

        if ($remark) {
            $params['remark'] = $remark;
        }

        $params['sign'] = $this->generateSignature($params);

        return Http::post("{$this->baseUrl}/api/v1/payout", $params)->json();
    }

    /**
     * Query payout status
     */
    public function queryPayout($thirdPartyId)
    {
        $params = [
            'pid' => config('services.cregis.project_id'),
            'third_party_id' => $thirdPartyId,
            'timestamp' => (int) (microtime(true) * 1000),
            'nonce' => $this->generateNonce(),
        ];

        $params['sign'] = $this->generateSignature($params);

        return Http::post("{$this->baseUrl}/api/v1/payout/query", $params)->json();
    }

    /**
     * Generate signature according to Cregis documentation
     * https://developer.cregis.com/api-reference/signature
     */
    private function generateSignature($params)
    {
        // Step 1: Remove sign and empty values, then sort
        $filtered = array_filter($params, function ($value) {
            return $value !== null && $value !== '';
        });
        ksort($filtered);

        // Step 2: Concatenate as key1value1key2value2...
        $concatenated = '';
        foreach ($filtered as $key => $value) {
            $concatenated .= $key . $value;
        }

        // Step 3: Prepend API Key
        $signString = $this->apiKey . $concatenated;

        // Step 4: Calculate MD5 hash (lowercase)
        return strtolower(md5($signString));
    }

    /**
     * Generate 6-character random nonce
     */
    private function generateNonce()
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);
    }
}
