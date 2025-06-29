<?php

declare(strict_types=1);

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Http\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use UserFrosting\Sprinkle\Account\Authenticate\Authenticator;
use UserFrosting\Sprinkle\Core\Facades\Config;

final class OutsetaAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Authenticator $authenticator,
        private ResponseFactoryInterface $responseFactory,
    ) {
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to check if the user is authenticated.
     * If not, it will redirect to the Outseta login page.
     *
     * @param ServerRequestInterface  $request The request
     * @param RequestHandlerInterface $handler The handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // If the user is already logged into a UserFrosting session, let them proceed.
        if ($this->authenticator->check()) {
            return $handler->handle($request);
        }

        // If they are a guest, build the redirect URL to the Outseta hosted login page.
        $outsetaDomain = Config::getString('outseta.domain');
        $loginUrl = "https://{$outsetaDomain}.outseta.com/login";

        // Create a 302 redirect response
        $response = $this->responseFactory->createResponse(302);

        return $response->withHeader('Location', $loginUrl);
    }
}