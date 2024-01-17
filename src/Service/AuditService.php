<?php
namespace App\Service;

use App\Entity\Action;
use App\Entity\BaseUser;
use App\Entity\TableHistory;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class AuditService
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function add(Action $action, ?TableHistory $history = null): void
    {
        /** @var BaseUser $user */
        $user = $this->security->getUser();

        if ($history) {
            if ($user instanceof User) {
                $history->setUser($user);
            }
            $this->em->persist($history);
            $this->em->flush();
            $action->setTableHistory($history);
        }

        $action->setUser($user);
        $this->em->persist($action);
        $this->em->flush();
    }

    /**
     * @param Action $action
     * @return null|object
     */
    public function getEntity(Action $action): ?object
    {
        if ($history = $action->getTableHistory()) {
            $class = $history->getTable();
            // The namespace AppBundle was renamed to App in the 2018 release
            $class = str_replace('AppBundle', 'App', $class);
            $id = $history->getEntry();

            if (!class_exists($class)) {
                return null;
            }
            return $this->em->getRepository($class)->find($id);
        } elseif (str_starts_with($action->getAction(), 'profile') || $action->getAction() === 'user-added') {
            return $this->em->getRepository(User::class)->find($action->getData1());
        } else {
            return null;
        }
    }
}
