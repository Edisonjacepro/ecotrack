<?php

namespace App\Controller;

use App\Entity\CarbonRecord;
use App\Entity\User;
use App\Service\CarbonCalculatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CarbonRecordController extends AbstractController
{
    #[Route('/records/new', name: 'app_record_new')]
    #[IsGranted('ROLE_USER')]
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

            $entityManager->persist($record);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('carbon_record/new.html.twig');
    }
}
