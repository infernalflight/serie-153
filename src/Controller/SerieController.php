<?php

namespace App\Controller;

use App\Entity\Serie;
use App\Form\SerieType;
use App\Helper\FileUploader;
use App\Repository\SerieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/serie', name: 'serie')]
#[IsGranted('ROLE_USER')]
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
        //$series = $serieRepository->findSeriesByGenre('Drama', $status);

        $series = $serieRepository->getAllSeriesWithSeasons();

        // Requête custom avec paramètres de critères en DQL
       // $series = $serieRepository->findSeriesByGenreWithDql('Drama', $status);

        // Requête custom avec paramètres de critères en SQL brut
       // $series = $serieRepository->findSeriesByGenreWithRawSql("Drama", $status);

        return $this->render('serie/list.html.twig', [
            'series' => $series
        ]);
    }

    #[Route('/catalogue/{page}', name:'_catalogue', requirements: ['page' => '\d+'], defaults: ['page' => 1])]
    public function listByPage(int $page, SerieRepository $serieRepository, ParameterBagInterface $parameterBag): Response
    {

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $this->getUser()->getRoles();

        $nbSeries = $parameterBag->get('nb_series_by_page');
        $offset = ($page - 1) * $nbSeries;

        $series = $serieRepository->findBy([], ['vote' => 'DESC'], $nbSeries, $offset,  );
        $total = $serieRepository->count();

        $nbTotalPages = ceil($total / $nbSeries);

        return $this->render('serie/list.html.twig', [
            'series' => $series,
            'nbTotal' => $nbTotalPages,
            'page' => $page
        ]);

    }



    #[Route('/detail/{id}', name: '_detail', requirements: ['id' => '\d+'])]
    public function detail(Serie $serie, Request $request): Response
    {

     if ($request->get('partial')) {
         return $this->render('serie/_detail_content.html.twig', [
             'serie' => $serie,
         ]);
     }

        return $this->render('serie/detail.html.twig', [
            'serie' => $serie,
        ]);
    }

    #[Route('/create', name: '_create')]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request, EntityManagerInterface $em, FileUploader $fileUploader): Response
    {
        $serie = new Serie();
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('poster_file')->getData();

            if ($file instanceof UploadedFile) {
                $name = $fileUploader->upload($file, $serie->getName());
                $serie->setPoster($name);
            }

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
    public function update(Request $request, EntityManagerInterface $em, Serie $serie, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(SerieType::class, $serie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('poster_file')->getData();

            if ($file instanceof UploadedFile) {
                $name = $fileUploader->upload($file, $serie->getName());
                $serie->setPoster($name);
            }
            $em->flush();

            $this->addFlash('success', 'La série a été modifiée avec succès !');
            return $this->redirectToRoute('serie_list');
        }

        return $this->render('serie/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: '_delete', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(Serie $serie, EntityManagerInterface $em, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$serie->getId(), $request->get('token'))) {
            $em->remove($serie);
            $em->flush();

            $this->addFlash('success', 'Une série a été supprimée');
        } else {
            $this->addFlash('danger', 'Pas possible de supprimer! ');
        }
        return $this->redirectToRoute('serie_list');

    }


}
