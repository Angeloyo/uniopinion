<?php

namespace App\Controller;

use App\Repository\OpinionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class EditOpinionController extends AbstractController
{

    private $entityManager;
    private $opinionRepository;
    public function __construct(
        EntityManagerInterface $entityManager,
        OpinionRepository $opinionRepository,
        )
    {
        $this->entityManager = $entityManager;
        $this->opinionRepository = $opinionRepository;
    }

    #[Route('/opinion/edit/{id}', name: 'app_edit_opinion')]
    public function index(int $id, Request $request): Response
    {

        $opinion = $this->opinionRepository->find($id);

        $session = $request->getSession();
        $referer = $session->get('referer');

        if (!$opinion) {
            $this->addFlash('error', 'Opinion no encontrada');
            return $this->redirect($referer);
        }

        // Check if the user has permission to edit the opinion
        $user = $this->getUser();
        if (!$user || $user->getId() !== $opinion->getOwner()->getId()) {
            // throw $this->createAccessDeniedException('No tienes permiso para editar esta opinion.');
            $this->addFlash('error', 'No tienes permiso para editar esta opinion.');
            return $this->redirect($referer);
        }

        $form = $this->createForm(\App\Form\SpecificOpinionFormType::class, $opinion);

        // Get original comment before form handling
        $originalComment = $opinion->getComment();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // if there is no comment we dont need to review it
            if($opinion->getComment() == null){
                $opinion->setAccepted(true);
                $opinion->setReviewed(true);
            }
            else{
                // When comment is edited, it needs to be reviewed again
                if ($originalComment != $opinion->getComment()) {
                    //hacer: avisar al usuario de que el comentario ha sido enviado a revision
                    $opinion->setAccepted(false);
                    $opinion->setReviewed(false);
                }
            }

            $this->entityManager->persist($opinion);
            $this->entityManager->flush();

            if ($referer) {
                return $this->redirect($referer);
            } else {
                return $this->redirectToRoute('app_home');
            }
        }

        return $this->render('edit_opinion/index.html.twig', [
            'form' => $form,
            'object' => $opinion->getSubject() ?? $opinion->getProfessor()
        ]);
    }
}
