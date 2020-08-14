<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthController extends ApiController
{
    /**
     * Register a new user.
     *
     * Input:
     * {"username": "<username>", "password": "<password>"}
     * Output:
     * {"token": "<JWT>"}
     *
     * @Route("/register/", methods={"post"})
     */
    public function register(
        Request $request,
        UserRepository $userRepository,
        UserPasswordEncoderInterface $encoder,
        JWTTokenManagerInterface $JWTTokenManager
    ) {
        $request = $this->transformJsonContent($request);
        $username = $request->get('username');
        $password = $request->get('password');

        $user = new User();
        $user->setUsername($username ?? '');
        $user->setPassword($password ?? ''); // set raw password for validation only
        $this->validateObject($user);
        $user->setPassword($encoder->encodePassword($user, $password)); // set hashed password
        $userRepository->create($user);

        return new JsonResponse(['token' => $JWTTokenManager->create($user)], Response::HTTP_CREATED);
    }

    /**
     * Login by username and password.
     *
     * Input:
     * {"username": "<username>", "password": "<password>"}
     * Output:
     * {"token": "<JWT>"}
     *
     * @Route("/login/", methods={"post"})
     */
    public function login()
    {
    }
}
