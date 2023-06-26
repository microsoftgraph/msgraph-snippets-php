<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

// Enable loading of Composer dependencies
require_once realpath(__DIR__ . '/vendor/autoload.php');
require_once 'GraphHelper.php';

// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__, ['.env', '.env.local'], false);
$dotenv->safeLoad();
$dotenv->required(['CLIENT_ID', 'TENANT_ID', 'GRAPH_USER_SCOPES'])->notEmpty();

print($_ENV['CLIENT_ID'].PHP_EOL);
print($_ENV['TENANT_ID'].PHP_EOL);
print($_ENV['GRAPH_USER_SCOPES'].PHP_EOL);

$userClient = GraphHelper::getGraphClientForUser();

$user = $userClient->me()->get()->wait();
print('Hello, '.$user->getDisplayName().PHP_EOL);

$choice = -1;

while ($choice != 0) {
    print('Please choose one of the following options:'.PHP_EOL);
    print('0. Exit'.PHP_EOL);

    $choice = (int)readline('');

    switch ($choice) {
        case 0:
        default:
            print('Goodbye...'.PHP_EOL);
    }
}
?>
