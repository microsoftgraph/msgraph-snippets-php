<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

use Microsoft\Graph\GraphRequestAdapter;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Abstractions\Authentication\BaseBearerTokenAuthenticationProvider;
use Microsoft\Kiota\Authentication\Oauth\AuthorizationCodeContext;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Microsoft\Kiota\Authentication\Oauth\ClientCredentialCertificateContext;
use Microsoft\Kiota\Authentication\Oauth\OnBehalfOfContext;

class CreateClients {
    public static function createWithAuthorizationCode(): GraphServiceClient {
        // <AuthorizationCodeSnippet>
        $scopes = ['User.Read'];

        // Multi-tenant apps can use "common",
        // single-tenant apps must use the tenant ID from the Azure portal
        $tenantId = 'common';

        // Values from app registration
        $clientId = 'YOUR_CLIENT_ID';
        $clientSecret = 'YOUR_CLIENT_SECRET';
        $redirectUri = 'YOUR_REDIRECT_URI';

        // For authorization code flow, the user signs into the Microsoft
        // identity platform, and the browser is redirected back to your app
        // with an authorization code in the query parameters
        $authorizationCode = 'AUTH_CODE_FROM_REDIRECT';

        // Microsoft\Kiota\Authentication\Oauth\AuthorizationCodeContext
        $tokenContext = new AuthorizationCodeContext(
            $tenantId,
            $clientId,
            $clientSecret,
            $authorizationCode,
            $redirectUri);

        $graphClient = new GraphServiceClient($tokenContext, $scopes);
        // </AuthorizationCodeSnippet>

        return $graphClient;
    }

    public static function createWithClientSecret(): GraphServiceClient {
        // <ClientSecretSnippet>
        // The client credentials flow requires that you request the
        // /.default scope, and pre-configure your permissions on the
        // app registration in Azure. An administrator must grant consent
        // to those permissions beforehand.
        $scopes = ['https://graph.microsoft.com/.default'];

        // Values from app registration
        $clientId = 'YOUR_CLIENT_ID';
        $tenantId = 'YOUR_TENANT_ID';
        $clientSecret = 'YOUR_CLIENT_SECRET';

        // Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext
        $tokenContext = new ClientCredentialContext(
            $tenantId,
            $clientId,
            $clientSecret);

        $graphClient = new GraphServiceClient($tokenContext, $scopes);
        // </ClientSecretSnippet>

        return $graphClient;
    }

    public static function createWithClientCertificate(): GraphServiceClient {
        // <ClientCertificateSnippet>
        // The client credentials flow requires that you request the
        // /.default scope, and pre-configure your permissions on the
        // app registration in Azure. An administrator must grant consent
        // to those permissions beforehand.
        $scopes = ['https://graph.microsoft.com/.default'];

        // Values from app registration
        $clientId = 'YOUR_CLIENT_ID';
        $tenantId = 'YOUR_TENANT_ID';

        // Certificate details
        $certificatePath = 'PATH_TO_CERTIFICATE';
        $privateKeyPath = 'PATH_TO_PRIVATE_KEY';
        $privateKeyPassphrase = 'PASSPHRASE';

        // Microsoft\Kiota\Authentication\Oauth\ClientCredentialCertificateContext
        $tokenContext = new ClientCredentialCertificateContext(
            $tenantId,
            $clientId,
            $certificatePath,
            $privateKeyPath,
            $privateKeyPassphrase);

        $graphClient = new GraphServiceClient($tokenContext, $scopes);
        // </ClientCertificateSnippet>

        return $graphClient;
    }

    public static function createWithOnBehalfOf(): GraphServiceClient {
        // <OnBehalfOfSnippet>
        $scopes = ['https://graph.microsoft.com/.default'];

        // Multi-tenant apps can use "common",
        // single-tenant apps must use the tenant ID from the Azure portal
        $tenantId = 'common';

        // Values from app registration
        $clientId = 'YOUR_CLIENT_ID';
        $clientSecret = 'YOUR_CLIENT_SECRET';

        // This is the incoming token to exchange using on-behalf-of flow
        $oboToken = 'JWT_TOKEN_TO_EXCHANGE';

        // Microsoft\Kiota\Authentication\Oauth\OnBehalfOfContext
        $tokenContext = new OnBehalfOfContext(
            $tenantId,
            $clientId,
            $clientSecret,
            $oboToken);

        $graphClient = new GraphServiceClient($tokenContext, $scopes);
        // </OnBehalfOfSnippet>

        return $graphClient;
    }

    public static function createWithDeviceCode(): GraphServiceClient {
        // <DeviceCodeSnippet>
        $scopes = 'User.Read';

        // Multi-tenant apps can use "common",
        // single-tenant apps must use the tenant ID from the Azure portal
        $tenantId = 'common';

        // Values from app registration
        $clientId = 'YOUR_CLIENT_ID';

        // Custom token provider
        $tokenProvider = new DeviceCodeTokenProvider(
            $clientId,
            $tenantId,
            $scopes);

        // Microsoft\Kiota\Abstractions\Authentication\BaseBearerTokenAuthenticationProvider
        $authProvider = new BaseBearerTokenAuthenticationProvider($tokenProvider);
        $adapter = new GraphRequestAdapter($authProvider);

        $graphClient = GraphServiceClient::createWithRequestAdapter($adapter);
        // </DeviceCodeSnippet>

        return $graphClient;
    }
}

?>
