<?php

namespace UserFrosting\Sprinkle\UfOutsetaIntegration\Services;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use UserFrosting\Config\Config;

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

        $this->client = new HttpClient([
            'base_uri' => $baseUrl,
            'headers' => [
                'Authorization' => "Outseta {$apiKey}:{$secretKey}",
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
            ],
            'timeout' => 5.0,
            //'verify'  => false, // SSL workaround for local
        ]);
    }

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

    /**
     * Adds a new person to an existing account in Outseta.
     *
     * @param array $personData ['Email' => '...', 'FirstName' => '...', 'LastName' => '...']
     * @param string $accountUid The UID of the owner's account.
     * @return array|null The new person's data from Outseta.
     */
    public function addPersonToAccount(array $personData, string $accountUid): ?array
    {
        try {
            $response = $this->client->post("crm/accounts/{$accountUid}/people", [
                'json' => $personData
            ]);

            return json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            error_log('Outseta addPersonToAccount failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Removes a person from an account in Outseta.
     *
     * @param string $personUid The UID of the person to remove.
     * @param string $accountUid The UID of the account they belong to.
     * @return bool True on success, false on failure.
     */
    public function removePersonFromAccount(string $personUid, string $accountUid): bool
    {
        try {
            $this->client->delete("crm/accounts/{$accountUid}/people/{$personUid}");

            return true;
        } catch (RequestException $e) {
            error_log('Outseta removePersonFromAccount failed: ' . $e->getMessage());
            return false;
        }
    }
}