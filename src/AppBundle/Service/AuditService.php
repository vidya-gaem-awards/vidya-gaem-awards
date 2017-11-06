<?php
namespace AppBundle\Service;

use AppBundle\Entity\Action;
use AppBundle\Entity\TableHistory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuditService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function add(Action $action, ?TableHistory $history = null)
    {
        $user = $this->tokenStorage->getToken()->getUser();

        if ($history) {
            $history->setUser($user);
            $this->em->persist($history);
            $this->em->flush();
            $action->setTableHistory($history);
        }

        $action->setUser($user);
        $this->em->persist($action);
        $this->em->flush();
    }
}
