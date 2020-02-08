<?php

namespace Uzink\FrontBundle\EventListener;

use Oneup\UploaderBundle\Event\PostPersistEvent;

class UploadListener
{
    public function onUpload(PostPersistEvent $event)
    {
        $response = $event->getResponse();
        $file = $event->getFile();

        $response['size'] = $file->getSize();
        $response['mime'] = $file->getMimeType();
        $response['basename'] = $file->getBasename();
        $response['extension'] = $file->getExtension();

        $webStr = 'web/';
        $webPos = strpos($file->getPath(), $webStr);
        $relPath = substr($file->getPath(), $webPos + strlen($webStr));

        $response['path'] = $relPath;
    }
}