<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGenerator;
use VGA\Model\Action;
use VGA\Model\Permission;
use VGA\Model\TableHistory;
use VGA\Model\User;

class PeopleController extends BaseController
{
    public function indexAction()
    {
        $tpl = $this->twig->loadTemplate('people.twig');

        $users = $this->em->getRepository(User::class)->findBy(
            ['special' => true], ['name' => 'ASC']
        );

        $response = new Response($tpl->render([
            'title' => 'People',
            'users' => $users
        ]));
        $response->send();
    }

    public function viewAction($steamID)
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->find($steamID);

        if (!$user || !$user->isSpecial()) {
            $this->session->getFlashBag()->add('error', 'Invalid SteamID provided.');
            $response = new RedirectResponse($this->generator->generate('people'), [], UrlGenerator::ABSOLUTE_URL);
            $response->send();
            return;
        }

        $tpl = $this->twig->loadTemplate('viewPerson.twig');
        $response = new Response($tpl->render([
            'title' => $user->getName(),
            '_user' => $user
        ]));
        $response->send();
    }

    public function editAction($steamID)
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->find($steamID);

        if (!$user || !$user->isSpecial()) {
            $this->session->getFlashBag()->add('error', 'Invalid SteamID provided.');
            $response = new RedirectResponse($this->generator->generate('people'), [], UrlGenerator::ABSOLUTE_URL);
            $response->send();
            return;
        }

        $tpl = $this->twig->loadTemplate('editPerson.twig');
        $response = new Response($tpl->render([
            'title' => $user->getName(),
            '_user' => $user
        ]));
        $response->send();
    }

    public function postAction($steamID)
    {
        /** @var User $user */
        $user = $this->em->getRepository(User::class)->find($steamID);

        if (!$user || !$user->isSpecial()) {
            $this->session->getFlashBag()->add('error', 'Invalid SteamID provided.');
            $response = new RedirectResponse($this->generator->generate('people'), [], UrlGenerator::ABSOLUTE_URL);
            $response->send();
            return;
        }
        
        $post = $this->request->request;

        // Remove group
        if ($post->get('RemoveGroup') && $this->user->canDo('profile-edit-groups')) {
            $groupName = $post->get('RemoveGroup');

            /** @var Permission $group */
            $group = $this->em->getRepository(Permission::class)->find($groupName);
            $user->removePermission($group);
            $this->em->persist($user);

            $this->session->getFlashBag()->add('formSuccess', 'Group successfully removed.');

            $action = new Action('profile-group-removed');
            $action->setUser($this->user)
                ->setPage(__CLASS__)
                ->setData1($steamID)
                ->setData2($groupName);

            $this->em->persist($action);
            $this->em->flush();
        }

        // Add group
        if ($post->get('AddGroup') && $this->user->canDo('profile-edit-groups')) {
            $groupName = trim(strtolower($post->get('GroupName')));

            /** @var Permission $group */
            $group = $this->em->getRepository(Permission::class)->find($groupName);
            if (!$group) {
                $this->session->getFlashBag()->add('formError', 'Invalid group name.');
            } elseif ($user->getPermissions()->contains($group)) {
                $this->session->getFlashBag()->add('formError', 'User already has that group.');
            } else {
                $user->addPermission($group);

                $action = new Action('profile-group-added');
                $action->setUser($this->user)
                    ->setPage(__CLASS__)
                    ->setData1($steamID)
                    ->setData2($groupName);

                $this->em->persist($action);
                $this->em->flush();

                $this->session->getFlashBag()->add('formSuccess', 'Group successfully added.');
            }
        }

        // Edit details (primary role and email)
        if ($post->get('action') === 'edit-details' && $this->user->canDo('profile-edit-details')) {
            $user->setPrimaryRole($post->get('PrimaryRole'));
            $user->setEmail($post->get('Email'));
            $this->em->persist($user);

            $action = new Action('profile-details-updated');
            $action->setUser($this->user)
                ->setPage(__CLASS__)
                ->setData1($steamID);
            $this->em->persist($action);

            $history = new TableHistory();
            $history->setUser($this->user)
                ->setTable('User')
                ->setEntry($steamID)
                ->setValues($post->all());
            $this->em->persist($history);

            $this->em->flush();

            $this->session->getFlashBag()->add('formSuccess', 'Details successfully updated.');
        }

        if ($post->get('action') === 'edit-notes' && $this->user->canDo('profile-edit-notes')) {
            $user->setNotes($post->get('Notes'));
            $this->em->persist($user);

            $action = new Action('profile-notes-updated');
            $action->setUser($this->user)
                ->setPage(__CLASS__)
                ->setData1($steamID);
            $this->em->persist($action);

            $history = new TableHistory();
            $history->setUser($this->user)
                ->setTable('User')
                ->setEntry($steamID)
                ->setValues($post->all());
            $this->em->persist($history);

            $this->em->flush();

            $this->session->getFlashBag()->add('formSuccess', 'Notes successfully updated.');
        }

        $response = new RedirectResponse(
            $this->generator->generate('viewPerson', ['steamID' => $steamID], UrlGenerator::ABSOLUTE_URL)
        );
        $response->send();
    }

    public function permissionsAction()
    {
        $tpl = $this->twig->loadTemplate('permissions.twig');
        $response = new Response($tpl->render([
            'title' => 'Permissions'
        ]));
        $response->send();
    }

    public function newAction()
    {
        $tpl = $this->twig->loadTemplate('addPerson.twig');
        $response = new Response($tpl->render([
            'title' => 'Add Person'
        ]));
        $response->send();
    }

    public function searchAction()
    {
        $post = $this->request->request;
        $response = new JsonResponse();

        try {
            $steam = \SteamId::create($post->get('id'));
        } catch (\SteamCondenserException $e) {
            $response->setData(['error' => 'no matches']);
            $response->send();
            return;
        }

        $repo = $this->em->getRepository(User::class);

        /** @var User $user */
        $user = $repo->find($steam->getSteamId64());
        if (!$user) {
            $user = new User($steam->getSteamId64());
            $avatar = base64_encode(file_get_contents($steam->getMediumAvatarUrl()));
            $user
                ->setName($steam->getNickname())
                ->setAvatar($avatar);
        }

        if ($user->isSpecial()) {
            $response->setData([
                'error' => 'already special',
                'name' => $user->getName()
            ]);
            $response->send();
            return;
        }

        if ($post->getBoolean('add')) {
            // Make the user special and give them level 1 access
            $user->setSpecial(true);
            /** @var Permission $permission */
            $permission = $this->em->getRepository(Permission::class)->find('level1');
            $user->addPermission($permission);
            $this->em->persist($user);
            $this->em->flush();

            $response->setData(['success' => true]);
            $response->send();
            return;
        }

        $response->setData([
            'success' => true,
            'name' => $user->getName(),
            'avatar' => $user->getAvatar(),
            'steamID' => $user->getSteamID()
        ]);
        $response->send();
    }
}
