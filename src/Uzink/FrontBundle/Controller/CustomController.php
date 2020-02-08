<?php

namespace Uzink\FrontBundle\Controller;

use FM\ElFinderPHP\Connector\ElFinderConnector;
use Symfony\Component\HttpFoundation\File\Exception\UploadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Oneup\UploaderBundle\Controller\AbstractController;
use Oneup\UploaderBundle\Uploader\Response\EmptyResponse;

class CustomController extends AbstractController
{
    public function upload()
    {
        $request = $this->container->get('request');
        $response = new EmptyResponse();
        $files = $this->getFiles($request->files);

        foreach ($files as $file) {
            try {
                $this->handleUpload($file, $response, $request);
            } catch (UploadException $e) {
                $this->errorHandler->addException($response, $e);
            }
        }

        ElFinderConnector::class;

        $response = $response->assemble();

        return new JsonResponse($response);
    }
}