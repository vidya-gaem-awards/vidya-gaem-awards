<?php
namespace AppBundle\Controller;

use AppBundle\Service\AuditService;
use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Action;
use AppBundle\Entity\Permission;
use AppBundle\Entity\TableHistory;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PeopleController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $users = $em->getRepository(User::class)->findBy(
            ['special' => true], ['name' => 'ASC']
        );

        return $this->render('people.html.twig', [
            'title' => 'People',
            'users' => $users
        ]);
    }

    public function viewAction($steamID, EntityManagerInterface $em)
    {
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => $steamID]);

        if (!$user || !$user->isSpecial()) {
            $this->addFlash('error', 'Invalid SteamID provided.');
            return $this->redirectToRoute('people');
        }

        $permissions = $em->getRepository(Permission::class)->findAll();

        return $this->render('viewPerson.html.twig', [
            'title' => $user->getName(),
            'user' => $user,
            'permissions' => $permissions
        ]);
    }

    public function editAction($steamID, EntityManagerInterface $em)
    {
        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => $steamID]);

        if (!$user || !$user->isSpecial()) {
            $this->addFlash('error', 'Invalid SteamID provided.');
            return $this->redirectToRoute('people');
        }

        return $this->render('editPerson.html.twig', [
            'title' => $user->getName(),
            'user' => $user
        ]);
    }

    public function postAction($steamID, ConfigService $configService, EntityManagerInterface $em, Request $request, AuthorizationCheckerInterface $authChecker, AuditService $auditService)
    {
        if ($configService->isReadOnly()) {
            $this->addFlash('error', 'The site is currently in read-only mode. No changes can be made.');
            return $this->redirectToRoute('people');
        }

        /** @var User $user */
        $user = $em->getRepository(User::class)->findOneBy(['username' => $steamID]);

        if (!$user || !$user->isSpecial()) {
            $this->addFlash('error', 'Invalid SteamID provided.');
            return $this->redirectToRoute('people');
        }
        
        $post = $request->request;

        // Remove group
        if ($post->get('RemoveGroup') && $authChecker->isGranted('ROLE_PROFILE_EDIT_GROUPS')) {
            $groupName = $post->get('RemoveGroup');

            /** @var Permission $group */
            $group = $em->getRepository(Permission::class)->find($groupName);
            $user->removePermission($group);
            $em->persist($user);

            $this->addFlash('formSuccess', 'Group successfully removed.');

            $auditService->add(
                new Action('profile-group-removed', $user->getId(), $groupName)
            );
            $em->flush();
        }

        // Add group
        if ($post->get('AddGroup') && $authChecker->isGranted('ROLE_PROFILE_EDIT_GROUPS')) {
            $groupName = trim(strtolower($post->get('GroupName')));

            /** @var Permission $group */
            $group = $em->getRepository(Permission::class)->find($groupName);
            if (!$group) {
                $this->addFlash('formError', 'Invalid group name.');
            } elseif ($user->getPermissions()->contains($group)) {
                $this->addFlash('formError', 'User already has that permission.');
            } else {
                $user->addPermission($group);
                $auditService->add(
                    new Action('profile-group-added', $user->getId(), $groupName)
                );
                $em->flush();

                $this->addFlash('formSuccess', 'Permission successfully added.');
            }
        }

        // Edit details (primary role and email)
        if ($post->get('action') === 'edit-details' && $authChecker->isGranted('ROLE_PROFILE_EDIT_DETAILS')) {
            $user->setPrimaryRole($post->get('PrimaryRole'));
            $user->setEmail($post->get('Email'));
            $em->persist($user);

            $auditService->add(
                new Action('profile-details-updated', $user->getId()),
                new TableHistory(User::class, $user->getId(), $post->all())
            );

            $em->flush();

            $this->addFlash('formSuccess', 'Details successfully updated.');
        }

        if ($post->get('action') === 'edit-notes' && $authChecker->isGranted('ROLE_PROFILE_EDIT_NOTES')) {
            $user->setNotes($post->get('Notes'));
            $em->persist($user);

            $auditService->add(
                new Action('profile-notes-updated', $user->getId()),
                new TableHistory(User::class, $user->getId(), $post->all())
            );
            $em->flush();

            $this->addFlash('formSuccess', 'Notes successfully updated.');
        }

        return $this->redirectToRoute('viewPerson', ['steamID' => $steamID]);
    }

    public function permissionsAction()
    {
        return $this->render('permissions.html.twig');
    }

    public function newAction(EntityManagerInterface $em)
    {
        $permissions = $em->getRepository(Permission::class)->findAll();

        return $this->render('addPerson.html.twig', [
            'permissions' => $permissions
        ]);
    }

    public function searchAction(EntityManagerInterface $em, Request $request, ConfigService $configService, AuditService $auditService)
    {
        $post = $request->request;

        // Perform basic profile URL parsing by only keeping the characters after the last slash.
        $id = trim($post->get('id'), '/ ');
        if (strpos($id, '/') !== false) {
            $id = substr($id, strrpos($id, '/') + 1);
        }

        try {
            $steam = \SteamId::create($id);
        } catch (\SteamCondenserException $e) {
            return $this->json(['error' => 'no matches']);
        }

        // Annoyingly, when you link a Steam account with Discord, the steam ID it gives back is incorrect.
        // See https://github.com/discordapp/discord-api-docs/issues/271.
        // Specifically, the 32nd bit (representing the instance of the account) is 0 when it should be 1.
        // If we detect the issue, we flip the bit and refetch the user using the correct ID.
        $binary = str_pad(decbin($steam->getSteamId64()), 64, '0', STR_PAD_LEFT);
        if ($binary[31] === '0') {
            $binary[31] = '1';
            $steam = \SteamId::create(bindec($binary));
        }

        $repo = $em->getRepository(User::class);

        /** @var User $user */
        $user = $repo->findOneBy(['username' => $steam->getSteamId64()]);
        if (!$user) {
            $user = new User();
            $user
                ->setSteamID($steam->getSteamId64())
                ->setName($steam->getNickname())
                ->setAvatar($steam->getMediumAvatarUrl());
        }

        if ($user->isSpecial()) {
            return $this->json([
                'error' => 'already special',
                'name' => $user->getName()
            ]);
        }

        if ($post->getBoolean('add')) {
            if ($configService->isReadOnly()) {
                return $this->json(['error' => 'The site is currently in read-only mode. No changes can be made.']);
            }

            // Make the user special and give them the starting permission
            $user->setSpecial(true);

            if ($post->get('permission')) {
                /** @var Permission $permission */
                $permission = $em->getRepository(Permission::class)->find($post->get('permission'));
                if (!$permission) {
                    return $this->json(['error' => 'Invalid permission specified.']);
                }
                $user->addPermission($permission);
            }
            $em->persist($user);
            $em->flush();

            $auditService->add(
                new Action('user-added', $user->getId(), $post->get('permission') ?: null)
            );

            return $this->json(['success' => true]);
        }

        return $this->json([
            'success' => true,
            'name' => $user->getName(),
            'avatar' => $user->getAvatar(),
            'steamID' => $user->getSteamID()
        ]);
    }
}
