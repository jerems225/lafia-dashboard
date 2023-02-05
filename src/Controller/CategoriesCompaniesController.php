<?php

namespace App\Controller;

use App\Form\CategoryType;
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
    ) {
    }

    #[Route('/categories-entreprises', name: 'admin.categories-companies', methods: ['GET', 'POST'])]
    public function ListAndAddCategory(Request $request): Response
    {
        $logger = $this->getUser();
        if (!$logger) {
            return $this->redirectToRoute('admin.login');
        }
        //find user informations
        $user = $this->userService->findUser('email', $logger->getUserIdentifier());

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
        }
        else
        {
            $categories = $result['categories'];
        }

        return new Response($this->twig->render('./admins/companies/categories/add.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]));
    }
}
