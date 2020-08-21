<?php

namespace App\Controller\User;

use App\Controller\ApiController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @route("/user/profile")
 */
class ProfileController extends ApiController
{
    /**
     * Get user data.
     *
     * @Route("/", methods={"get"})
     */
    public function index()
    {
        $user = $this->getUser();

        return new JsonResponse($this->normalize($user, ['user_detail']));
    }

    /**
     * Update user data.
     *
     * @Route("/", methods={"put", "patch"})
     */
    public function update(Request $request, UserRepository $userRepository)
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
     *
     * @Route("/", methods={"delete"})
     */
    public function delete(Request $request, UserRepository $userRepository)
    {
        $user = $this->getUser();
        $userRepository->remove($user);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
