<?php
namespace VGA\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use VGA\Model\Permission;
use VGA\Model\User;
use VGA\Utils;

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
            $response = new RedirectResponse($this->generator->generate('people'));
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
            $response = new RedirectResponse($this->generator->generate('people'));
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
            $response = new RedirectResponse($this->generator->generate('people'));
            $response->send();
            return;
        }
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
        $repo = $this->em->getRepository(User::class);
        /** @var User $user */
        $user = $repo->find($this->request->request->get('ID'));

        $response = new JsonResponse();
        if (!$user) {
            $response->setData(['error' => 'no matches']);
            $response->send();
            return;
        }

        if ($user->isSpecial()) {
            $response->setData([
                'error' => 'already special',
                'name' => $user->getName()
            ]);
            $response->send();
            return;
        }

        if ($this->request->request->getBoolean('Add')) {
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
            'Name' => $user->getName(),
            'Avatar' => $user->getAvatar(),
            'SteamID' => $user->getSteamID()
        ]);
        $response->send();
    }
}
