<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\UserRepository;
use App\Repository\FileRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;


class GetCollectionFileProvider implements ProviderInterface
{
    private $requestStack;
    private $userRepository;
    private $fileRepository;

    public function __construct(
        RequestStack $requestStack,
        UserRepository $userRepository,
        FileRepository $fileRepository
    ) {
        $this->requestStack = $requestStack;
        $this->userRepository = $userRepository;
        $this->fileRepository = $fileRepository;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $request = $this->requestStack->getCurrentRequest();

        $authorizationHeader = $request->headers->get('AUTH-TOKEN');

        if (!$authorizationHeader)
        {
            throw new HttpException(Response::HTTP_UNAUTHORIZED, 'No authentication token provided.');
        }

        $user = $this->userRepository->findOneByToken($authorizationHeader);

        if (!$user) {
            throw new HttpException(Response::HTTP_NOT_FOUND, 'User not found for the provided token.');
        }

        $files = $this->fileRepository->findBy(['user' => $user]);

        return $files;
    }
}
