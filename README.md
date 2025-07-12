This is a simple base integration of the Outseta service for UserFrosting 5.

---
Installation:
1) Add "userfrosting/uf-outseta-integration": "^1.0.0" to your composer.json under require.
2) Run composer update

---
Configuration:
1) In your .env file add the following parameters with the corresponding values you can acquire in your outseta account
OUTSETA_DOMAIN=
OUTSETA_API_KEY=
OUTSETA_SECRET_KEY=
OUTSETA_WEBHOOK_KEY=

2) Run the migration with "php bakery migrate" to create the required table used in the extension of the User model

3) Run the seed required to add in the default groups and roles "php bakery seed" and select the seed "OutsetaGroupSeed"

4) Set up the webhook in outseta (under settings->notifications->add notification):
   activity type: Account Created
   callback URL: https://YOURDOMAIN.COM/api/outseta/webhooks/accountCreated
   *This is an example webhook that comes with the sprinkle, you can easily add any others in the routes group and with new methods in the webhook controller. Keep in mind a path blacklisted from the csrf check is /api/outseta/webhooks/*

---
Usage:

Basic intended usage includes using the outseta register widget to get users to sign up and register (and pay for the subscription if set like so). Then the webhook receives the registration event and either creates a new user in userfrosting or connects to an existing one.

In your application routes you can do the following:
add to use entries
use UserFrosting\Sprinkle\UfOutsetaIntegration\Http\Middleware\SubscriptionAuthMiddleware;

Add middleware to check if the user accessing a route has an active subscription:
->add(SubscriptionAuthMiddleware::class)

example:
$app->group('/premium', function ($app) {
    $app->get('/dashboard', [AppController::class, 'pagePremiumDashboard']);
})->add(SubscriptionAuthMiddleware::class);

note:
This middleware considers the following outseta stage IDs as active: 2, 3, 4, 7, 8. If you wish to handle diferentlly fork the repository and change the ACTIVE_STAGES const in the SubscriptionAuthMiddleware.php
