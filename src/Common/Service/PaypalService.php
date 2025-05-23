<?php

namespace AlAya\Common\Service ;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaypalService
{
    private $client;
    private $clientId;
    private $secret;
    private $apiUrl;

    public function __construct(HttpClientInterface $client,ParameterBagInterface $parameter)
    {
        $this->client = $client;
        $this->clientId = $parameter->get('paypal.client.id');
        $this->secret = $parameter->get('paypal.secret');
        $this->apiUrl = $parameter->get('paypal.api.url');
    }

    // Step 1: Get PayPal Access Token
    private function getAccessToken(): string
    {
        $response = $this->client->request('POST', $this->apiUrl.'/v1/oauth2/token', [
            'auth_basic' => [$this->clientId, $this->secret],
            'body' => 'grant_type=client_credentials',
        ]);

        return $response->toArray()['access_token'];
    }

    // Step 2: Create PayPal Order
    public function createOrder(float $amount): array
    {
        $token = $this->getAccessToken();

        $response = $this->client->request('POST', $this->apiUrl.'/v2/checkout/orders', [
            'headers' => ['Authorization' => 'Bearer '.$token, 'Content-Type' => 'application/json'],
            'json' => [
                'intent' => 'CAPTURE',
                'purchase_units' => [[
                    'amount' => ['currency_code' => 'EUR', 'value' => $amount]
                ]]
            ],
        ]);

        return $response->toArray();
    }

    // Step 3: Capture Payment
    public function captureOrder(string $orderId): array
    {
        $token = $this->getAccessToken();

        $response = $this->client->request('POST', $this->apiUrl."/v2/checkout/orders/$orderId/capture", [
            'headers' => ['Authorization' => 'Bearer '.$token, 'Content-Type' => 'application/json']
        ]);

        return $response->toArray();
    }
}
