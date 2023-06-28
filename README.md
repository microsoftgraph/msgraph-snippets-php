# Microsoft Graph PHP SDK Snippets

[![PHP Composer](https://github.com/microsoftgraph/msgraph-snippets-php/actions/workflows/php.yml/badge.svg)](https://github.com/microsoftgraph/msgraph-snippets-php/actions/workflows/php.yml) ![License.](https://img.shields.io/badge/license-MIT-green.svg)

This repository contains sample snippets for the [Microsoft Graph PHP SDK](https://github.com/microsoftgraph/msgraph-sdk-php). These snippets are referenced in the [Microsoft Graph SDK documentation](https://learn.microsoft.com/graph/sdks/sdks-overview).

## Prerequisites

- [PHP 8](https://www.php.net/)

## Register an app in Azure Active Directory

1. Open a browser and navigate to the [Microsoft Entra admin center](https://entra.microsoft.com) and login using a **Work or School Account**.

1. Expand **Azure Active Directory** in the left-hand navigation, then expand **Applications**, then select **App registrations**.

1. Select **New registration**. Enter a name for your application, for example, `Graph Snippets`.

1. Set **Supported account types** as desired. The options are:

    | Option | Who can sign in? |
    |--------|------------------|
    | **Accounts in this organizational directory only** | Only users in your Microsoft 365 organization |
    | **Accounts in any organizational directory** | Users in any Microsoft 365 organization (work or school accounts) |
    | **Accounts in any organizational directory ... and personal Microsoft accounts** | Users in any Microsoft 365 organization (work or school accounts) and personal Microsoft accounts |

1. Leave **Redirect URI** empty.

1. Select **Register**. On the application's **Overview** page, copy the value of the **Application (client) ID** and save it, you will need it in the next step. If you chose **Accounts in this organizational directory only** for **Supported account types**, also copy the **Directory (tenant) ID** and save it.

1. Select **Authentication** under **Manage**. Locate the **Advanced settings** section and change the **Allow public client flows** toggle to **Yes**, then choose **Save**.

## Configuring the sample

You can set these values directly in [.env](.env), or you can create a copy of **.env** named **.env.local** and set the values there.

1. Set `CLIENT_ID` to the **Application (client) ID** from your app registration.
1. If you chose **Accounts in this organizational directory only** for **Supported account types**, set `TENANT_ID` to your **Directory (tenant) ID**.

## Code of conduct

This project has adopted the [Microsoft Open Source Code of Conduct](https://opensource.microsoft.com/codeofconduct/). For more information see the [Code of Conduct FAQ](https://opensource.microsoft.com/codeofconduct/faq/) or contact [opencode@microsoft.com](mailto:opencode@microsoft.com) with any additional questions or comments.
