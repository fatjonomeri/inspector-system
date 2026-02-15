<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\User;
use App\Repository\JobRepository;
use App\Trait\TimezoneAwareTrait;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/jobs', name: 'api_jobs_')]
class JobController extends AbstractController
{
    use TimezoneAwareTrait;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private JobRepository $jobRepository
    ) {
    }

    #[Route('/available', name: 'available', methods: ['GET'])]
    #[OA\Get(
        path: '/api/jobs/available',
        summary: 'Get available jobs',
        description: 'Returns all jobs with status "available" and filters by the authenticated user\'s location',
        tags: ['Job'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of available jobs',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(
                            property: 'jobs',
                            type: 'array',
                            items: new OA\Items(type: 'object')
                        )
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Not authenticated')
        ]
    )]
    public function getAvailable(): JsonResponse
    {
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            return $this->json([
                'success' => false,
                'message' => 'Authentication required'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Get all available jobs for the user's location
        $jobs = $this->jobRepository->findAvailable($user->getLocation());

        return $this->json([
            'success' => true,
            'jobs' => array_map(fn($job) => $this->serializeJob($job, $user), $jobs)
        ]);
    }

    #[Route('/{id}/assign', name: 'assign', methods: ['POST'])]
    #[OA\Post(
        path: '/api/jobs/{id}/assign',
        summary: 'Assign job to inspector',
        tags: ['Job'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['scheduledDate'],
                properties: [
                    new OA\Property(
                        property: 'scheduledDate', 
                        type: 'string', 
                        format: 'date-time',
                        description: 'When the job is scheduled to be performed',
                        example: '2026-02-20 14:30:00'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Job assigned successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Job assigned successfully'),
                        new OA\Property(property: 'job', type: 'object')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Not authenticated'),
            new OA\Response(response: 404, description: 'Job not found'),
            new OA\Response(response: 409, description: 'Job is not available for assignment')
        ]
    )]
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

        $availableJobs = $this->jobRepository->findAvailable($user->getLocation());
        if (!in_array($job, $availableJobs)) {
            return $this->json([
                'success' => false,
                'message' => 'Job is not available for assignment'
            ], Response::HTTP_CONFLICT);
        }

        $data = json_decode($request->getContent(), true);
        

        //check if scheduled date is provided and is in the future
        if (!isset($data['scheduledDate'])) {
            return $this->json([
                'success' => false,
                'message' => 'scheduledDate is required'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $scheduledDate = $this->parseFromTimezone($data['scheduledDate'], $user->getTimezone());

            if ($scheduledDate < new \DateTimeImmutable('now')) {
                return $this->json([
                    'success' => false,
                    'message' => 'The scheduled date cannot be in the past.'
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $job->assignTo($user, $scheduledDate);
            
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Job assigned successfully',
                'job' => $this->serializeJob($job, $user)
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}/complete', name: 'complete', methods: ['POST'])]
    #[OA\Post(
        path: '/api/jobs/{id}/complete',
        summary: 'Complete assigned job',
        tags: ['Job'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['assessment'],
                properties: [
                    new OA\Property(
                        property: 'assessment', 
                        type: 'string', 
                        description: 'Inspector assessment of the completed work',
                        example: 'All safety checks completed. No issues found.'
                    ),
                    new OA\Property(
                        property: 'completedAt', 
                        type: 'string', 
                        format: 'date-time',
                        description: 'When the job was completed (optional, defaults to now)',
                        example: '2026-02-20 16:45:00'
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200, 
                description: 'Job completed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Job completed successfully'),
                        new OA\Property(property: 'job', type: 'object')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Not authenticated'),
            new OA\Response(response: 403, description: 'Can only complete own jobs'),
            new OA\Response(response: 404, description: 'Job not found'),
            new OA\Response(response: 409, description: 'Job is not in assigned status')
        ]
    )]
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
            // Parse completedAt if provided, otherwise it will default to now
            $completedAt = null;
            if (isset($data['completedAt'])) {
                $completedAt = $this->parseFromTimezone($data['completedAt'], $user->getTimezone());
            }
            
            $job->complete($data['assessment'], $completedAt);
            
            $this->entityManager->flush();

            return $this->json([
                'success' => true,
                'message' => 'Job completed successfully',
                'job' => $this->serializeJob($job, $user)
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
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
