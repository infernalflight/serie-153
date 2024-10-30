<?php

namespace App\Controller;

use App\Entity\Season;
use App\Form\SeasonType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/season', name: 'season')]
class SeasonController extends AbstractController
{

    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $em, SerieRepository $serieRepository): Response
    {
        $season = new Season();

        $idSerie = $request->get('idSerie');

        if ($idSerie) {
            $serie = $serieRepository->find($idSerie);
            $season->setSerie($serie);
        }

        $form = $this->createForm(SeasonType::class, $season);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($season);
            $em->flush();

            $this->addFlash('success', 'Une nouvelle saison a été crée');
            return $this->redirectToRoute('serie_detail', ['id' => $season->getSerie()->getId()]);
        }


        return $this->render('season/edit.html.twig', [
            'form' => $form,
            'idSerie' => $idSerie ?? null,
        ]);
    }
}
