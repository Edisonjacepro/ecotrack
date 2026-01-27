<?php

namespace App\EventSubscriber;

use App\Entity\CarbonRecord;
use App\Entity\User;
use App\Entity\UserEcoAction;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\SecurityBundle\Security;

class EntityOwnerSubscriber implements EventSubscriber
{
    public function __construct(private Security $security)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [Events::prePersist];
    }

    /**
     * @param LifecycleEventArgs<ObjectManager> $args
     */
    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof CarbonRecord && !$entity instanceof UserEcoAction) {
            return;
        }

        if ($entity->getUser() instanceof User) {
            return;
        }

        $user = $this->security->getUser();
        if ($user instanceof User) {
            $entity->setUser($user);
        }
    }
}
