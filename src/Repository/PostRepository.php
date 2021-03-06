<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use App\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    private const ORDER_DESC = 'DESC';
    private const ORDER_ASC = 'ASC';

    private array $availableSortList = [
        'created' => 'p.createdAt', // default sort
        'title' => 'p.title',
    ];

    private array $availableSortOrderList = [
        self::ORDER_DESC, // default order
        self::ORDER_ASC,
    ];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function create(Post $post)
    {
        $this->_em->persist($post);
        $this->_em->flush();
    }

    public function update(Post $post)
    {
        $this->_em->persist($post);
        $this->_em->flush();
    }

    public function remove(Post $post)
    {
        $this->_em->remove($post);
        $this->_em->flush();
    }

    public function removeAll(User $user)
    {
        foreach ($user->getPosts() as $post) {
            $this->_em->remove($post);
        }

        $this->_em->flush();
    }

    public function findLatest(User $user = null, int $page = 1, Tag $tag = null, string $searchQuery = null, string $sort = null, string $order = null): Paginator
    {
        $qb = $this->createQueryBuilder('p')
            ->addSelect('u', 't')
            ->innerJoin('p.user', 'u')
            ->leftJoin('p.tags', 't')
        ;

        // user posts
        if (null !== $user) {
            $qb
                ->andWhere('p.user = :user')
                ->setParameter('user', $user)
            ;
        } else {
            $qb
                ->andWhere('p.isPrivate = :is_private')
                ->setParameter('is_private', false)
            ;
        }

        // search by tag
        if (null !== $tag) {
            $qb
                ->andWhere(':tag MEMBER OF p.tags')
                ->setParameter('tag', $tag)
            ;
        }

        // search in post title by query
        if (null !== $searchQuery) {
            $searchTerms = $this->extractSearchTerms($searchQuery);

            if (0 !== \count($searchTerms)) {
                $orStatements = $qb->expr()->orX();

                foreach ($searchTerms as $term) {
                    $orStatements->add(
                        $qb->expr()->like('p.title', $qb->expr()->literal('%'.$term.'%'))
                    );
                }

                $qb->andWhere($orStatements);
            }
        }

        // sort
        list($sort, $order) = $this->getSort($sort, $order);
        $qb->orderBy($sort, $order);

        return (new Paginator($qb))->paginate($page);
    }

    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', preg_replace('/\s+/', ' ', trim($searchQuery))));

        // ignore the search terms that are too short
        return array_filter($terms, function ($term) {
            return 2 <= mb_strlen($term);
        });
    }

    private function getSort(string $sort = null, string $order = null): array
    {
        $sort = $this->availableSortList[$sort] ?? current($this->availableSortList);

        if (null !== $order) {
            $order = mb_strtoupper($order);
        }

        if (!\in_array($order, $this->availableSortOrderList, true)) {
            $order = self::ORDER_DESC;
        }

        return [$sort, $order];
    }
}
