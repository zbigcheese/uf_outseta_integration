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

        // Configure the Guzzle client
        $this->client = new HttpClient([
            'base_uri' => $this->baseUrl,
            'headers' => [
                'Authorization' => "Outseta {$this->apiKey}:{$this->secretKey}",
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'timeout' => 5.0,

            // --- PERMANENT WORKAROUND FOR LOCAL WAMP SERVER ---
            // This bypasses the local SSL certificate verification issue.
            // REMOVE THIS LINE BEFORE DEPLOYING TO A LIVE SERVER.
            'verify' => false,
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
     * Retrieves the authenticated user's profile from Outseta using an access token.
     *
     * @param string $accessToken The access token received from Outseta.
     * @return array|null The person's data as an array, or null if it fails.
     */
    public function getPersonByToken(string $accessToken): ?array
    {
        try {
            // We make a request to the /profile endpoint, but instead of our admin API key,
            // we use the user's access token as a Bearer token for authorization.
            $response = $this->client->get('profile', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
            ]);

            return json_decode((string) $response->getBody(), true);

        } catch (RequestException $e) {
            // If the token is invalid or expired, Outseta will return a 401 error.
            // We log the error and return null.
            error_log('Outseta getPersonByToken failed: ' . $e->getMessage());
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