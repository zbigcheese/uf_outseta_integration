<?php

declare(strict_types=1);

namespace UserFrosting\Sprinkle\UfOutsetaIntegration\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use UserFrosting\Sprinkle\Account\Authenticate\Authenticator;
use UserFrosting\Sprinkle\Account\Database\Models\User;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Services\OutsetaService;
use UserFrosting\Sprinkle\UfOutsetaIntegration\Services\UserProvisioner;

class TeamController
{

    /**
     * Renders the team management page.
     */
    public function page(
        Authenticator $authenticator,
        OutsetaService $outseta,
        Twig $view
    ): Response {
        $owner = $authenticator->user();
        $ownerAccountUid = $owner->outsetaSubscriber->outseta_uid ?? null;

        $teammates = [];
        if ($ownerAccountUid) {
            $teammates = $outseta->getPeopleForAccount($ownerAccountUid);
        }

        return $view->render(new \Slim\Psr7\Response(), 'pages/team.html.twig', [
            'teammates' => $teammates,
        ]);
    }

    public function addTeammate(
        Request $request,
        Authenticator $authenticator,
        OutsetaService $outseta,
        UserProvisioner $provisioner
    ): Response {
        $owner = $authenticator->user();
        $data = (array)$request->getParsedBody();

        // Get the owner's Outseta Account UID
        $ownerAccountUid = $owner->outsetaSubscriber->outseta_uid ?? null;
        if (!$ownerAccountUid) {
            // Handle error: Owner is not linked to an Outseta account.
            // You would typically return a JSON error response here.
            return new \Slim\Psr7\Response(400);
        }

        // Call the Outseta API to add the new person
        $newPersonData = [
            'Email'     => $data['email'],
            'FirstName' => $data['first_name'],
            'LastName'  => $data['last_name'],
        ];
        $newOutsetaPerson = $outseta->addPersonToAccount($newPersonData, $ownerAccountUid);

        if (!$newOutsetaPerson) {
            // Handle API error
            return new \Slim\Psr7\Response(500);
        }

        // Provision the new user locally in the "Team Accounts" group
        $provisioner->findOrCreate($newOutsetaPerson, 'outseta-team-accounts');

        // Return a success response (e.g., redirect back to the team page)
        return new \Slim\Psr7\Response(200);
    }

    public function removeTeammate(
        string $teammateId, // The UserFrosting ID of the teammate
        Authenticator $authenticator,
        OutsetaService $outseta
    ): Response {
        $owner = $authenticator->user();
        $teammate = User::find($teammateId);

        if (!$teammate || !$teammate->outsetaSubscriber) {
            // Handle error: Teammate not found or not linked
            return new \Slim\Psr7\Response(404);
        }

        // TODO: Add logic to ensure the current user is the owner of this teammate's account.

        $ownerAccountUid = $owner->outsetaSubscriber->outseta_uid;
        $teammatePersonUid = $teammate->outsetaSubscriber->outseta_uid;

        // Call Outseta API to remove the person
        $success = $outseta->removePersonFromAccount($teammatePersonUid, $ownerAccountUid);

        if (!$success) {
            // Handle API error
            return new \Slim\Psr7\Response(500);
        }

        // Soft delete the user in UserFrosting
        $teammate->flag_disabled = true;
        $teammate->save();

        return new \Slim\Psr7\Response(200);
    }
}