<?php

namespace App\Controller\User;

use App\Controller\ApiController;
use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\PostRepository;
use App\Security\Voter\PostVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class PostController extends ApiController
{
    /**
     * Create post.
     */
    #[Route('/post/', methods: ['POST'])]
    public function create(Request $request, PostRepository $postRepository): JsonResponse
    {
        $post = new Post();
        $post->setUser($this->getUser());

        $request = $this->transformJsonContent($request);
        $this->processTags($request, $post);
        $this->fillObject($post, $request->request->all());
        $this->validateObject($post);
        $postRepository->create($post);

        return new JsonResponse(['id' => $post->getId()], Response::HTTP_CREATED);
    }

    /**
     * Get user post by id.
     */
    #[Route('/post/{id}/', methods: ['GET'])]
    public function id(Post $post): JsonResponse
    {
        $this->denyAccessUnlessGranted(PostVoter::VIEW, $post);

        return new JsonResponse($this->normalize($post, ['post', 'post_text', 'post_html']));
    }

    /**
     * Update user post by id.
     */
    #[Route('/post/{id}/', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, Post $post, PostRepository $postRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);

        $request = $this->transformJsonContent($request);
        $this->processTags($request, $post);
        $this->fillObject($post, $request->request->all());
        $this->validateObject($post);
        $postRepository->update($post);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete user post by id.
     */
    #[Route('/post/{id}/', methods: ['DELETE'])]
    public function delete(Post $post, PostRepository $postRepository): JsonResponse
    {
        $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        $postRepository->remove($post);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Get all user posts.
     */
    #[Route('/posts/', methods: ['GET'])]
    public function all(Request $request, PostRepository $postRepository): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $paginator = $postRepository->findLatest($this->getUser(), $page, $tag ?? null);

        return new JsonResponse($this->renderPaginator($paginator, ['post', 'post_text', 'post_html']));
    }

    /**
     * Delete all user posts.
     */
    #[Route('/posts/', methods: ['DELETE'])]
    public function deleteAll(PostRepository $postRepository): JsonResponse
    {
        $postRepository->removeAll($this->getUser());

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Convert markdown to html.
     */
    #[Route('/post/md2html/', methods: ['POST'])]
    public function md2html(Request $request): JsonResponse
    {
        $text = $this->transformJsonContent($request)->request->get('text', '');
        $html = (new \Parsedown())->text($text);

        return new JsonResponse(['html' => $html]);
    }

    private function processTags(Request $request, Post $post): void
    {
        $tagRepository = $this->getDoctrine()->getRepository(Tag::class);
        $tags = $request->request->get('tags');

        if (\is_array($tags)) {
            $post->getTags()->clear();

            foreach ($tags as $tag) {
                $tag = $tagRepository->upsert($tag['name']);
                $this->validateObject($tag);
                $post->addTag($tag);
            }
        }

        $request->request->remove('tags');
    }
}
