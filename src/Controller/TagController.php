<?php

namespace App\Controller;

use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tags")
 */
class TagController extends ApiController
{
    /**
     * @Route("/", methods={"get"})
     */
    public function index(TagRepository $tagRepository)
    {
        $tags = $tagRepository->findAll();

        return new JsonResponse($this->normalize($tags, ['tag']));
    }
}
