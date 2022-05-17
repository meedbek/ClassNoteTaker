<?php

namespace App\Controller;

use App\Entity\Notes;
use App\Form\NotesType;
use App\Repository\NotesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Modules;

date_default_timezone_set('Asia/Kolkata');

#[Route('/notes')]
class NotesController extends AbstractController
{
    #[Route('/', name: 'app_notes_index', methods: ['GET'])]
    public function index(NotesRepository $notesRepository): Response
    {
        return $this->render('notes/index.html.twig', [
            'notes' => $notesRepository->findAll(),
        ]);
    }

    #[Route('/module/{id}', name: 'app_notes_module', methods: ['GET'])]
    public function notes(Modules $module): Response
    {
        return $this->render('notes/index.html.twig', [
            'notes' => $module->getNotes(),
            'module' => $module
        ]);
    }

    #[Route('/new/{id}', name: 'app_notes_new', methods: ['GET', 'POST'])]
    public function new(Modules $module,Request $request, NotesRepository $notesRepository): Response
    {
        $note = new Notes();
        $form = $this->createForm(NotesType::class, $note); 
        $note->setModule($module);
        $note->setCreatedAt(new \DateTime('now'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $notesRepository->add($note, true);

            return $this->redirectToRoute('app_notes_module', ['id' => $module->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notes/new.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_notes_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Notes $note, NotesRepository $notesRepository): Response
    {
        $form = $this->createForm(NotesType::class, $note);
        $form->handleRequest($request);
        $note->setUpdatedAt(new \DateTime('now'));

        if ($form->isSubmitted() && $form->isValid()) {
            $notesRepository->add($note, true);

            return $this->redirectToRoute('app_notes_module', ['id' => $note->getModule()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('notes/edit.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_notes_delete', methods: ['POST'])]
    public function delete(Request $request, Notes $note, NotesRepository $notesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $notesRepository->remove($note, true);
        }

        return $this->redirectToRoute('app_notes_module', ['id' => $note->getModule()->getId()], Response::HTTP_SEE_OTHER);
    }
}
