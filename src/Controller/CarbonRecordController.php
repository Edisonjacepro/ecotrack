<?php

namespace App\Controller;

use App\Entity\CarbonRecord;
use App\Entity\User;
use App\Repository\CarbonRecordRepository;
use App\Service\CarbonCalculatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CarbonRecordController extends AbstractController
{
    #[Route('/records', name: 'app_record_index')]
    public function index(CarbonRecordRepository $carbonRecordRepository): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('carbon_record/index.html.twig', [
            'records' => $carbonRecordRepository->findForUser($user),
        ]);
    }

    #[Route('/records/new', name: 'app_record_new')]
    public function new(Request $request, CarbonCalculatorService $calculator, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('carbon_record', (string) $request->request->get('_csrf_token'))) {
                $this->addFlash('error', 'Jeton CSRF invalide.');

                return $this->redirectToRoute('app_record_new');
            }

            $category = (string) $request->request->get('category', '');
            $data = [
                'distance_km' => $request->request->get('distance_km'),
                'mode' => $request->request->get('mode'),
                'kwh' => $request->request->get('kwh'),
                'energy_type' => $request->request->get('energy_type'),
                'meals' => $request->request->get('meals'),
                'meal_type' => $request->request->get('meal_type'),
                'hours' => $request->request->get('hours'),
                'activity' => $request->request->get('activity'),
            ];

            try {
                $amount = $calculator->calculate($category, $data);
            } catch (\InvalidArgumentException $exception) {
                $this->addFlash('error', $exception->getMessage());

                return $this->redirectToRoute('app_record_new');
            }

            $record = new CarbonRecord();
            $record->setCategory($category);
            $record->setAmountKg($amount);
            $record->setSourceData($data);
            $record->setNotes($request->request->get('notes'));
            $record->setUser($user);
            $recordedAt = $this->parseDate((string) $request->request->get('recorded_at'));
            if ($recordedAt) {
                $record->setRecordedAt($recordedAt);
            }

            $entityManager->persist($record);
            $entityManager->flush();

            return $this->redirectToRoute('app_record_index');
        }

        return $this->render('carbon_record/new.html.twig');
    }

    #[Route('/records/{id}', name: 'app_record_show', methods: ['GET'])]
    public function show(CarbonRecord $record): Response
    {
        $this->denyAccessUnlessGranted('VIEW', $record);

        return $this->render('carbon_record/show.html.twig', [
            'record' => $record,
        ]);
    }

    #[Route('/records/{id}/edit', name: 'app_record_edit')]
    public function edit(CarbonRecord $record, Request $request, CarbonCalculatorService $calculator, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $record);

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('carbon_record_edit_'.$record->getId(), (string) $request->request->get('_csrf_token'))) {
                $this->addFlash('error', 'Jeton CSRF invalide.');

                return $this->redirectToRoute('app_record_edit', ['id' => $record->getId()]);
            }

            $category = (string) $request->request->get('category', $record->getCategory());
            $data = [
                'distance_km' => $request->request->get('distance_km'),
                'mode' => $request->request->get('mode'),
                'kwh' => $request->request->get('kwh'),
                'energy_type' => $request->request->get('energy_type'),
                'meals' => $request->request->get('meals'),
                'meal_type' => $request->request->get('meal_type'),
                'hours' => $request->request->get('hours'),
                'activity' => $request->request->get('activity'),
            ];

            try {
                $amount = $calculator->calculate($category, $data);
            } catch (\InvalidArgumentException $exception) {
                $this->addFlash('error', $exception->getMessage());

                return $this->redirectToRoute('app_record_edit', ['id' => $record->getId()]);
            }

            $record->setCategory($category);
            $record->setAmountKg($amount);
            $record->setSourceData($data);
            $record->setNotes($request->request->get('notes'));
            $recordedAt = $this->parseDate((string) $request->request->get('recorded_at'));
            if ($recordedAt) {
                $record->setRecordedAt($recordedAt);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_record_index');
        }

        return $this->render('carbon_record/edit.html.twig', [
            'record' => $record,
        ]);
    }

    #[Route('/records/{id}/delete', name: 'app_record_delete', methods: ['POST'])]
    public function delete(CarbonRecord $record, Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $record);

        if ($this->isCsrfTokenValid('carbon_record_delete_'.$record->getId(), (string) $request->request->get('_csrf_token'))) {
            $entityManager->remove($record);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_record_index');
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