<?php

namespace App\Controller;

use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/posts")
 */
class PostController extends ApiController
{
    /**
     * Get all posts.
     *
     * @Route("/", methods={"get"})
     */
    public function index(
        Request $request,
        PostRepository $postRepository,
        TagRepository $tagRepository
    ) {
        $page = $request->query->getInt('page', 1);
        $tagName = $request->query->get('tag');
        $query = $request->query->get('query');

        if (null !== $tagName) {
            $tag = $tagRepository->findOneBy(['name' => $tagName]);

            if (!$tag) {
                throw new NotFoundHttpException(sprintf('Tag with name "%s" not found!', $tagName));
            }
        }

        $paginator = $postRepository->findLatest($page, $tag ?? null, $query);

        return new JsonResponse($this->renderPaginator($paginator, ['post']));
    }
}
