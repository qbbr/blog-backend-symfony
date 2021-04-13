<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\String\Slugger\SluggerInterface;

class DoctrinePostSubscriber implements EventSubscriber
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->setSlug($args);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $this->setSlug($args);
    }

    private function setSlug(LifecycleEventArgs $args): void
    {
        /** @var Post $entity */
        $entity = $args->getObject();

        if (!$entity instanceof Post) {
            return;
        }

        $entity->setSlug($this->slugger->slug($entity->getTitle()));
        $entity->setHtml((new \Parsedown())->text($entity->getText()));
    }
}
