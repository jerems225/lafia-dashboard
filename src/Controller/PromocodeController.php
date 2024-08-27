<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PromoType;
use App\Services\AuthService;
use App\Services\Orders\PromoCodeService;
use App\Services\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class PromocodeController extends AbstractController
{
    public function __construct(private Environment $twig,
    private UserService $userService,
    private AuthService $authService,
    private PromoCodeService $promoCodeService)
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

    #[Route('/promo-codes', name: 'admin.promo-codes')]
    public function getCodes(Request $request) : Response
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

        $result = $this->promoCodeService->getCodes($user);
        if ($result['status'] == false) {
            $promocodes = [];
            $this->addFlash('promo-error', $result['message']);
        } else {
            $promocodes = $result['promo-codes'];
        }

        $form = $this->createForm(PromoType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $code = $form->get('code_promo')->getData();
            $percent = $form->get('promoPercent')->getData();
            //call add service
            $addResult = $this->promoCodeService->addCodes($user, $code, $percent);
            if ($addResult['status'] == false) {
                $this->addFlash('promo-error', $addResult['message']);
            } else {
                $this->addFlash('promo-success', 'Le processus d\'ajout d\'un nouveau code promo a bien été pris en compte !');
            }

            return $this->redirectToRoute('admin.promo-codes');
        }

        return new Response($this->twig->render('./admins/promo-codes/list.html.twig',[
            'promocodes' => $promocodes,
            'form' => $form->createView()
        ]));
    }

    #[Route('/promo-codes/modification/{uuid}', name: 'admin.promo-codes.update')]
    public function updateCode(Request $request, $uuid) : Response
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

        $result = $this->promoCodeService->getCode($user, $uuid);
        if ($result['status'] == false) {
            $promocode = [];
            $this->addFlash('promo-error', $result['message']);
        } else {
            $promocode = $result['promo-code'];
        }

        $form = $this->createForm(PromoType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $code = $form->get('code_promo')->getData();
            $percent = $form->get('promoPercent')->getData();
            //call add service
            $updateResult = $this->promoCodeService->updateCode($user, $uuid, $code, $percent);
            if ($updateResult['status'] == false) {
                $this->addFlash('promo-error', $updateResult['message']);
            } else {
                $this->addFlash('promo-success', 'Le processus de modification d\'un code promo a bien été pris en compte !');
            }

            return $this->redirectToRoute('admin.promo-codes');
        }

        return new Response($this->twig->render('./admins/promo-codes/modify.html.twig',[
            'promocode' => $promocode,
            'form' => $form->createView()
        ]));
    }

    #[Route('/promo-codes/suppression/{uuid}', name: 'admin.promo-codes.delete')]
    public function deleteCode(Request $request, $uuid) : Response
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

        $result = $this->promoCodeService->deleteCode($user, $uuid);
        if ($result['status'] == false) {
            $this->addFlash('promo-error', $result['message']);
        } else {
            $this->addFlash('promo-success',"Le processus de suppression d\'un code promo a bien été pris en compte !");
        }

        return $this->redirectToRoute('admin.promo-codes');
    }
}