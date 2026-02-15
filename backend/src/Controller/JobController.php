<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\User;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/inspector/jobs', name: 'api_jobs_')]
class JobController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JobRepository $jobRepository
    ) {
    }

    #[Route('/available', name: 'available', methods: ['GET'])]
    public function getAvailable(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Only show jobs for the inspector's location
        $jobs = $this->jobRepository->findAvailable($user->getLocation());

        return $this->json([
            'success' => true,
            'jobs' => array_map(fn($job) => $this->serializeJob($job), $jobs)
        ]);
    }

    #[Route('/my-jobs', name: 'my_jobs', methods: ['GET'])]
    public function getMyJobs(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $jobs = $this->jobRepository->findAssignedToInspector($user);

        return $this->json([
            'success' => true,
            'jobs' => array_map(fn($job) => $this->serializeJob($job), $jobs)
        ]);
    }

    #[Route('/my-jobs/pending', name: 'my_jobs_pending', methods: ['GET'])]
    public function getMyPendingJobs(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $jobs = $this->jobRepository->findPendingForInspector($user);

        return $this->json([
            'success' => true,
            'jobs' => array_map(fn($job) => $this->serializeJob($job), $jobs)
        ]);
    }

    #[Route('/my-jobs/completed', name: 'my_jobs_completed', methods: ['GET'])]
    public function getMyCompletedJobs(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $jobs = $this->jobRepository->findCompletedByInspector($user);

        return $this->json([
            'success' => true,
            'jobs' => array_map(fn($job) => $this->serializeJob($job), $jobs)
        ]);
    }

    #[Route('/{id}/assign', name: 'assign', methods: ['POST'])]
    public function assignJob(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $job = $this->jobRepository->find($id);
        
        if (!$job) {
            return $this->json([
                'success' => false,
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if (!$job->isAvailable()) {
            return $this->json([
                'success' => false,
                'message' => 'Job is not available for assignment'
            ], Response::HTTP_CONFLICT);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['scheduledDate'])) {
            return $this->json([
                'success' => false,
                'message' => 'scheduledDate is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $scheduledDate = new \DateTimeImmutable($data['scheduledDate']);
            $job->assignTo($user, $scheduledDate);
            
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Job assigned successfully',
                'job' => $this->serializeJob($job)
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/complete', name: 'complete', methods: ['POST'])]
    public function completeJob(int $id, Request $request): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $job = $this->jobRepository->find($id);
        
        if (!$job) {
            return $this->json([
                'success' => false,
                'message' => 'Job not found'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($job->getAssignedTo() !== $user) {
            return $this->json([
                'success' => false,
                'message' => 'You can only complete jobs assigned to you'
            ], Response::HTTP_FORBIDDEN);
        }

        if (!$job->isAssigned()) {
            return $this->json([
                'success' => false,
                'message' => 'Job is not in assigned status'
            ], Response::HTTP_CONFLICT);
        }

        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['assessment']) || empty($data['assessment'])) {
            return $this->json([
                'success' => false,
                'message' => 'assessment is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $job->complete($data['assessment']);
            
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Job completed successfully',
                'job' => $this->serializeJob($job)
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    private function serializeJob(Job $job): array
    {
        $data = [
            'id' => $job->getId(),
            'title' => $job->getTitle(),
            'description' => $job->getDescription(),
            'status' => $job->getStatus(),
            'location' => $job->getLocation()?->getCode(),
            'createdAt' => $job->getCreatedAt()?->format('Y-m-d H:i:s'),
            'updatedAt' => $job->getUpdatedAt()?->format('Y-m-d H:i:s'),
            'scheduledDate' => $job->getScheduledDate()?->format('Y-m-d H:i:s'),
            'completedAt' => $job->getCompletedAt()?->format('Y-m-d H:i:s'),
            'assessment' => $job->getAssessment(),
        ];

        if ($job->getAssignedTo()) {
            $inspector = $job->getAssignedTo();
            $data['assignedTo'] = [
                'id' => $inspector->getId(),
                'email' => $inspector->getEmail(),
                'firstName' => $inspector->getFirstName(),
                'lastName' => $inspector->getLastName(),
                'location' => $inspector->getLocation()?->getCode(),
            ];
        }

        return $data;
    }
}
