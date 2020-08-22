<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use App\Repository\TagRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends ApiController
{
    /**
     * Get all posts.
     *
     * @Route("/posts/", methods={"get"})
     */
    public function index(
        Request $request,
        PostRepository $postRepository,
        TagRepository $tagRepository
    ) {
        $page = $request->query->getInt('page', 1);
        $tagName = $request->query->get('tag');
        $query = $request->query->get('query');
        $sort = $request->query->get('sort');
        $order = $request->query->get('order');

        if (null !== $tagName) {
            $tag = $tagRepository->findOneBy(['name' => $tagName]);

            if (!$tag) {
                throw new NotFoundHttpException(sprintf('Tag with name "%s" not found!', $tagName));
            }
        }

        $paginator = $postRepository->findLatest(null, $page, $tag ?? null, $query, $sort, $order);

        return new JsonResponse($this->renderPaginator($paginator, ['post', 'post_html']));
    }

    /**
     * Get post by slug.
     *
     * @Route("/post/{slug}/", methods={"get"})
     */
    public function getBySlug(Post $post)
    {
        if ($post->getIsPrivate()) {
            throw new NotFoundHttpException('Post not found!');
        }

        return new JsonResponse($this->normalize($post, ['post', 'post_html']));
    }
}
