<?php

declare(strict_types=1);

namespace UserFrosting\Sprinkle\UfOutsetaIntegration\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as SlimResponse;
use UserFrosting\Config\Config;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Services\UserProvisioner;

class WebhookController
{
    public function __construct(
        protected UserProvisioner $provisioner,
        protected Config $config
    ) {
    }

    public function processAccountCreated(Request $request): Response
    {
        // 1. Verify the webhook signature
        $hexSecretKey = $this->config->getString('outseta.webhook_key');

        // Decode the hex key into its raw binary representation before using it.
        $binarySecretKey = hex2bin($hexSecretKey);

        $outsetaSignatureHeader = $request->getHeaderLine('X-Hub-Signature-256');
        $signatureParts = explode('=', $outsetaSignatureHeader, 2);
        $outsetaSignature = $signatureParts[1] ?? '';
        $payload = (string)$request->getBody();

        // Use the decoded binary key in the calculation
        $expectedSignature = hash_hmac('sha256', $payload, $binarySecretKey);

        if (!hash_equals($expectedSignature, $outsetaSignature)) {
            return new SlimResponse(401);
        }

        // 2. Process the valid payload
        $data = json_decode($payload, true);

        if (isset($data['PrimaryContact']['Uid'])) {
            $personData = $data['PrimaryContact'];
            $this->provisioner->findOrCreate($personData, 'outseta-account-owners');
        }

        return new SlimResponse(200);
    }
}
