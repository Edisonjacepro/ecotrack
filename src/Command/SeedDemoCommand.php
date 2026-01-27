<?php

namespace App\Command;

use App\Entity\EcoAction;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-demo',
    description: 'Seed demo user and eco actions.'
)]
class SeedDemoCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $userRepo = $this->entityManager->getRepository(User::class);
        $actionRepo = $this->entityManager->getRepository(EcoAction::class);

        $user = $userRepo->findOneBy(['email' => 'demo@ecotrack.local']);
        if (!$user) {
            $user = new User();
            $user->setEmail('demo@ecotrack.local');
            $user->setFullName('Demo EcoTrack');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'DemoPass123!'));
            $this->entityManager->persist($user);
        }

        if ($actionRepo->count([]) === 0) {
            $actions = [
                ['Transport', 'Covoiturage deux fois par semaine', 'transport', 12.5],
                ['Energie', 'Baisser le chauffage de 1 degre', 'energy', 8.0],
                ['Alimentation', 'Planifier trois repas vegetarien', 'food', 6.5],
                ['Numerique', 'Limiter le streaming HD', 'digital', 3.2],
            ];

            foreach ($actions as [$title, $description, $category, $saving]) {
                $action = new EcoAction();
                $action->setTitle($title);
                $action->setDescription($description);
                $action->setCategory($category);
                $action->setEstimatedSavingKg((float) $saving);
                $action->setActive(true);
                $this->entityManager->persist($action);
            }
        }

        $this->entityManager->flush();

        $output->writeln('Demo seed ready: demo@ecotrack.local / DemoPass123!');

        return Command::SUCCESS;
    }
}