<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\AuthService;
use App\Services\Riders\RidersService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class RiderController extends AbstractController
{
    public function __construct(
        private Environment $twig,
        private UserService $userService,
        private AuthService $authService,
        private RidersService $ridersService
    ) {
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

    #[Route('/livreurs', name:'admin.livreurs')]
    public function getRiders() : Response
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

        //get riders request by status "pending"
        $result  = $this->ridersService->getRiders($user);
        if ($result['status'] == false ) {
            $riders = [];
            $this->addFlash('riders-error', $result['message']);
        } else {
            $riders = $result['riders'];
        }
        return new Response($this->twig->render('./admins/riders/list.html.twig',[
            'riders' => $riders
        ]));
    }

    #[Route('/livreurs/{uuid}', name:'admin.livreurs.show')]
    public function getRider($uuid) : Response
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
        
        $result = $this->ridersService->getRider($user, $uuid);
        if ($result['status'] == false) {
            $rider = null;
            $this->addFlash('riders-error', $result['message']);
        } else {
            $rider = $result['rider'];
        }
        return new Response($this->twig->render('./admins/riders/show.html.twig',[
            'rider' => $rider
        ]));
    }

    #[Route('/livreurs/modification/{uuid}/{status}', name: 'admin.livreurs.status')]
    public function setCompanyStatus($uuid, $status): Response
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

        //Update status
        $result = $this->ridersService->updateStatus($user, $uuid, $status);

        if ($result['status'] == false) {
            $this->addFlash('riders-error', $result['message']);
        } else {
            $this->addFlash('edit-rider', $result['message']);
        }

        return $this->redirectToRoute('admin.livreurs.show', [
            'uuid' => $uuid
        ]);
    }
}