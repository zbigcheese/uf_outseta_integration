<?php

namespace UserFrosting\Sprinkle\UfOutsetaIntegration\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Account\Authenticate\Authenticator;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Services\OutsetaService;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Services\UserProvisioner;

class AuthController
{
    public function callback(
        Request $request,
        Response $response,
        OutsetaService $outseta,
        Authenticator $authenticator,
        UserProvisioner $provisioner
    ): Response {
        // Get the access_token from the URL query parameters
        $queryParams = $request->getQueryParams();
        $accessToken = $queryParams['access_token'] ?? null;

        if (!$accessToken) {
            // Handle error - maybe redirect to home with an error message
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        // Use the token to get the user's profile from Outseta
        $outsetaPerson = $outseta->getPersonByToken($accessToken);

        if (!$outsetaPerson) {
            // Handle error - token was invalid or expired
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        // Find or create a local user account
        $localUser = $provisioner->findOrCreate($outsetaPerson);

        // Log the user into the UserFrosting system
        $authenticator->login($localUser);

        // Redirect to the dashboard or a protected page
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }
}