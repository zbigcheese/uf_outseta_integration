<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Services;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use UserFrosting\Config\Config; // <-- Import Config

class OutsetaService
{
    protected HttpClient $client;

    /**
     * Constructor with direct dependency injection.
     */
    public function __construct(Config $config, HttpClient $client)
    {
        $apiKey = $config->getString('outseta.api_key');
        $secretKey = $config->getString('outseta.secret_key');
        $outsetaDomain = $config->getString('outseta.domain');
        $baseUrl = "https://{$outsetaDomain}.outseta.com/api/v1/";

        // Guzzle client is now created directly here.
        // We use the injected $client and re-configure it.
        $this->client = new HttpClient([
            'base_uri' => $baseUrl,
            'headers' => [
                'Authorization' => "Outseta {$apiKey}:{$secretKey}",
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'timeout' => 5.0,
            'verify'  => false, // Keep the SSL workaround for your local env
        ]);
    }

    // All other methods (getPersonByEmail, etc.) are the same,
    // but they now use $this->client directly, not $this->getClient().
    public function getPersonByEmail(string $email): ?array
    {
        try {
            $response = $this->client->get('crm/people', [
                'query' => ['Email' => $email]
            ]);

            return $this->handleResponse($response);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() == 404) {
                return null;
            }
            error_log('Outseta API Error: ' . $e->getMessage());
            return null;
        }
    }

    public function getPersonByToken(string $accessToken): ?array
    {
        try {
            $response = $this->client->get('profile', [
                'headers' => ['Authorization' => 'Bearer ' . $accessToken]
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            error_log('Outseta getPersonByToken failed: ' . $e->getMessage());
            return null;
        }
    }

    protected function handleResponse(ResponseInterface $response): ?array
    {
        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $body = json_decode((string) $response->getBody(), true);

        if (isset($body['items']) && !empty($body['items'])) {
            return $body['items'][0];
        }

        return null;
    }

    /**
     * Retrieves a full Account object from Outseta by its UID.
     * This object contains subscription details.
     *
     * @param string $accountUid The Account UID from Outseta.
     * @return array|null The Account data, or null if not found.
     */
    public function getAccount(string $accountUid): ?array
    {
        try {
            $response = $this->client->get("crm/accounts/{$accountUid}");

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            error_log('Outseta getAccount failed: ' . $e->getMessage());
            return null;
        }
    }
}