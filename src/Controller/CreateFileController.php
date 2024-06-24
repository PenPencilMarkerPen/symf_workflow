<?php

namespace App\Controller;

use App\Entity\File;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;

#[AsController]
class CreateFileController extends AbstractController {


    public function __invoke(File $file, Request $request, UserRepository $userRepository): File
    {
        $authToken = $request->headers->get('auth-token');

        if (!$authToken)
        {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'No authentication token provided.');
        }

        $user = $userRepository->findOneBy([
            'token' => $authToken
        ]);

        $file->setUser($user);
        
        return $file;
    }
}