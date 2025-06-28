<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Services;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use UserFrosting\Config\Config;

/**
 * Service class for interacting with the Outseta API.
 */
class OutsetaService
{
    protected HttpClient $client;
    protected string $apiKey;
    protected string $secretKey;
    protected string $baseUrl;

    /**
     * Constructor.
     *
     * @param Config $config The UserFrosting config service.
     * @param HttpClient $client The Guzzle HTTP client.
     */
    public function __construct(Config $config, HttpClient $client)
    {
        $this->apiKey = $config->getString('outseta.api_key');
        $this->secretKey = $config->getString('outseta.secret_key');
        $outsetaDomain = $config->getString('outseta.domain');

        $this->baseUrl = "https://{$outsetaDomain}.outseta.com/api/v1/";

        $this->client = new HttpClient([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => "Outseta {$this->apiKey}:{$this->secretKey}",
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'timeout' => 5.0,
        ]);
    }

    /**
     * Retrieves a person from Outseta by their email address.
     *
     * @param string $email
     * @return array|null The person's data as an array, or null if not found.
     */
    public function getPersonByEmail(string $email): ?array
    {
        try {
            $response = $this->client->get('crm/people', [
                'query' => [
                    'Email' => $email,
                ]
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

    /**
     * A helper function to process the API response.
     *
     * @param ResponseInterface $response
     * @return array|null
     */
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
}