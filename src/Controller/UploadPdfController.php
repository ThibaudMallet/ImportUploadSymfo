<?php

namespace App\Controller;

use App\Service\PdfUploader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UploadPdfController extends AbstractController
{
    #[Route('/upload/pdf', name: 'app_upload_pdf')]
    public function index(Request $request, PdfUploader $pdfUploader): Response
    {
        $file = $request->files->get('file');

        if ($file) {
            $pdfUploader->upload($file);
        }

        return $this->render('upload_pdf/index.html.twig', [

        ]);
    }
}
