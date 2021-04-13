<?php

namespace App\Controller\User;

use App\Controller\ApiController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user/profile')]
class ProfileController extends ApiController
{
    /**
     * Get user data.
     */
    #[Route('/', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse($this->normalize($user, ['user_detail']));
    }

    /**
     * Update user data.
     */
    #[Route('/', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUser();
        $request = $this->transformJsonContent($request);
        $this->fillObject($user, $request->request->all());
        $this->validateObject($user);
        $userRepository->update($user);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete user.
     */
    #[Route('/', methods: ['DELETE'])]
    public function delete(UserRepository $userRepository): JsonResponse
    {
        $user = $this->getUser();
        $userRepository->remove($user);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
