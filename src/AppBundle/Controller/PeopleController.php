<?php
namespace AppBundle\Controller;

use AppBundle\Service\ConfigService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Action;
use AppBundle\Entity\Permission;
use AppBundle\Entity\TableHistory;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PeopleController extends Controller
{
    public function indexAction(EntityManagerInterface $em)
    {
        $users = $em->getRepository(User::class)->findBy(
            ['special' => true], ['name' => 'ASC']
        );

        return $this->render('people.twig', [
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

        return $this->render('viewPerson.twig', [
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

        return $this->render('editPerson.twig', [
            'title' => $user->getName(),
            'user' => $user
        ]);
    }

    public function postAction($steamID, ConfigService $configService, EntityManagerInterface $em, Request $request, AuthorizationCheckerInterface $authChecker, UserInterface $currentUser)
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

            $action = new Action('profile-group-removed');
            $action->setUser($currentUser)
                ->setData1($steamID)
                ->setData2($groupName);

            $em->persist($action);
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

                $action = new Action('profile-group-added');
                $action->setUser($currentUser)
                    ->setData1($steamID)
                    ->setData2($groupName);

                $em->persist($action);
                $em->flush();

                $this->addFlash('formSuccess', 'Permission successfully added.');
            }
        }

        // Edit details (primary role and email)
        if ($post->get('action') === 'edit-details' && $authChecker->isGranted('ROLE_PROFILE_EDIT_DETAILS')) {
            $user->setPrimaryRole($post->get('PrimaryRole'));
            $user->setEmail($post->get('Email'));
            $em->persist($user);

            $action = new Action('profile-details-updated');
            $action->setUser($currentUser)
                ->setData1($steamID);
            $em->persist($action);

            $history = new TableHistory();
            $history->setUser($currentUser)
                ->setTable('User')
                ->setEntry($steamID)
                ->setValues($post->all());
            $em->persist($history);

            $em->flush();

            $this->addFlash('formSuccess', 'Details successfully updated.');
        }

        if ($post->get('action') === 'edit-notes' && $authChecker->isGranted('ROLE_PROFILE_EDIT_NOTES')) {
            $user->setNotes($post->get('Notes'));
            $em->persist($user);

            $action = new Action('profile-notes-updated');
            $action->setUser($currentUser)
                ->setData1($steamID);
            $em->persist($action);

            $history = new TableHistory();
            $history->setUser($currentUser)
                ->setTable('User')
                ->setEntry($steamID)
                ->setValues($post->all());
            $em->persist($history);

            $em->flush();

            $this->addFlash('formSuccess', 'Notes successfully updated.');
        }

        return $this->redirectToRoute('viewPerson', ['steamID' => $steamID]);
    }

    public function permissionsAction()
    {
        return $this->render('permissions.twig');
    }

    public function newAction()
    {
        return $this->render('addPerson.twig');
    }

    public function searchAction(EntityManagerInterface $em, Request $request, ConfigService $configService)
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

            // Make the user special and give them level 1 access
            $user->setSpecial(true);
            /** @var Permission $permission */
            $permission = $em->getRepository(Permission::class)->find('level1');
            $user->addPermission($permission);
            $em->persist($user);
            $em->flush();

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
