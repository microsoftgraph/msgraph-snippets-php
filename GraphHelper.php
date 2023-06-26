<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

use Microsoft\Graph\GraphRequestAdapter;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Abstractions\Authentication\BaseBearerTokenAuthenticationProvider;

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
}
?>
