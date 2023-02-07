<?php

namespace App\Controller;

use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityController extends AbstractController
{
    public function __construct(private Environment $twig, private UserService $userService)
    {
    }


    #[Route(path: '/connectez-vous', name: 'admin.login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('admin.index');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return new Response($this->twig->render(
            './admins/security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error' => $error
            ]
        ));
    }

    #[Route('/deconnecction', name: 'app_logout')]
    public function logout()
    {
    }
}
