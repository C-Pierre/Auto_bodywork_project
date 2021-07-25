<?php

namespace App\Controller\Admin;

use App\Entity\Pack;
use App\Form\PackType;
use App\Repository\PackRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/admin/pack", name="admin_pack_")
     */
class AdminPackController extends AbstractController
{
    /**
     * @Route("/new", name="new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $pack = new Pack();
        $form = $this->createForm(PackType::class, $pack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($pack);
            $entityManager->flush();

            return $this->redirectToRoute('admin_pack_all', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pack/new.html.twig', [
            'pack' => $pack,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit", methods={"GET","POST"})
     * @ParamConverter("pack", class="App\Entity\Pack", options={"mapping": {"id": "id"}})
     * @var \App\Entity\Pack $pack
     * @return Response
     */
    public function edit(Request $request, Pack $pack): Response
    {
        $form = $this->createForm(PackType::class, $pack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_pack_all', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('pack/edit.html.twig', [
            'pack' => $pack,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="delete")
     * @ParamConverter("pack", class="App\Entity\Pack", options={"mapping": {"id": "id"}})
     * @var \App\Entity\Pack $pack
     * @return Response
     */
    public function delete(Request $request, Pack $pack): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($pack);
            $entityManager->flush();

        return $this->redirectToRoute('admin_pack', [], Response::HTTP_SEE_OTHER);
    }
}
