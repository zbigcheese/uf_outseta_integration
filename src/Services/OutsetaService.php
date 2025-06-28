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

            // --- START SUCCESS DEBUGGING ---
            // If the request was successful, let's see what Outseta sent back.
            echo "API Request Succeeded (Status 200).<br>";
            $body = json_decode((string) $response->getBody(), true);
            dd($body); // This will stop execution and dump the response from Outseta.
            // --- END SUCCESS DEBUGGING ---

            return $this->handleResponse($response);

        } catch (RequestException $e) {

            // --- START ERROR DEBUGGING ---
            // If the request failed, let's see the detailed error.
            echo "Outseta API Request Failed.<br>";
            if ($e->hasResponse()) {
                $errorResponse = $e->getResponse();
                echo "Status Code: " . $errorResponse->getStatusCode() . "<br>";
                echo "Response Body: <pre>" . $errorResponse->getBody()->getContents() . "</pre>";
            }
            // Also dump the main error message
            dd($e->getMessage());
            // --- END ERROR DEBUGGING ---

            // This part will not be reached because of dd()
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