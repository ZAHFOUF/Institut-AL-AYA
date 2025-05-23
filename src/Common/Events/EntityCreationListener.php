<?php

namespace AlAya\Common\Events;

use Doctrine\Persistence\Event\LifecycleEventArgs ;

class EntityCreationListener
{
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setCreatedAt')) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if (method_exists($entity, 'setSubmittedAt')) {
            $entity->setSubmittedAt(new \DateTimeImmutable());
        }

    }

    public function postPersist(LifecycleEventArgs $args): void
    {
        // TO DO
    }
}
