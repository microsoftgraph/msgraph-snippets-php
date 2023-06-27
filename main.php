<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

// Enable loading of Composer dependencies
require_once realpath(__DIR__ . '/vendor/autoload.php');
require_once 'GraphHelper.php';
require_once realpath(__DIR__ . '/snippets/BatchRequests.php');

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, ['.env', '.env.local'], false);
$dotenv->safeLoad();
$dotenv->required(['CLIENT_ID', 'TENANT_ID', 'GRAPH_USER_SCOPES'])->notEmpty();

$userClient = GraphHelper::getDebugGraphClientForUser();

$user = $userClient->me()->get()->wait();
print('Hello, '.$user->getDisplayName().PHP_EOL);

$choice = -1;

while ($choice != 0) {
    print('Please choose one of the following options:'.PHP_EOL);
    print('0. Exit'.PHP_EOL);
    print('1. Run batch samples'.PHP_EOL);

    $choice = (int)readline('');

    switch ($choice) {
        case 1:
            BatchRequests::runAllSamples($userClient);
            break;
        case 0:
            print('Goodbye...'.PHP_EOL);
            break;
        default:
            print('Invalid choice!'.PHP_EOL);
    }
}
?>
