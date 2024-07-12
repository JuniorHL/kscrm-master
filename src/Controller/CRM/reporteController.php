<?php

namespace App\Controller\CRM;

use App\Repository\ProyectoRepository;
use Doctrine\DBAL\Driver\Mysqli\Initializer\Options;
use Dompdf\Dompdf;
use Dompdf\Options as DompdfOptions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class reporteController extends AbstractController
{
    private $proyectoRepository;

    public function __construct(ProyectoRepository $proyectoRepository)
    {
        $this->proyectoRepository = $proyectoRepository;
    }
    #[Route('/reporte/proyectos/{id}', name: 'app_reporte_proyectos')]
    public function generateReport(int $id): Response
    {
        $proyecto = $this->proyectoRepository->find($id);

        if (!$proyecto) {
            throw $this->createNotFoundException('No project found for id ' . $id);
        }

        // Configuración de Dompdf
        $pdfOptions = new DompdfOptions();
        $pdfOptions->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($pdfOptions);
        $html = $this->renderView('crm/reporte/reporte.html.twig', [
            'proyecto' => $proyecto,
        ]);

        // Cargar HTML en Dompdf
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Enviar el PDF al navegador
        $nombreproyecto = $proyecto->getPytNombre();
        $output = $dompdf->output();
        return new Response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="Reporte_' . $nombreproyecto . '.pdf"',
        ]);
    }
}
