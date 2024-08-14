<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;

class ConferenceController extends AbstractController
{
    /**
     * This is the index action method of a controller.
     * It renders the 'conference/index.html.twig' template and passes the list of all conferences to it.
     *
     * @param Environment          $tiwg                 The Twig environment service for rendering templates
     * @param ConferenceRepository $conferenceRepository The repository for fetching conference data
     *
     * @return Response The rendered template as a Response object
     */
    #[Route('/', name: 'homepage')]
    public function index(Environment $tiwg, ConferenceRepository $conferenceRepository): Response
    {
        return new Response($tiwg->render('conference/index.html.twig', [
            'conferences' => $conferenceRepository->findAll(),
        ]));
    }

    /**
     * This is a controller action method that handles the display of a specific conference.
     *
     * @param Environment       $twig              The Twig environment service for rendering templates
     * @param Conference        $conference        The Conference entity object to be displayed
     * @param CommentRepository $commentRepository The repository for fetching comments related to the conference
     *
     * @return Response The rendered response containing the conference details and comments
     */
    #[Route('/conference/{id}', name: 'conference')]
    public function show(
        Environment $twig,
        Conference $conference,
        CommentRepository $commentRepository
    ): Response {
        return new Response(
            $twig->render('conference/show.html.twig', [
                'conference' => $conference,
                'comments' => $commentRepository->findBy(
                    ['conference' => $conference],
                    ['createdAt' => 'DESC'],
                )
            ])
        );
    }
}
