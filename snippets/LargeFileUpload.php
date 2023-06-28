<?php
// Copyright (c) Microsoft Corporation. All rights reserved.
// Licensed under the MIT license.

use Microsoft\Graph\Core\Tasks\LargeFileUploadTask;
use Microsoft\Graph\Generated\Drives\Item\Items\Item\CreateUploadSession\CreateUploadSessionPostRequestBody as DriveItemCreateUploadSessionPostRequestBody;
use Microsoft\Graph\Generated\Models;
use Microsoft\Graph\Generated\Users\Item\Messages\Item\Attachments\CreateUploadSession\CreateUploadSessionPostRequestBody as AttachmentCreateUploadSessionPostRequestBody;
use Microsoft\Graph\GraphServiceClient;

class LargeFileUpload {
    public static function runAllSamples(GraphServiceClient $graphClient): void {
        $filePath = $_ENV['LARGE_FILE_PATH'];

        LargeFileUpload::uploadFileToOneDrive($graphClient, $filePath, 'Documents/vacation.gif');
        LargeFileUpload::uploadAttachmentToMessage($graphClient, $filePath);
    }

    private static function uploadFileToOneDrive(GraphServiceClient $graphClient, string $filePath, string $itemPath): void {
        // <LargeFileUploadSnippet>
        // Create a file stream
        $file = GuzzleHttp\Psr7\Utils::streamFor(fopen($filePath, 'r'));

        // Create the upload session request
        $uploadProperties = new Models\DriveItemUploadableProperties();
        $uploadProperties->setAdditionalData([
            '@microsoft.graph.conflictBehavior' => 'replace'
        ]);

        // use Microsoft\Graph\Generated\Drives\Item\Items\Item\CreateUploadSession\CreateUploadSessionPostRequestBody
        // as DriveItemCreateUploadSessionPostRequestBody;
        $uploadSessionRequest = new DriveItemCreateUploadSessionPostRequestBody();
        $uploadSessionRequest->setItem($uploadProperties);

        // Create the upload session
        /** @var Models\Drive $drive */
        $drive = $graphClient->me()->drive()->get()->wait();
        $uploadSession = $graphClient->drives()
            ->byDriveId($drive->getId())
            ->items()
            ->byDriveItemId('root:/'.$itemPath.':')
            ->createUploadSession()
            ->post($uploadSessionRequest)
            ->wait();

        $largeFileUpload = new LargeFileUploadTask($uploadSession, $graphClient->getRequestAdapter(), $file);
        $totalSize = $file->getSize();
        $progress = fn($prog) => print('Uploaded '.$prog[1].' of '.$totalSize.' bytes'.PHP_EOL);

        try {
            $largeFileUpload->upload($progress)->wait();
        } catch (\Psr\Http\Client\NetworkExceptionInterface $ex) {
            $largeFileUpload->resume()->wait();
        }
        // </LargeFileUploadSnippet>

        // Added to remove warning about unused function
        if (is_null($uploadSession)) {
            LargeFileUpload::resumeUpload($largeFileUpload);
        }
    }

    private static function resumeUpload(LargeFileUploadTask $largeFileUpload): void {
        // <ResumeSnippet>
        $largeFileUpload->resume();
        // </ResumeSnippet>
    }

    private static function uploadAttachmentToMessage(GraphServiceClient $graphClient, string $filePath): void {
        // <UploadAttachmentSnippet>
        // Create a message
        $draftMessage = new Models\Message();
        $draftMessage->setSubject('Large attachment');

        /** @var Models\Message $savedDraft */
        $savedDraft = $graphClient->me()
            ->messages()
            ->post($draftMessage)
            ->wait();

        // Create a file stream
        $file = GuzzleHttp\Psr7\Utils::streamFor(fopen($filePath, 'r'));

        // Create an attachment
        $attachment = new Models\AttachmentItem();
        $attachment->setAttachmentType(new Models\AttachmentType(Models\AttachmentType::FILE));
        $attachment->setName(basename($filePath));
        $attachment->setSize($file->getSize());

        // use Microsoft\Graph\Generated\Users\Item\Messages\Item\Attachments\CreateUploadSession\CreateUploadSessionPostRequestBody
        // as AttachmentCreateUploadSessionPostRequestBody;
        $uploadSessionRequest = new AttachmentCreateUploadSessionPostRequestBody();
        $uploadSessionRequest->setAttachmentItem($attachment);

        // Create the upload session
        $uploadSession = $graphClient->me()
            ->messages()
            ->byMessageId($savedDraft->getId())
            ->attachments()
            ->createUploadSession()
            ->post($uploadSessionRequest)
            ->wait();

        $largeFileUpload = new LargeFileUploadTask($uploadSession, $graphClient->getRequestAdapter(), $file);
        $totalSize = $file->getSize();
        $progress = fn($prog) => print('Uploaded '.$prog[1].' of '.$totalSize.' bytes'.PHP_EOL);

        try {
            $largeFileUpload->upload($progress)->wait();
        } catch (\Psr\Http\Client\NetworkExceptionInterface $ex) {
            $largeFileUpload->resume()->wait();
        }
        // </UploadAttachmentSnippet>
    }
}
?>
