<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/serie', name: 'serie')]
class SerieController extends AbstractController
{
    #[Route('/test', name: '_test')]
    public function test(EntityManagerInterface $em): Response
    {

        $serie = new Serie();
        $serie->setName('Smallville')
            ->setStatus('ENDED')
            ->setDateCreated(new \DateTime())
            ->setFirstAirDate(new \DateTime('2001-10-16'));

        $em->persist($serie);
        $em->flush();

        return new Response("Nouvelle série crée avec succes");
    }

    #[Route('/list/{status}', name: '_list')]
    public function list(SerieRepository $serieRepository, ?string $status = null): Response
    {

        // Requete hérité "findAll()"
//      $series = $serieRepository->findAll();

        if ($status && !\in_array($status, ['returning', 'ended', 'canceled'])) {
            throw $this->createNotFoundException('Status non valide');
        }

        $criterias = ['genres' => 'Drama'];

        if ($status) {
            $criterias['status'] = $status;
        }

        // Requête hérité "findBy() avec critères et orderby"
        /**
        $series = $serieRepository->findBy(
            $criterias,
            ['firstAirDate' => 'DESC']
        );
**/

        // Requête custom avec paramètres de critères
        $series = $serieRepository->findSeriesByGenre('Drama', $status);

        // Requête custom avec paramètres de critères en DQL
       // $series = $serieRepository->findSeriesByGenreWithDql('Drama', $status);

        // Requête custom avec paramètres de critères en SQL brut
       // $series = $serieRepository->findSeriesByGenreWithRawSql("Drama", $status);

        return $this->render('serie/list.html.twig', [
            'series' => $series
        ]);
    }

    #[Route('/detail/{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function detail(Serie $serie): Response
    {
        return $this->render('serie/detail.html.twig', [
            'serie' => $serie,
        ]);
    }

    #[Route('/create', name: '_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($serie);
            $em->flush();

            $this->addFlash('success', 'Une nouvelle série a été crée avec succès !');
            return $this->redirectToRoute('serie_list');
        }


        return $this->render('serie/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/update/{id}', name: '_update', requirements: ['id' => '\d+'])]
    public function update(Request $request, EntityManagerInterface $em, Serie $serie): Response
    {
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em->flush();

            $this->addFlash('success', 'La série a été modifiée avec succès !');
            return $this->redirectToRoute('serie_list');
        }

        return $this->render('serie/edit.html.twig', [
            'form' => $form,
        ]);
    }


}
