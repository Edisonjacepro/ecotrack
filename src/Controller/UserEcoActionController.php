<?php

namespace App\Controller;

use App\Entity\EcoAction;
use App\Entity\User;
use App\Entity\UserEcoAction;
use App\Repository\EcoActionRepository;
use App\Repository\UserEcoActionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class UserEcoActionController extends AbstractController
{
    #[Route('/actions', name: 'app_user_actions')]
    public function index(UserEcoActionRepository $userEcoActionRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user_action/index.html.twig', [
            'actions' => $userEcoActionRepository->findForUser($user),
        ]);
    }

    #[Route('/actions/new', name: 'app_user_action_new')]
    public function new(Request $request, EcoActionRepository $ecoActionRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('user_action_new', (string) $request->request->get('_csrf_token'))) {
                $this->addFlash('error', 'Jeton CSRF invalide.');
                return $this->redirectToRoute('app_user_action_new');
            }

            $ecoActionId = (int) $request->request->get('eco_action');
            $ecoAction = $ecoActionRepository->find($ecoActionId);
            if (!$ecoAction instanceof EcoAction) {
                $this->addFlash('error', 'Action invalide.');
                return $this->redirectToRoute('app_user_action_new');
            }

            $userAction = new UserEcoAction();
            $userAction->setUser($user);
            $userAction->setEcoAction($ecoAction);
            $userAction->setStatus((string) $request->request->get('status', 'planned'));
            $userAction->setNotes($request->request->get('notes'));
            $userAction->setStartedAt($this->parseDate($request->request->get('started_at')));
            $userAction->setCompletedAt($this->parseDate($request->request->get('completed_at')));

            $entityManager->persist($userAction);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_actions');
        }

        return $this->render('user_action/new.html.twig', [
            'eco_actions' => $ecoActionRepository->findBy(['active' => true], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/actions/{id}', name: 'app_user_action_show', methods: ['GET'])]
    public function show(UserEcoAction $userEcoAction): Response
    {
        $this->denyAccessUnlessGranted('VIEW', $userEcoAction);

        return $this->render('user_action/show.html.twig', [
            'action' => $userEcoAction,
        ]);
    }

    #[Route('/actions/{id}/edit', name: 'app_user_action_edit')]
    public function edit(UserEcoAction $userEcoAction, Request $request, EcoActionRepository $ecoActionRepository, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $userEcoAction);

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('user_action_edit_'.$userEcoAction->getId(), (string) $request->request->get('_csrf_token'))) {
                $this->addFlash('error', 'Jeton CSRF invalide.');
                return $this->redirectToRoute('app_user_action_edit', ['id' => $userEcoAction->getId()]);
            }

            $ecoActionId = (int) $request->request->get('eco_action');
            $ecoAction = $ecoActionRepository->find($ecoActionId);
            if ($ecoAction instanceof EcoAction) {
                $userEcoAction->setEcoAction($ecoAction);
            }

            $userEcoAction->setStatus((string) $request->request->get('status', $userEcoAction->getStatus()));
            $userEcoAction->setNotes($request->request->get('notes'));
            $userEcoAction->setStartedAt($this->parseDate($request->request->get('started_at')));
            $userEcoAction->setCompletedAt($this->parseDate($request->request->get('completed_at')));

            $entityManager->flush();

            return $this->redirectToRoute('app_user_actions');
        }

        return $this->render('user_action/edit.html.twig', [
            'action' => $userEcoAction,
            'eco_actions' => $ecoActionRepository->findBy(['active' => true], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/actions/{id}/delete', name: 'app_user_action_delete', methods: ['POST'])]
    public function delete(UserEcoAction $userEcoAction, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $userEcoAction);

        if ($this->isCsrfTokenValid('user_action_delete_'.$userEcoAction->getId(), (string) $request->request->get('_csrf_token'))) {
            $entityManager->remove($userEcoAction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_actions');
    }

    #[Route('/actions/add/{id}', name: 'app_action_add', methods: ['POST'])]
    public function add(EcoAction $ecoAction, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if (!$this->isCsrfTokenValid('add_action_'.$ecoAction->getId(), (string) $request->request->get('_csrf_token'))) {
            $this->addFlash('error', 'Jeton CSRF invalide.');

            return $this->redirectToRoute('app_dashboard');
        }

        $userAction = new UserEcoAction();
        $userAction->setUser($user);
        $userAction->setEcoAction($ecoAction);
        $userAction->setStatus('planned');
        $userAction->setStartedAt(new \DateTimeImmutable());
        $userAction->setNotes($ecoAction->getDescription());

        $entityManager->persist($userAction);
        $entityManager->flush();

        $this->addFlash('success', 'Action ajoutee a votre suivi.');

        return $this->redirectToRoute('app_dashboard');
    }

    private function parseDate(?string $value): ?\DateTimeImmutable
    {
        if (!$value) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date ?: null;
    }
}
