<?php

namespace App\Command;

use App\Entity\Job;
use App\Entity\Location;
use App\Entity\User;
use App\Repository\LocationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:seed-database',
    description: 'Seeds the database with initial users and jobs',
)]
class SeedDatabaseCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private LocationRepository $locationRepository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Seed locations first
        $this->seedLocations($io);

        // Seed locations first
        $this->seedLocations($io);

        // Check if admin already exists
        $existingAdmin = $this->entityManager->getRepository(User::class)
            ->findOneBy(['email' => 'admin@example.com']);

        if ($existingAdmin) {
            $io->warning('Admin user already exists. Skipping user seed.');
            return Command::SUCCESS;
        }

        // Get locations
        $locationUk = $this->locationRepository->findByCode('UK');
        $locationMexico = $this->locationRepository->findByCode('Mexico');
        $locationIndia = $this->locationRepository->findByCode('India');

        // Create Admin User
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setFirstName('Admin');
        $admin->setLastName('User');
        $admin->setLocation($locationUk);
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));

        $this->entityManager->persist($admin);

        // Create Inspector Users
        $inspector1 = new User();
        $inspector1->setEmail('inspector.uk@example.com');
        $inspector1->setFirstName('James');
        $inspector1->setLastName('Smith');
        $inspector1->setLocation($locationUk);
        $inspector1->setRoles(['ROLE_INSPECTOR']);
        $inspector1->setPassword($this->passwordHasher->hashPassword($inspector1, 'password123'));

        $this->entityManager->persist($inspector1);

        $inspector2 = new User();
        $inspector2->setEmail('inspector.mexico@example.com');
        $inspector2->setFirstName('Carlos');
        $inspector2->setLastName('Rodriguez');
        $inspector2->setLocation($locationMexico);
        $inspector2->setRoles(['ROLE_INSPECTOR']);
        $inspector2->setPassword($this->passwordHasher->hashPassword($inspector2, 'password123'));

        $this->entityManager->persist($inspector2);

        $inspector3 = new User();
        $inspector3->setEmail('inspector.india@example.com');
        $inspector3->setFirstName('Raj');
        $inspector3->setLastName('Patel');
        $inspector3->setLocation($locationIndia);
        $inspector3->setRoles(['ROLE_INSPECTOR']);
        $inspector3->setPassword($this->passwordHasher->hashPassword($inspector3, 'password123'));

        $this->entityManager->persist($inspector3);

        $this->entityManager->flush();

        // Create sample jobs for each location
        $this->seedJobs($io);

        $io->success('Database seeded successfully!');
        $io->table(
            ['Email', 'Password', 'Role', 'Location'],
            [
                ['admin@example.com', 'admin123', 'ROLE_ADMIN', 'UK'],
                ['inspector.uk@example.com', 'password123', 'ROLE_INSPECTOR', 'UK'],
                ['inspector.mexico@example.com', 'password123', 'ROLE_INSPECTOR', 'Mexico'],
                ['inspector.india@example.com', 'password123', 'ROLE_INSPECTOR', 'India'],
            ]
        );

        return Command::SUCCESS;
    }

    private function seedLocations(SymfonyStyle $io): void
    {
        $locations = [
            ['name' => 'United Kingdom', 'code' => 'UK', 'timezone' => 'Europe/London', 'countryCode' => 'GB'],
            ['name' => 'Mexico', 'code' => 'Mexico', 'timezone' => 'America/Mexico_City', 'countryCode' => 'MX'],
            ['name' => 'India', 'code' => 'India', 'timezone' => 'Asia/Kolkata', 'countryCode' => 'IN'],
        ];

        foreach ($locations as $locationData) {
            $existing = $this->locationRepository->findByCode($locationData['code']);
            if ($existing) {
                continue;
            }

            $location = new Location();
            $location->setName($locationData['name']);
            $location->setCode($locationData['code']);
            $location->setTimezone($locationData['timezone']);
            $location->setCountryCode($locationData['countryCode']);
            $this->entityManager->persist($location);
        }

        $this->entityManager->flush();
        $io->note('Locations seeded');
    }

    private function seedJobs(SymfonyStyle $io): void
    {
        // Get locations
        $locationUk = $this->locationRepository->findByCode('UK');
        $locationMexico = $this->locationRepository->findByCode('Mexico');
        $locationIndia = $this->locationRepository->findByCode('India');
        // UK Jobs
        $ukJobs = [
            [
                'title' => 'Safety Inspection - London Office',
                'description' => 'Annual safety inspection of electrical systems and fire exits',
                'location' => $locationUk,
            ],
            [
                'title' => 'Equipment Maintenance Check - Manchester',
                'description' => 'Quarterly maintenance inspection of manufacturing equipment',
                'location' => $locationUk,
            ],
            [
                'title' => 'Building Compliance Audit - Birmingham',
                'description' => 'Full building compliance audit for health and safety regulations',
                'location' => $locationUk,
            ],
        ];

        // Mexico Jobs
        $mexicoJobs = [
            [
                'title' => 'Factory Safety Inspection - Mexico City',
                'description' => 'Comprehensive safety inspection of production facilities',
                'location' => $locationMexico,
            ],
            [
                'title' => 'Fire Safety Check - Guadalajara',
                'description' => 'Fire safety equipment and emergency exit inspection',
                'location' => $locationMexico,
            ],
        ];

        // India Jobs
        $indiaJobs = [
            [
                'title' => 'Warehouse Safety Audit - Mumbai',
                'description' => 'Safety audit of warehouse facilities and storage procedures',
                'location' => $locationIndia,
            ],
            [
                'title' => 'IT Infrastructure Check - Bangalore',
                'description' => 'Inspection of server rooms and data center safety measures',
                'location' => $locationIndia,
            ],
            [
                'title' => 'Office Building Inspection - Delhi',
                'description' => 'Routine office building safety and compliance inspection',
                'location' => $locationIndia,
            ],
        ];

        $allJobs = array_merge($ukJobs, $mexicoJobs, $indiaJobs);

        foreach ($allJobs as $jobData) {
            $job = new Job();
            $job->setTitle($jobData['title']);
            $job->setDescription($jobData['description']);
            $job->setLocation($jobData['location']);
            $this->entityManager->persist($job);
        }

        $this->entityManager->flush();

        $io->note(sprintf('Created %d sample jobs across all locations', count($allJobs)));
    }
}
