<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UniversityRepository;
use App\Repository\DegreeRepository;
use Symfony\Component\HttpFoundation\Request;

class UniversitiesController extends AbstractController
{

    private $universityRepository;
    private $degreeRepository;

    public function __construct(
        UniversityRepository $universityRepository,
        DegreeRepository $degreeRepository,
        )
    {
        $this->universityRepository = $universityRepository;
        $this->degreeRepository = $degreeRepository;
    }
    
    #[Route('/universities', name: 'app_universities')]
    public function index(Request $request): Response
    {
        
        $session = $request->getSession();
        // $referer = $request->headers->get('referer');
        $referer = $request->getUri();
        $session->set('referer', $referer);

        $acceptedUniversities = $this->universityRepository->findBy(['accepted' => true]);

        return $this->render('universities.html.twig', [
            'universities' => $acceptedUniversities,
        ]);
    }

    #[Route('/u/{universitySlug}', name: 'app_university')]
    public function show(string $universitySlug, Request $request): Response
    {

        $session = $request->getSession();
        // $referer = $request->headers->get('referer');
        $referer = $request->getUri();
        $session->set('referer', $referer);

        $university = $this->universityRepository->findOneBySlug($universitySlug);

        $acceptedDegrees = $this->degreeRepository->findBy(['university' => $university, 'accepted' => true]);

        return $this->render('show_university.html.twig', [
            'university' => $university,
            'degrees' => $acceptedDegrees,
        ]);
    }
}