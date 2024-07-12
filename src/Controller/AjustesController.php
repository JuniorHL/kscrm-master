<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AjustesController extends AbstractController
{
    #[Route('/', name: 'app_ajustes_reencaminar')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_crm_dashboard_mostrar');
    }
}
