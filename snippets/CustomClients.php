<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

use Microsoft\Graph\Core\GraphClientFactory;
use Microsoft\Graph\GraphRequestAdapter;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Abstractions\Authentication\AuthenticationProvider;
use Microsoft\Kiota\Http\Middleware\KiotaMiddleware;

class CustomClients {
    public static function createWithChaosHandler(AuthenticationProvider $authProvider): GraphServiceClient {
        // <ChaosHandlerSnippet>
        // Create the default handler stack
        $handlerStack = GraphClientFactory::getDefaultHandlerStack();

        // Add the chaos handler
        $handlerStack->push(KiotaMiddleware::chaos());

        // Create an HTTP client with the handler stack
        $httpClient = GraphClientFactory::createWithMiddleware($handlerStack);

        // Create the Graph service client
        // $authProvider is an implementation of
        // Microsoft\Kiota\Abstractions\Authentication\AuthenticationProvider
        $adapter = new GraphRequestAdapter($authProvider, $httpClient);

        $graphClient = GraphServiceClient::createWithRequestAdapter($adapter);
        // </ChaosHandlerSnippet>

        return $graphClient;
    }

    public static function createWithProxy(AuthenticationProvider $authProvider): GraphServiceClient {
        // <ProxySnippet>
        // Create HTTP client with a Guzzle config
        // to specify proxy
        $guzzleConfig = [
            'proxy' => 'http://localhost:8888'
        ];

        $httpClient = GraphClientFactory::createWithConfig($guzzleConfig);

        // Create the Graph service client
        // $authProvider is an implementation of
        // Microsoft\Kiota\Abstractions\Authentication\AuthenticationProvider
        $adapter = new GraphRequestAdapter($authProvider, $httpClient);

        $graphClient = GraphServiceClient::createWithRequestAdapter($adapter);
        // </ProxySnippet>

        return $graphClient;
    }
}
?>
