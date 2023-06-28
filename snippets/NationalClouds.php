<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

use Microsoft\Graph\Core\NationalCloud;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Authentication\Oauth\TokenRequestContext;

class NationalClouds {
    public static function createClientForUsGov(TokenRequestContext $tokenRequestContext): GraphServiceClient {
        // <NationalCloudSnippet>
        $scopes = ['https://graph.microsoft.us/.default'];

        // Create the Microsoft Graph client object using
        // the Microsoft Graph for US Government L4 endpoint
        // $tokenRequestContext is one of the token context classes
        // from Microsoft\Kiota\Authentication\Oauth
        $graphClient = new GraphServiceClient($tokenRequestContext, $scopes, NationalCloud::US_GOV);
        // </NationalCloudSnippet>

        return $graphClient;
    }
}
?>
