<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\User;
use App\Repository\JobRepository;
use App\Trait\TimezoneAwareTrait;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_user_')]
#[OA\Tag(name: 'User')]
class UserController extends AbstractController
{
    use TimezoneAwareTrait;

    public function __construct(
        private JobRepository $jobRepository
    ) {
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    #[OA\Get(
        path: '/api/me',
        summary: 'Get current authenticated user',
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current user information'
            ),
            new OA\Response(response: 401, description: 'Not authenticated')
        ]
    )]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'location' => $user->getLocation()?->getCode(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'timezone' => $user->getTimezone(),
                'createdAt' => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
                'lastLoginAt' => $user->getLastLoginAt()?->format('Y-m-d H:i:s'),
            ]
        ]);
    }

    #[Route('/me/jobs', name: 'me_jobs', methods: ['GET'])]
    #[OA\Get(
        path: '/api/me/jobs',
        summary: 'Get jobs assigned to current user',
        tags: ['User'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of jobs assigned to authenticated user'
            ),
            new OA\Response(response: 401, description: 'Not authenticated')
        ]
    )]
    public function myJobs(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $jobs = $this->jobRepository->findAssignedToInspector($user);

        return $this->json([
            'success' => true,
            'jobs' => array_map(fn($job) => $this->serializeJob($job, $user), $jobs)
        ]);
    }

    private function serializeJob(Job $job, User $user): array
    {
        $timezone = $user->getTimezone();

        $data = [
            'id' => $job->getId(),
            'title' => $job->getTitle(),
            'description' => $job->getDescription(),
            'status' => $job->getStatus(),
            'location' => $job->getLocation()?->getCode(),
            'createdAt' => $this->formatWithTimezone($job->getCreatedAt(), $timezone),
            'updatedAt' => $this->formatWithTimezone($job->getUpdatedAt(), $timezone),
            'scheduledDate' => $this->formatWithTimezone($job->getScheduledDate(), $timezone),
            'completedAt' => $this->formatWithTimezone($job->getCompletedAt(), $timezone),
            'assessment' => $job->getAssessment(),
            'timezone' => $timezone,
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
