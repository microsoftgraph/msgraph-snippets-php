<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

use Microsoft\Graph\Core\Tasks\PageIterator;
use Microsoft\Graph\Generated\Models;
use Microsoft\Graph\Generated\Models\MessageCollectionResponse;
use Microsoft\Graph\Generated\Users\Item\Messages\MessagesRequestBuilderGetQueryParameters;
use Microsoft\Graph\Generated\Users\Item\Messages\MessagesRequestBuilderGetRequestConfiguration;
use Microsoft\Graph\GraphServiceClient;

class Paging {
    public static function runAllSamples(GraphServiceClient $graphClient): void {
        Paging::iterateAllMessages($graphClient);
        Paging::iterateAllMessagesWithPause($graphClient);
        Paging::manuallyPageAllMessages($graphClient);
    }

    private static function iterateAllMessages(GraphServiceClient $graphClient): void {
        // <PagingSnippet>
        $query = new MessagesRequestBuilderGetQueryParameters(
            top: 10,
            select: ['sender', 'subject', 'body']);

        $config = new MessagesRequestBuilderGetRequestConfiguration(
            queryParameters: $query,
            headers: ['Prefer' => 'outlook.body-content-type="text"']);

        $messages = $graphClient->me()
            ->messages()
            ->get($config)
            ->wait();

        // Microsoft\Graph\Core\Tasks\PageIterator
        $pageIterator = new PageIterator($messages, $graphClient->getRequestAdapter());

        $callback = function($message): bool {
            /** @var Models\Message $message */
            print($message->getSubject().PHP_EOL);
            // Return true to continue iteration
            return true;
        };

        // Re-add the header to subsequent requests
        $pageIterator->setHeaders(['Prefer' => 'outlook.body-content-type="text"']);

        $pageIterator->iterate($callback);
        // </PagingSnippet>
    }

    private static function iterateAllMessagesWithPause(GraphServiceClient $graphClient): void {
        // <ResumePagingSnippet>
        $count = 0;

        $messages = $graphClient->me()
            ->messages()
            ->get()
            ->wait();

        // Microsoft\Graph\Core\Tasks\PageIterator
        $pageIterator = new PageIterator($messages, $graphClient->getRequestAdapter());

        $callback = function($message) use (&$count): bool {
            /** @var Models\Message $message */
            $count++;
            print($count.'. '.$message->getSubject().PHP_EOL);
            // Return true to continue iteration
            // Return false once first 5 have been processed
            return $count < 5;
        };

        $pageIterator->iterate($callback);

        print('Pausing iteration after first 5'.PHP_EOL);
        sleep(5);

        // Process next 5
        $count = 0;
        $pageIterator->iterate($callback);
        // </ResumePagingSnippet>
    }

    private static function manuallyPageAllMessages(GraphServiceClient $graphClient): void {
        // <ManualPagingSnippet>
        /** @var MessageCollectionResponse $messages */
        $messages = $graphClient->me()
            ->messages()
            ->get()
            ->wait();

        while (null !== $messages->getValue())
        {
            foreach($messages->getValue() as $message) {
                /** @var Models\Message $message */
                print($message->getSubject().PHP_EOL);
            }

            if (null !== $messages->getOdataNextLink()) {
                $messages = $graphClient->me()
                    ->messages()
                    ->withUrl($messages->getOdataNextLink())
                    ->get()
                    ->wait();
            }
            else {
                break;
            }
        }
        // </ManualPagingSnippet>
    }
}
?>
