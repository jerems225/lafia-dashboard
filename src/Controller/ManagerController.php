<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminType;
use App\Form\EditAdminPasswordType;
use App\Form\EditAdminType;
use App\Services\AuthService;
use App\Services\ManagerService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ManagerController extends AbstractController
{
    public function __construct(private Environment $twig,
    private UserService $userService,
    private AuthService $authService,
    private ManagerService $managerService)
    {
        
    }

    public function Access(User $user): bool
    {
        $result = $this->authService->RoleChecker($user);
        if (!$result['status']) {
            $this->addFlash('Access-Checker', $result['message']);
            return false;
        }

        return true;
    }

    #[Route('/gestionnaires', name:'admin.managers')]
    public function getAdmins(Request $request) : Response
    {
        $logger = $this->getUser();
        if (!$logger) {
            return $this->redirectToRoute('admin.login');
        }
        //find user informations
        $user = $this->userService->findUser('email', $logger->getUserIdentifier());

        //Check User Access
        $access = $this->Access($user);
        if (!$access) {
            return $this->redirectToRoute('app_logout');
        }

        $result = $this->managerService->getAdmins($user);
        if ($result['status'] == false) {
            $admins = [];
            $this->addFlash('admin-error', $result['message']);
        } else {
            $admins = $result['admins'];
        }

        $form = $this->createForm(AdminType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //call add service
            $admin_array = [
                'firstName' => $form->get('firstName')->getData(),
                'lastName' => $form->get('lastName')->getData(),
                'email' => $form->get('email')->getData(),
                'phone' => $form->get('phone')->getData(),
                'password' => $form->get('password')->getData()
            ];

            $addResult = $this->managerService->addAdmin($user, $admin_array);
            if($addResult['status'] == false)
            {
                $this->addFlash('admin-error', $addResult['message']);
            }
            else
            {
                $this->addFlash('admin-success', 'Le processus d\'ajout d\'un gestionnaire a bien été pris en compte !');
            }

            return $this->redirectToRoute('admin.managers');
        }

        return new Response($this->twig->render('./admins/managers/list.html.twig',[
            'admins' => $admins,
            'form' => $form->createView()
        ]));
    }

    #[Route('/gestionnaires/modification/{uuid}', name:'admin.managers.edit')]
    public function updateAdmin(Request $request, $uuid) : Response
    {
        $logger = $this->getUser();
        if (!$logger) {
            return $this->redirectToRoute('admin.login');
        }
        //find user informations
        $user = $this->userService->findUser('email', $logger->getUserIdentifier());

        //Check User Access
        $access = $this->Access($user);
        if (!$access) {
            return $this->redirectToRoute('app_logout');
        }

        $result = $this->managerService->getAdmin($user, $uuid);
        if ($result['status'] == false) {
            $admin = [];
            $this->addFlash('admin-error', $result['message']);
        } else {
            $admin = $result['admin'];
        }

        $form = $this->createForm(EditAdminType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            //call add service
            $admin_array = [
                'firstName' => $form->get('firstName')->getData(),
                'lastName' => $form->get('lastName')->getData(),
                'email' => $form->get('email')->getData(),
                'phone' => $form->get('phone')->getData(),
                'role' => $form->get('role')->getData(),
                'userId' => $user->getUuid()
            ];

            $updateResult = $this->managerService->updateAdmin($user, $admin_array, $uuid);
            if($updateResult['status'] == false)
            {
                $this->addFlash('admin-error', $updateResult['message']);
            }
            else
            {
                $this->addFlash('admin-success', 'Le processus de modification d\'un gestionnaire a bien été pris en compte !');
            }

            return $this->redirectToRoute('admin.managers');
        }

        return new Response($this->twig->render('./admins/managers/modify.html.twig',[
            'admin' => $admin,
            'form' => $form->createView()
        ]));
    }

    #[Route('/gestionnaires/modification/mot-de-passe/{uuid}', name:'admin.managers.edit.password')]
    public function updateAdminPassword(Request $request, $uuid) : Response
    {
        $logger = $this->getUser();
        if (!$logger) {
            return $this->redirectToRoute('admin.login');
        }
        //find user informations
        $user = $this->userService->findUser('email', $logger->getUserIdentifier());

        //Check User Access
        $access = $this->Access($user);
        if (!$access) {
            return $this->redirectToRoute('app_logout');
        }

        $result = $this->managerService->getAdmin($user, $uuid);
        if ($result['status'] == false) {
            $admin = [];
            $this->addFlash('admin-error', $result['message']);
        } else {
            $admin = $result['admin'];
        }

        $form = $this->createForm(EditAdminPasswordType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $admin_array = [
                'password' => $form->get('password')->getData(),
                'userId' => $user->getUuid()
            ];

            $addResult = $this->managerService->updatePassword($user, $admin_array, $uuid);
            if($addResult['status'] == false)
            {
                $this->addFlash('admin-error', $addResult['message']);
            }
            else
            {
                $this->addFlash('admin-success', 'Le processus de modification du mot de passe d\'un gestionnaire a bien été pris en compte !');
            }

            return $this->redirectToRoute('admin.managers');
        }

        return new Response($this->twig->render('./admins/managers/modify-password.html.twig',[
            'admin' => $admin,
            'form' => $form->createView()
        ]));
    }
}