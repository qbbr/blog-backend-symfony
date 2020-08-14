<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    public function upsert(string $name): Tag
    {
        $tag = $this->findOneBy(['name' => $name]);

        if (!$tag) {
            $tag = new Tag();
            $tag->setName($name);
            $this->_em->persist($tag);
        }

        return $tag;
    }

    public function removeAllEmpty(): int
    {
        $ids = $this->createQueryBuilder('t')
            ->select('t.id')
            ->leftJoin('t.posts', 'p')
            ->where('p.id IS NULL')
            ->getQuery()
            ->getResult();

        return $this->createQueryBuilder('t')
            ->where('t.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->delete()
            ->getQuery()
            ->execute();
    }
}
