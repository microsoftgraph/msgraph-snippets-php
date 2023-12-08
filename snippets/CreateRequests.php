<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

use Microsoft\Graph\Generated\Groups\GroupsRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Groups\GroupsRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\Generated\Models;
use Microsoft\Graph\Generated\Users\Item\CalendarView\CalendarViewRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\Item\CalendarView\CalendarViewRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\Generated\Users\Item\Events\EventsRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\Generated\Users\Item\Messages\Item\MessageItemRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\Item\Messages\Item\MessageItemRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\Generated\Users\Item\Messages\MessagesRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\Item\Messages\MessagesRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\Generated\Users\Item\UserItemRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\Item\UserItemRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\GraphServiceClient;

class CreateRequests {
    public static function runAllSamples(GraphServiceClient $graphClient): void {
        // Create a new message
        $newMessage = new Models\Message();
        $newMessage->setSubject("Temporary");

        /** @var Models\Message $tempMessage */
        $tempMessage = $graphClient->me()->messages()->post($newMessage)->wait();

        // Get a team to update
        $query = new GroupsRequestBuilderGetQueryParameters(filter: 'resourceProvisioningOptions/Any(x:x eq \'Team\')');
        $config = new GroupsRequestBuilderGetRequestConfiguration(queryParameters: $query);
        /** @var Models\GroupCollectionResponse $teams */
        $teams = $graphClient->groups()->get($config)->wait();
        $teamId = $teams->getValue()[0]->getId();

        CreateRequests::makeReadRequest($graphClient);
        CreateRequests::makeSelectRequest($graphClient);
        CreateRequests::makeListRequest($graphClient);
        CreateRequests::makeItemByIdRequest($graphClient, $tempMessage->getId());
        CreateRequests::makeExpandRequest($graphClient, $tempMessage->getId());
        CreateRequests::makeDeleteRequest($graphClient, $tempMessage->getId());
        CreateRequests::makeCreateRequest($graphClient);
        CreateRequests::makeUpdateRequest($graphClient, $teamId);
        CreateRequests::makeHeadersRequest($graphClient);
        CreateRequests::makeQueryParametersRequest($graphClient);
    }

    private static function makeReadRequest(GraphServiceClient $graphClient): Models\User {
        // <ReadRequestSnippet>
        // GET https://graph.microsoft.com/v1.0/me
        /** @var Models\User $user */
        $user = $graphClient->me()
            ->get()
            ->wait();
        // </ReadRequestSnippet>

        return $user;
    }

    private static function makeSelectRequest(GraphServiceClient $graphClient): Models\User {
        // <SelectRequestSnippet>
        // GET https://graph.microsoft.com/v1.0/me?$select=displayName,jobTitle
        // Microsoft\Graph\Generated\Users\Item\UserItemRequestBuilderGetQueryParameters
        $query = new UserItemRequestBuilderGetQueryParameters(
            select: ['displayName', 'jobTitle']);

        // Microsoft\Graph\Generated\Users\Item\UserItemRequestBuilderGetRequestConfiguration
        $config = new UserItemRequestBuilderGetRequestConfiguration(
            queryParameters: $query);

        /** @var Models\User $user */
        $user = $graphClient->me()
            ->get($config)
            ->wait();
        // </SelectRequestSnippet>

        return $user;
    }

    private static function makeListRequest(GraphServiceClient $graphClient): Models\MessageCollectionResponse {
        // <ListRequestSnippet>
        // GET https://graph.microsoft.com/v1.0/me/messages?
        // $select=subject,sender&$filter=subject eq 'Hello world'
        // Microsoft\Graph\Generated\Users\Item\Messages\MessagesRequestBuilderGetQueryParameters
        $query = new MessagesRequestBuilderGetQueryParameters(
            select: ['subject', 'sender'],
            filter: 'subject eq \'Hello world\''
        );

        // Microsoft\Graph\Generated\Users\Item\Messages\MessagesRequestBuilderGetRequestConfiguration
        $config = new MessagesRequestBuilderGetRequestConfiguration(
            queryParameters: $query);

        /** @var Models\MessageCollectionResponse $messages */
        $messages = $graphClient->me()
            ->messages()
            ->get($config)
            ->wait();
        // </ListRequestSnippet>

        return $messages;
    }

    private static function makeItemByIdRequest(GraphServiceClient $graphClient, string $messageId): Models\Message {
        // <ItemByIdRequestSnippet>
        // GET https://graph.microsoft.com/v1.0/me/messages/{message-id}
        // messageId is a string containing the id property of the message
        /** @var Models\Message $message */
        $message = $graphClient->me()
            ->messages()
            ->byMessageId($messageId)
            ->get()
            ->wait();
        // </ItemByIdRequestSnippet>

        return $message;
    }

    private static function makeExpandRequest(GraphServiceClient $graphClient, string $messageId): Models\Message {
        // <ExpandRequestSnippet>
        // GET https://graph.microsoft.com/v1.0/me/messages/{message-id}?$expand=attachments
        // messageId is a string containing the id property of the message
        // Microsoft\Graph\Generated\Users\Item\Messages\Item\MessageItemRequestBuilderGetQueryParameters
        $query = new MessageItemRequestBuilderGetQueryParameters(
            expand: ['attachments']
        );

        // Microsoft\Graph\Generated\Users\Item\Messages\Item\MessageItemRequestBuilderGetRequestConfiguration
        $config = new MessageItemRequestBuilderGetRequestConfiguration(
            queryParameters: $query);

        /** @var Models\Message $message */
        $message = $graphClient->me()
            ->messages()
            ->byMessageId($messageId)
            ->get($config)
            ->wait();
        // </ExpandRequestSnippet>

        return $message;
    }

    private static function makeDeleteRequest(GraphServiceClient $graphClient, string $messageId): void {
        // <DeleteRequestSnippet>
        // DELETE https://graph.microsoft.com/v1.0/me/messages/{message-id}
        // messageId is a string containing the id property of the message
        $graphClient->me()
            ->messages()
            ->byMessageId($messageId)
            ->delete()
            ->wait();
        // </DeleteRequestSnippet>
    }

    private static function makeCreateRequest(GraphServiceClient $graphClient): Models\Calendar {
        // <CreateRequestSnippet>
        // POST https://graph.microsoft.com/v1.0/me/calendars
        $calendar = new Models\Calendar();
        $calendar->setName('Volunteer');

        /** @var Models\Calendar $newCalendar */
        $newCalendar = $graphClient->me()
            ->calendars()
            ->post($calendar)
            ->wait();
        // </CreateRequestSnippet>

        return $newCalendar;
    }

    private static function makeUpdateRequest(GraphServiceClient $graphClient, string $teamId): void {
        // <UpdateRequestSnippet>
        // PATCH https://graph.microsoft.com/v1.0/teams/{team-id}
        $funSettings = new Models\TeamFunSettings();
        $funSettings->setAllowGiphy(true);
        $funSettings->setGiphyContentRating(
            new Models\GiphyRatingType(Models\GiphyRatingType::STRICT));

        $team = new Models\Team();
        $team->setFunSettings($funSettings);

        // $teamId is a string containing the id property of the team
        $graphClient->teams()
            ->byTeamId($teamId)
            ->patch($team);
        // </UpdateRequestSnippet>
    }

    private static function makeHeadersRequest(GraphServiceClient $graphClient): Models\EventCollectionResponse {
        // <HeadersRequestSnippet>
        // GET https://graph.microsoft.com/v1.0/me/events
        // Microsoft\Graph\Generated\Users\Item\Events\EventsRequestBuilderGetRequestConfiguration
        $config = new EventsRequestBuilderGetRequestConfiguration(
            headers: ['Prefer' => 'outlook.timezone="Pacific Standard Time"']
        );

        /** @var Models\EventCollectionResponse $events */
        $events = $graphClient->me()
            ->events()
            ->get($config)
            ->wait();
        // </HeadersRequestSnippet>

        return $events;
    }

    private static function makeQueryParametersRequest(GraphServiceClient $graphClient): Models\EventCollectionResponse {
        // <QueryParametersRequestSnippet>
        // GET https://graph.microsoft.com/v1.0/me/calendarView?
        // startDateTime=2023-06-14T00:00:00Z&endDateTime=2023-06-15T00:00:00Z
        // Microsoft\Graph\Generated\Users\Item\CalendarView\CalendarViewRequestBuilderGetQueryParameters
        $query = new CalendarViewRequestBuilderGetQueryParameters(
            startDateTime: '2023-06-14T00:00:00Z',
            endDateTime: '2023-06-15T00:00:00Z');

        // Microsoft\Graph\Generated\Users\Item\CalendarView\CalendarViewRequestBuilderGetRequestConfiguration
        $config = new CalendarViewRequestBuilderGetRequestConfiguration(
            queryParameters: $query);

        /** @var Models\EventCollectionResponse $events */
        $events = $graphClient->me()
            ->calendarView()
            ->get($config)
            ->wait();
        // </QueryParametersRequestSnippet>

        return $events;
    }
}

?>
