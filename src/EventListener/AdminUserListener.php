<?php

namespace App\EventListener;

use App\Entity\Admin;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserListener implements EventSubscriberInterface
{
	/**
	 * @var UserPasswordHasherInterface
	 */
	private $passwordHasher;

	private $entityManager;

	public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->passwordHasher = $passwordHasher;
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
		$this->encodePassword($args);
	}

	public function preUpdate(LifecycleEventArgs $args): void
	{
		$this->encodePassword($args);
	}

	public function encodePassword(LifecycleEventArgs $args): void
	{
		$entity = $args->getObject();

		if (!$entity instanceof Admin) {
			return;
		}
		if($args->hasChangedField("password")){
			$hashedPassword = $this->passwordHasher->hashPassword(
				$entity,
				$args->getNewValue("password")
			);
			$entity->setPassword($hashedPassword);
		}

	}
}