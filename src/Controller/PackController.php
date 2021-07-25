<?php

namespace App\Controller;

use App\Repository\PackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PackController extends AbstractController
{
    /**
     * @Route("/pack", name="pack", methods={"GET"})
     */
    public function index(PackRepository $packRepository): Response
    {
        return $this->render('pack/index.html.twig', [
            'packs' => $packRepository->findAll(),
        ]);
    }
}
