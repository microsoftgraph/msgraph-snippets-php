<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Microsoft\Graph\Core\GraphClientFactory;
use Microsoft\Graph\GraphRequestAdapter;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Abstractions\Authentication\BaseBearerTokenAuthenticationProvider;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once 'DeviceCodeTokenProvider.php';

class GraphHelper {
    public static function getGraphClientForUser(): GraphServiceClient {
        $clientId = $_ENV['CLIENT_ID'];
        $tenantId = $_ENV['TENANT_ID'];
        $scopes = $_ENV['GRAPH_USER_SCOPES'];

        $tokenProvider = new DeviceCodeTokenProvider($clientId, $tenantId, $scopes);
        $authProvider = new BaseBearerTokenAuthenticationProvider($tokenProvider);
        $adapter = new GraphRequestAdapter($authProvider);

        return GraphServiceClient::createWithRequestAdapter($adapter);
    }

    public static function getDebugGraphClientForUser(): GraphServiceClient {
        $clientId = $_ENV['CLIENT_ID'];
        $tenantId = $_ENV['TENANT_ID'];
        $scopes = $_ENV['GRAPH_USER_SCOPES'];

        $logger = new Logger('graph');
        $logger->pushHandler(new StreamHandler('php://stdout', \Monolog\Level::Debug));

        $stack = GraphClientFactory::getDefaultHandlerStack();
        $stack->push(Middleware::log($logger, new MessageFormatter('{method} {uri} {req_body}')));

        $httpClient = GraphClientFactory::createWithMiddleware($stack);

        $tokenProvider = new DeviceCodeTokenProvider($clientId, $tenantId, $scopes);
        $authProvider = new BaseBearerTokenAuthenticationProvider($tokenProvider);
        $adapter = new GraphRequestAdapter($authProvider, $httpClient);

        return GraphServiceClient::createWithRequestAdapter($adapter);
    }
}
?>
