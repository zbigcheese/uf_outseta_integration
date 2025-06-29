<?php

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Services;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Container\ContainerInterface; // <-- Import this
use Psr\Http\Message\ResponseInterface;

/**
 * Service class for interacting with the Outseta API.
 */
class OutsetaService
{
    protected ContainerInterface $ci;
    protected ?HttpClient $client = null; // The client is now nullable and starts as null

    /**
     * The constructor is now very simple. It just accepts the main service container.
     *
     * @param ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    /**
     * A private "getter" method to initialize the HTTP client only when it's first needed.
     */
    private function getClient(): HttpClient
    {
        // If the client has already been created, just return it.
        if ($this->client !== null) {
            return $this->client;
        }

        // If not, create it now. By this point, the app has fully booted and 'config' will be available.
        $config = $this->ci->get('config'); // Get config from the container
        $apiKey = $config->getString('outseta.api_key');
        $secretKey = $config->getString('outseta.secret_key');
        $outsetaDomain = $config->getString('outseta.domain');
        $baseUrl = "https://{$outsetaDomain}.outseta.com/api/v1/";

        // Create and store the client for future use
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

        return $this->client;
    }

    /**
     * All public methods must now use getClient() to ensure the client is initialized.
     */
    public function getPersonByEmail(string $email): ?array
    {
        try {
            // Use the getter method here
            $response = $this->getClient()->get('crm/people', [
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

    public function getPersonByToken(string $accessToken): ?array
    {
        try {
            // Use the getter method here
            $response = $this->getClient()->get('profile', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                ]
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
}