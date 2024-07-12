<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/acceder', name: 'app_acceder')]
    public function acceder(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/acceder.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
			'page_title' => 'KSCRM Acceso',
			'csrf_token_intention' => 'authenticate',
			'target_path' => $this->generateUrl('app_crm_dashboard_mostrar'),
		]);
    }

    #[Route(path: '/desconectarse', name: 'app_desconectarse')]
    public function desconectarse(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
