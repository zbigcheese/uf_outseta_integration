<?php

declare(strict_types=1);

namespace UserFrosting\Sprinkle\UfOutsetaIntegration\Http\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UserFrosting\Sprinkle\Account\Authenticate\Authenticator;
use UserFrosting\Sprinkle\Account\Authorize\AuthorizationManager;
use UserFrosting\Sprinkle\Account\Exceptions\AuthGuardException;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Services\OutsetaService;


final class SubscriptionAuthMiddleware implements MiddlewareInterface
{
    // Define active subscription stages from Outseta
    private const ACTIVE_STAGES = [
        2, // Trialing (on a free trial or subscribed to a free plan)
        3, // Subscribing (on a paid subscription and contributes to your monthly recurring revenue)
        4, // Canceling (The customer has indicated they want to cancel their base subscription.)
        //5, // Expired (The base subscription has ended after cancellation)
        //6, // Trial Expired
        7, // Past due (The base subscription is current, but payment has failed. The user likely needs to update the credit card information that they have on file.)
        8, // Cancelling Trial (When a user requests to cancel during a free trial period. The user will continue to have access for the rest of the trial period, but will not be subscribed at the end of the trial period.)
    ];

    public function __construct(
        private Authenticator $authenticator,
        private AuthorizationManager $authorizator,
        private OutsetaService $outseta,
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // 1. If the user is a guest, throw the framework's standard exception.
        // The framework's error handler will catch this and perform the
        // redirect to the configured login page automatically.
        if (!$this->authenticator->check()) {
            throw new AuthGuardException();
        }

        // 2. Get the currently logged-in UserFrosting user.
        $user = $this->authenticator->user();

        // 3. Check for the admin bypass
        if ($this->authorizator->checkAccess($user, 'uri_account_settings')) {
            return $handler->handle($request);
        }

        // 4. If not an admin, proceed with the Outseta subscription check...
        $outsetaAccountUid = $user->outsetaSubscriber->outseta_uid ?? null;

        if ($outsetaAccountUid === null) {
            return $this->denyAccess('/upgrade');
        }

        $account = $this->outseta->getAccount($outsetaAccountUid);

        if ($account && in_array($account['AccountStage'], self::ACTIVE_STAGES)) {
            return $handler->handle($request);
        }

        return $this->denyAccess('/upgrade');
    }

    /**
     * Helper function to create a redirect response.
     */
    private function denyAccess(string $redirectUrl): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(302);

        return $response->withHeader('Location', $redirectUrl);
    }
}