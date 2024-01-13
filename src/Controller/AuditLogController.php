<?php
namespace App\Controller;

use App\Entity\Action;
use App\Service\AuditService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class AuditLogController extends AbstractController
{
    public function indexAction(EntityManagerInterface $em, AuditService $auditService): Response
    {
        $actions = [
            'profile-group-added' => 'Added a permission to a user',
            'profile-group-removed' => 'Removed a permission from a user',
            'profile-notes-updated' => 'Updated user notes',
            'profile-details-updated' => 'Updated user details',
            'user-added' => 'Added a user to the team',
            'award-added' => 'Created an award',
            'award-delete' => 'Deleted an award',
            'award-edited' => 'Edited an award',
            'add-video-game' => 'Added a video game to the main autocompleter',
            'remove-video-game' => 'Removed a video game from the main autocompleter',
            'reload-video-games' => 'Reloaded the list of video game releases',
            'config-updated' => 'Updated the website config',
            'mass-nomination-open' => 'Opened nominations for all awards',
            'mass-nomination-close' => 'Closed nominations for all awards',
            'nominee-new' => 'Added a nominee to an award',
            'nominee-delete' => 'Removed a nominee from an award',
            'nominee-edit' => 'Edited an award nominee',
            'winner-image-upload' => 'Uploaded an image for an award winner',
            'advert-new' => 'Created an advert',
            'advert-edit' => 'Edited an advert',
            'advert-delete' => 'Deleted an advert',
            'item-new' => 'Created a lootbox reward',
            'item-edit' => 'Edited a lootbox reward',
            'item-delete' => 'Deleted a lootbox reward',
            'cron-results-enabled' => 'Enabled the result generator process',
            'cron-results-disabled' => 'Disabled the result generator process',
            'config-readonly-enabled' => 'Turned on read-only mode',
            'template-added' => 'Added a new site template',
            'template-edited' => 'Edited a site template',
            'autocompleter-added' => 'Created an autocompleter',
            'autocompleter-edited' => 'Edited an autocompleter',
            'autocompleter-deleted' => 'Deleted an autocompleter',
//            'fantasy-signed-up' => 'Signed up for the Fantasy League',
//            'fantasy-picked' => 'Made a pick in the Fantasy League',
//            'fantasy-updated-details' => 'Updated their details in the Fantasy League',
            'arg-times-updated' => 'Updated file unlock times for the ARG',
            'arg-config-updated' => 'Updated the ARG settings',
            'captcha-game-new' => 'Added a game to the captcha',
            'captcha-game-edit' => 'Edited a game in the captcha',
            'captcha-game-delete' => 'Removed a game from the captcha',
            'captcha-game-bulk-upload' => 'Bulk uploaded images for the captcha',
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

        return $this->render('auditLog.html.twig', [
            'title' => 'Audit Log',
            'actions' => $result,
            'actionTypes' => $actions,
            'auditService' => $auditService,
        ]);
    }
}
