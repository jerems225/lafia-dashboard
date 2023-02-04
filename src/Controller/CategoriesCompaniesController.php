<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class CategoriesCompaniesController extends AbstractController
{
    public function __construct(private Environment $twig)
    {
        
    }

    #[Route('/categories-entreprises', name:'admin.categories-companies')]
    public function ListAndAddCategory() : Response
    {
        $logger = $this->getUser();
        if(!$logger)
        {
            return $this->redirectToRoute('admin.login');
        }

        return new Response($this->twig->render('./companies/categories/add.html.twig'));
    }
}