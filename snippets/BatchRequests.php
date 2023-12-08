<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

use Microsoft\Graph\BatchRequestBuilder;
use Microsoft\Graph\Core\Requests\BatchRequestContent;
use Microsoft\Graph\Core\Requests\BatchRequestItem;
use Microsoft\Graph\Core\Requests\BatchResponseContent;
use Microsoft\Graph\Generated\Models;
use Microsoft\Graph\Generated\Users\Item\CalendarView\CalendarViewRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\Item\CalendarView\CalendarViewRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Kiota\Abstractions\RequestInformation;

class BatchRequests {
    public static function runAllSamples(GraphServiceClient $graphClient): void {
        BatchRequests::simpleBatch($graphClient);
        BatchRequests:: dependentBatch($graphClient);
    }

    private static function simpleBatch(GraphServiceClient $graphClient): void {
        // <SimpleBatchSnippet>
        // Use the request builder to generate a GET
        // request to /me
        $userRequest = $graphClient->me()->toGetRequestInformation();

        $timeZone = new \DateTimeZone('America/New_York');
        $today = new \DateTimeImmutable('today midnight', $timeZone);
        $tomorrow = new \DateTimeImmutable('tomorrow midnight', $timeZone);

        // Use the request builder to generate a GET
        // request to /me/calendarView?startDateTime="start"&endDateTime="end"
        $query = new CalendarViewRequestBuilderGetQueryParameters(
            startDateTime: $today->format(\DateTime::ATOM),
            endDateTime: $tomorrow->format(\DateTime::ATOM));
        $config = new CalendarViewRequestBuilderGetRequestConfiguration(queryParameters: $query);
        $eventsRequest = $graphClient->me()->calendarView()->toGetRequestInformation($config);

        // Build the batch
        $userRequestItem = new BatchRequestItem($userRequest);
        $eventsRequestItem = new BatchRequestItem($eventsRequest);
        $batchRequestContent = new BatchRequestContent([$userRequestItem, $eventsRequestItem]);

        // Create a batch request builder to send the batched requests
        $batchRequestBuilder = new BatchRequestBuilder($graphClient->getRequestAdapter());
        /** @var BatchResponseContent $batchResponse */
        $batchResponse = $batchRequestBuilder->postAsync($batchRequestContent)->wait();

        // De-serialize the responses
        $user = $batchResponse->getResponseBody($userRequestItem->getId(), Models\User::class);
        print('Hello '.$user->getDisplayName().'!'.PHP_EOL);

        // For collections, must use the *CollectionResponse class to deserialize
        // getValue will return an array of items
        $events = $batchResponse->getResponseBody($eventsRequestItem->getId(), Models\EventCollectionResponse::class);
        print('You have '.count($events->getValue()).' events on your calendar today'.PHP_EOL);
        // </SimpleBatchSnippet>
    }

    private static function dependentBatch(GraphServiceClient $graphClient): void {
        // <DependentBatchSnippet>
        $startTime = new \DateTimeImmutable('today 5PM');
        $endTime = $startTime->add(new \DateInterval('PT30M'));

        $newEvent = new Models\Event();
        $newEvent->setSubject('File end-of-day report');
        $start = new Models\DateTimeTimeZone();
        $start->setDateTime($startTime->format('Y-m-d\TH:i:s'));
        $start->setTimeZone('Eastern Standard Time');
        $newEvent->setStart($start);
        $end = new Models\DateTimeTimeZone();
        $end->setDateTime($endTime->format('Y-m-d\TH:i:s'));
        $end->setTimeZone('Eastern Standard Time');
        $newEvent->setEnd($end);

        // Use the request builder to generate a
        // POST request to /me/events
        $addEventRequest = $graphClient->me()->events()->toPostRequestInformation($newEvent);

        $timeZone = new \DateTimeZone('America/New_York');
        $today = new \DateTimeImmutable('today midnight', $timeZone);
        $tomorrow = new \DateTimeImmutable('tomorrow midnight', $timeZone);

        // Use the request builder to generate a GET
        // request to /me/calendarView?startDateTime="start"&endDateTime="end"
        $query = new CalendarViewRequestBuilderGetQueryParameters(
            startDateTime: $today->format(\DateTime::ATOM),
            endDateTime: $tomorrow->format(\DateTime::ATOM));
        $config = new CalendarViewRequestBuilderGetRequestConfiguration(queryParameters: $query);
        $eventsRequest = $graphClient->me()->calendarView()->toGetRequestInformation($config);

        // Build the batch
        // Force the requests to execute in order, so that the request for
        // today's events will include the new event created.

        // First request, no dependency
        $addEventRequestItem = new BatchRequestItem($addEventRequest);

        // Second request, depends on addEventRequestItem
        $eventsRequestItem = new BatchRequestItem($eventsRequest, dependsOn: [$addEventRequestItem]);
        $batchRequestContent = new BatchRequestContent([$addEventRequestItem, $eventsRequestItem]);

        // Create a batch request builder to send the batched requests
        $batchRequestBuilder = new BatchRequestBuilder($graphClient->getRequestAdapter());
        /** @var BatchResponseContent $batchResponse */
        $batchResponse = $batchRequestBuilder->postAsync($batchRequestContent)->wait();

        // De-serialize the responses
        $createdEvent = $batchResponse->getResponseBody($addEventRequestItem->getId(), Models\Event::class);
        print('New event created with ID: '.$createdEvent->getId().PHP_EOL);

        // For collections, must use the *CollectionResponse class to deserialize
        // getValue will return an array of items
        $events = $batchResponse->getResponseBody($eventsRequestItem->getId(), Models\EventCollectionResponse::class);
        print('You have '.count($events->getValue()).' events on your calendar today'.PHP_EOL);
        // </DependentBatchSnippet>
    }
}
?>
