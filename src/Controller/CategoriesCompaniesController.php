<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CategoryType;
use App\Form\EditCategoryType;
use App\Services\AuthService;
use App\Services\Companies\CategorieService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class CategoriesCompaniesController extends AbstractController
{
    public function __construct(
        private Environment $twig,
        private CategorieService $categorieService,
        private UserService $userService,
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

    #[Route('/categories-entreprises', name: 'admin.categories-companies')]
    public function ListAndAddCategory(Request $request): Response
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

        $form = $this->createForm(CategoryType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category = [
                "name" => $form->get('title')->getData(),
                "description" => $form->get('description')->getData(),
                "image" => $form->get('image')->getData(),
            ];
            $result = $this->categorieService->createCategory($user, $category);
            $result['status'] == false ?  $this->addFlash('categories-companies-error', $result['message']) : $this->addFlash('add-categorie', "Le processus d'ajout d'une nouvelle catégorie a bien été prise en compte !");
            return $this->redirectToRoute('admin.categories-companies');
        }

        $result = $this->categorieService->getCategories($user);
        if ($result['status'] == false) {
            $categories = [];
            $this->addFlash('categories-companies-error', $result['message']);
        } else {
            $categories = $result['categories'];
        }

        return new Response($this->twig->render('./admins/companies/categories/add.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]));
    }

    #[Route('/categories-entreprises/modification/{uuid}', name: 'admin.categories-companies.edit')]
    public function editCategory(Request $request, $uuid): Response
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
        };

        $result = $this->categorieService->getCategory($user, $uuid);
        if ($result['status'] == false) {
            $category = [
                "name" => "",
                "description" => "",
                "image" => ""
            ];
            $this->addFlash('categories-companies-error', $result['message']);
        } else {
            $category = $result['category'];
        }

        $form = $this->createForm(EditCategoryType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $category = [
                "uuid" => $uuid,
                "name" => $form->get('title')->getData(),
                "description" => $form->get('description')->getData(),
                "image" => $form->get('image')->getData()
            ];

            $result = $this->categorieService->updateCategory($user, $category);
            $result['status'] == false ?  $this->addFlash('categories-companies-error', $result['message']) : $this->addFlash('add-categorie', "Le processus de modification de la catégorie " . $category['name'] . " a bien été pris en compte !");
            return $this->redirectToRoute('admin.categories-companies');
        }


        return new Response($this->twig->render('./admins/companies/categories/edit.html.twig', [
            'form' => $form->createView(),
            'category' => $category
        ]));
    }

    #[Route('/categories-entreprises/entreprises/{uuid}', name:'admin.categories-companies.entreprise')]
    public function showCompanies(Request $request, $uuid) : Response
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
        };

        $result = $this->categorieService->getCategory($user, $uuid);
        if ($result['status'] == false) {
            $category = null;
            $companies = [];
            $this->addFlash('companies-error', $result['message']);
        } else {
            $category = $result['category'];
            $companies = $result['companies'];
        }

        return new Response($this->twig->render('./admins/companies/categories/list-companies.html.twig', [
            'category' => $category,
            'companies' => $companies,
        ]));
    }

    #[Route('/categories-entreprises/suppression/{uuid}', name: 'admin.categories-companies.delete')]
    public function deleteCategory($category_uuid): Response
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

        $result = $this->categorieService->deleteCategory($user, $category_uuid);

        $result['status'] == false ?  $this->addFlash('categories-companies-error', $result['message']) : $this->addFlash('add-categorie', $result['message']);

        return $this->redirectToRoute('admin.categories-companies');
    }
}
