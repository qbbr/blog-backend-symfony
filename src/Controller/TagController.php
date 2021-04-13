<?php

namespace App\Controller;

use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class TagController extends ApiController
{
    #[Route('/tags/', methods: ['GET'])]
    public function index(TagRepository $tagRepository): JsonResponse
    {
        $tags = $tagRepository->findAll();

        return new JsonResponse($this->normalize($tags, ['tag']));
    }
}
