<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Action;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AuditLogController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $actions = [
            'profile-group-added' => 'Added a permission to a user',
            'profile-group-removed' => 'Removed a permission from a user',
            'profile-notes-updated' => 'Updated user notes',
            'profile-details-updated' => 'Updated user details',
            'award-added' => 'Created an award',
            'award-delete' => 'Deleted an award',
            'award-edited' => 'Edited an award',
            'add-video-game' => 'Added a video game to the autocomplete list',
            'config-updated' => 'Updated the website config',
            'mass-nomination-change' => 'Opened or closed all nominations',
            'nominee-new' => 'Added a nominee to an award',
            'nominee-deleted' => 'Removed a nominee from an award',
            'nominee-edit' => 'Edited an award nominee',
            'winner-image-upload' => 'Uploaded an image for an award winner',
        ];

        $result = $em->createQueryBuilder()
            ->select('a')
            ->from(Action::class, 'a')
            ->where('a.user IS NOT NULL')
            ->andWhere('a.action IN (:actions)')
            ->setParameter('actions', array_keys($actions))
            ->orderBy('a.timestamp', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('auditLog.twig', [
            'title' => 'Audit Log',
            'actions' => $result,
            'actionTypes' => $actions,
        ]);
    }
}
