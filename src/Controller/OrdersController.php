<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\AuthService;
use App\Services\Orders\OrdersService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class OrdersController extends AbstractController
{
    public function __construct(private Environment $twig,
    private UserService $userService,
    private AuthService $authService,
    private OrdersService $ordersService)
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

    #[Route('/suivi-de-commandes', name:'admin.orders')]
    public function getOrders() : Response
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

        $result = $this->ordersService->getOrders($user);
        if ($result['status'] == false) {
            $orders = [];
            $this->addFlash('orders-error', $result['message']);
        } else {
            $orders = $result['orders'];
        }

        return new Response($this->twig->render('./admins/orders/list.html.twig',[
            'orders' => $orders
        ]));
    }
}