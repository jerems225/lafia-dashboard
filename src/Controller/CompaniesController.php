<?php

namespace App\Controller;

use App\Entity\User;
use App\Services\AuthService;
use App\Services\Companies\CategorieService;
use App\Services\Companies\CompaniesService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class CompaniesController extends AbstractController
{
    public function __construct(
        private Environment $twig,
        private UserService $userService,
        private CompaniesService $companiesService,
        private CategorieService $categorieService,
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

    #[Route('/companies', name: 'admin.companies')]
    public function listCompanies(): Response
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

        $result = $this->companiesService->getCompanies($user, "pending");
        if ($result['status'] == false) {
            $companies = [];
            $this->addFlash('companies-error', $result['message']);
        } else {
            $companies = $result['companies'];
        }



        return new Response($this->twig->render('./admins/companies/list.html.twig', [
            'companies' => $companies
        ]));
    }

    #[Route('/companies/{uuid}', name: 'admin.companies.show')]
    public function showCompany(Request $request, $uuid): Response
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

        //get Company, Owner and User
        $result = $this->companiesService->getCompany($user, $uuid);
        if ($result['status'] == false) {
            $company = null;
            $owner = null;
            $userOwner = null;
            $this->addFlash('companies-error', $result['message']);
        } else {
            $company = $result['company'];
            $owner = $result['owner'];
            $userOwner = $result['user'];
        }

        if ($company) {
            //get category
            $categoryResult = $this->categorieService->getCategory($user, $company['categoryCompanyId']);
            if ($categoryResult['status'] == false) {
                $category = null;
                $this->addFlash('companies-error', $categoryResult['message']);
            } else {
                $category = $categoryResult['category'];
            }
        } else {
            $category = null;
        }

        return new Response($this->twig->render('./admins/companies/show.html.twig', [
            'company' => $company,
            'owner' => $owner,
            'user' => $userOwner,
            'category' => $category
        ]));
    }

    #[Route('/companies/modification/{uuid}/{status}', name: 'admin.companies.status')]
    public function setCompanyStatus(Request $request, $uuid, $status): Response
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
        $result = $this->companiesService->updateStatus($user, $uuid, $status);

        if ($result['status'] == false) {
            $this->addFlash('companies-error', $result['message']);
        } else {
            $this->addFlash('edit-company', $result['message']);
        }

        return $this->redirectToRoute('admin.companies.show', [
            'uuid' => $uuid
        ]);
    }
}
