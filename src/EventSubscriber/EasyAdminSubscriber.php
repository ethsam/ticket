<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['addData'],
            BeforeEntityUpdatedEvent::class => ['updateUser'],
        ];
    }

    public function updateUser(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ( $entity instanceof User ) {
            $this->setPassword($entity);
        } else {
            return;
        }

    }

    public function addData(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if ($entity instanceof User) {
            $this->setPassword($entity);
        } else {
            return;
        }

    }



    /**
     * @param User $entity
     */
    public function setPassword(User $entity): void
    {
        //$pass = $entity->getPassword();
        $pass = $entity->getClearpassword();

        //dd($pass);

        if ( "" !== $pass ) {
            $entity->setPassword(
                $this->passwordEncoder->hashPassword(
                    $entity,
                    $pass
                )
            );
            $entity->setClearpassword("");
            $this->entityManager->persist($entity);
            $this->entityManager->flush();
        }
    }

}
