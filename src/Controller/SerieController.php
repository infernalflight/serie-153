<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function detail(SerieRepository $serieRepository, int $id): Response
    {
        $serie = $serieRepository->find($id);

        if (!$serie) {
            throw $this->createNotFoundException();
        }

        return $this->render('serie/detail.html.twig', [
            'serie' => $serie,
        ]);
    }


}
