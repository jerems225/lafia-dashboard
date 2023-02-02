<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class AdminController extends AbstractController {
    public function __construct(private Environment $twig)
    {
        
    }

    #[Route('/',name:'admin.index')]
    public function AdminIndex() : Response
    {
        $logger = $this->getUser();
        if(!$logger)
        {
            return $this->redirectToRoute('admin.login');
        }

        return new Response($this->twig->render('./admins/index.html.twig'));
    }
}