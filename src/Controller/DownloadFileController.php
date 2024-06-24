<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse; 
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;


#[AsController]
#[Route("api/")]
class DownloadFileController extends AbstractController {

    #[Route(
        name: 'download_file',
        path: 'files/{name}/download',
        methods: ['GET'],
    )]
    public function downloadFile(string $name): Response
    {
        $filesDirectory = $this->getParameter('upload_destination');

        $filePath = $filesDirectory . '/' . $name;

        if (!file_exists($filePath)) {
            throw new HttpException(Response::HTTP_NO_CONTENT, 'No files.');
        }

        $response = new BinaryFileResponse($filePath);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $name);

        return $response;
    }
}