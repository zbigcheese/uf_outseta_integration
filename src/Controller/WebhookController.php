<?php

declare(strict_types=1);

namespace Zbigcheese\Sprinkles\UfOutsetaIntegration\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\Response as SlimResponse;
use UserFrosting\Sprinkle\Account\Database\Models\User;
use UserFrosting\Sprinkle\Core\Facades\Config;
use Zbigcheese\Sprinkles\UfOutsetaIntegration\Database\Models\OutsetaSubscriber;

class WebhookController
{
    public function process(Request $request): Response
    {
        // 1. Get the secret key from your application's config
        $secretKey = Config::getString('outseta.webhook_key');

        // 2. Get the signature from the request header
        $outsetaSignature = $request->getHeaderLine('Outseta-Signature');

        // 3. Get the raw content of the request body
        $payload = (string)$request->getBody();

        // 4. Calculate what the signature should be
        $expectedSignature = hash_hmac('sha256', $payload, $secretKey);

        // 5. Securely compare the expected signature with the one from the header
        if (!hash_equals($expectedSignature, $outsetaSignature)) {
            // If they don't match, this is a fraudulent request. Reject it.
            return new SlimResponse(401); // 401 Unauthorized
        }

        // --- Signature is valid, proceed with processing the webhook ---
        
        $data = json_decode($payload, true);

        // Check if this is a subscription creation event and it has the data we need
        if (isset($data['Metadata']['uf_user_id']) && isset($data['Data']['Person']['Uid'])) {
            $userFrostingId = $data['Metadata']['uf_user_id'];
            $outsetaPersonUid = $data['Data']['Person']['Uid'];

            // Find the local user
            $user = User::find($userFrostingId);

            if ($user) {
                OutsetaSubscriber::updateOrCreate(
                    ['user_id' => $user->id],
                    ['outseta_uid' => $outsetaPersonUid]
                );
            }
        }
        
        // Always return a 200 OK response to Outseta to acknowledge successful receipt.
        return new SlimResponse(200);
    }
}