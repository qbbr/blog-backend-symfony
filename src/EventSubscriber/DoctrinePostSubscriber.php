<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class DoctrinePostSubscriber implements EventSubscriber
{
    /**
     * @var SluggerInterface
     */
    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $this->setSlug($args);
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $this->setSlug($args);
    }

    private function setSlug(LifecycleEventArgs $args)
    {
        /** @var Post $entity */
        $entity = $args->getObject();

        if (!$entity instanceof Post) {
            return;
        }

        $entity->setSlug($this->slugger->slug($entity->getTitle()));
    }
}
