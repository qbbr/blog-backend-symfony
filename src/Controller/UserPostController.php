<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Tag;
use App\Repository\PostRepository;
use App\Security\Voter\PostVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/private/user")
 */
class UserPostController extends ApiController
{
    /**
     * Create post.
     *
     * @Route("/post/", methods={"post"})
     */
    public function create(Request $request, PostRepository $postRepository)
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
     *
     * @Route("/post/{id}/", methods={"get"})
     */
    public function id(Post $post)
    {
        $this->denyAccessUnlessGranted(PostVoter::VIEW, $post);

        return new JsonResponse($this->normalize($post, ['post']));
    }

    /**
     * Update user post by id.
     *
     * @Route("/post/{id}/", methods={"put", "patch"})
     */
    public function update(Request $request, Post $post, PostRepository $postRepository)
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
     *
     * @Route("/post/{id}/", methods={"delete"})
     */
    public function delete(Post $post, PostRepository $postRepository)
    {
        $this->denyAccessUnlessGranted(PostVoter::EDIT, $post);
        $postRepository->remove($post);

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * Get all user posts.
     *
     * @Route("/posts/", methods={"get"})
     */
    public function all(Request $request, PostRepository $postRepository)
    {
        $page = $request->query->getInt('page', 1);
        $paginator = $postRepository->findLatest($this->getUser(), $page, $tag ?? null, null);

        return new JsonResponse($this->renderPaginator($paginator, ['post']));
    }

    /**
     * Delete all user posts.
     *
     * @Route("/posts/", methods={"delete"})
     */
    public function deleteAll(PostRepository $postRepository)
    {
        $postRepository->removeAll($this->getUser());

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    private function processTags(Request $request, Post $post)
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
