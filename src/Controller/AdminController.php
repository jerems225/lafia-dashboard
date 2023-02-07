<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\AuthService;
use App\Services\OverviewService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AdminController extends AbstractController
{
    public function __construct(
        private Environment $twig,
        public OverviewService $overviewService,
        public UserService $userService,
        private AuthService $authService
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

    #[Route('/', name: 'admin.index')]
    public function AdminIndex(Request $request): Response
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

        $date = new \DateTimeImmutable();
        $statYear = $date->format('Y');

        if (count($_REQUEST) > 0) {
            $statYear = $request->request->get('statsyear');
        }



        return new Response($this->twig->render('./admins/index.html.twig', [
            'users' => $this->overviewService->getUsersCount($user),
            'companies' => $this->overviewService->getCompaniesCount($user),
            'orders' => $this->overviewService->getOrdersCount($user, "delivered"),
            'sales' => $this->overviewService->getOrdersByMonth($user, "delivered", $statYear),
            'ordersPending' => $this->overviewService->getOrdersByMonth($user, "pending", $statYear),
            'ordersCanceled' => $this->overviewService->getOrdersByMonth($user, "canceled", $statYear),
            'statsyear' => $this->overviewService->statsYear(),
            'statYear' => $statYear,
            'requests' => $this->overviewService->getCompaniesByMonth($user, null, $statYear),
            'requests_sum' => array_sum($this->overviewService->getCompaniesByMonth($user, null, $statYear)),
            'requestsPending' => array_sum($this->overviewService->getCompaniesByMonth($user, "pending", $statYear)),
            'requestsRejected' => array_sum($this->overviewService->getCompaniesByMonth($user, "rejected", $statYear)),
        ]));
    }
}
