<?php

namespace App\Controller;

use App\Entity\Conference;
use App\Repository\CommentRepository;
use App\Repository\ConferenceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConferenceController extends AbstractController
{
    /**
     * This is the index action method of a controller.
     * It renders the 'conference/index.html.twig' template and passes the list of all conferences to it.
     *
     * @param ConferenceRepository $conferenceRepository The repository for fetching conference data
     *
     * @return Response The rendered template as a Response object
     */
    #[Route('/', name: 'homepage')]
    public function index(ConferenceRepository $conferenceRepository): Response
    {
        return $this->render('conference/index.html.twig');
    }

    /**
     * This controller action is responsible for rendering the conference details page.
     *
     * @param Request $request The current HTTP request
     * @param Conference $conference The conference entity to be displayed
     * @param CommentRepository $commentRepository The repository for fetching comments
     *
     * @return Response The HTTP response containing the rendered template
     */
    #[Route('/conference/{slug}', name: 'conference')]
    public function show(
        Request $request,
        Conference $conference,
        CommentRepository $commentRepository,
        ConferenceRepository $conferenceRepository
    ): Response {
        $offset = max(0, $request->query->getInt('offset', 0));
        $paginator = $commentRepository->getCommentPaginator($conference, $offset);

        return $this->render('conference/show.html.twig', [
            'conference' => $conference,
            'comments' => $paginator,
            'previous' => $offset - CommentRepository::COMMENTS_PER_PAGE,
            'next' => min(count($paginator), $offset + CommentRepository::COMMENTS_PER_PAGE),
        ]);
    }
}
