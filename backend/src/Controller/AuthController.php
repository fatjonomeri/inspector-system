<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/auth', name: 'api_auth_')]
#[OA\Tag(name: 'Authentication')]
class AuthController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    #[OA\Post(
        path: '/api/auth/register',
        summary: 'Register a new inspector',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password', 'location'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'inspector@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123'),
                    new OA\Property(property: 'location', type: 'string', enum: ['UK', 'Mexico', 'India'], example: 'UK'),
                    new OA\Property(property: 'firstName', type: 'string', example: 'John'),
                    new OA\Property(property: 'lastName', type: 'string', example: 'Doe'),
                    new OA\Property(property: 'role', type: 'string', enum: ['ROLE_INSPECTOR', 'ROLE_ADMIN'], example: 'ROLE_INSPECTOR')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'User registered successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'User registered successfully'),
                        new OA\Property(property: 'user', type: 'object')
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Validation error'),
            new OA\Response(response: 409, description: 'User already exists')
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!$data) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Invalid JSON'
            ], Response::HTTP_BAD_REQUEST);
        }

        $requiredFields = ['email', 'password', 'location'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                return new JsonResponse([
                    'success' => false,
                    'message' => "Field '$field' is required"
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return new JsonResponse([
                'success' => false,
                'message' => 'User with this email already exists'
            ], Response::HTTP_CONFLICT);
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setLocation($data['location']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Optional fields
        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }
        if (isset($data['lastName'])) {
            $user->setLastName($data['lastName']);
        }

        $user->setRoles(['ROLE_INSPECTOR']);
        
        // Validate entity
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errorMessages
            ], Response::HTTP_BAD_REQUEST);
        }

        // Save user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'location' => $user->getLocation(),
                'roles' => $user->getRoles(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'timezone' => $user->getTimezone(),
            ]
        ], Response::HTTP_CREATED);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    #[OA\Post(
        path: '/api/auth/login',
        summary: 'Login to get a session cookie',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email', type: 'string', format: 'email', example: 'inspector@example.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'password123')
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Login successful'),
                        new OA\Property(property: 'user', type: 'object')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid credentials')
        ]
    )]
    public function login(): JsonResponse
    {
        // Handled by security.yaml (json_login)
        $user = $this->getUser();
        
        if (!$user instanceof User) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Authentication required'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'location' => $user->getLocation(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ]
        ]);
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    #[OA\Post(
        path: '/api/auth/logout',
        summary: 'Logout and destroy session',
        responses: [
            new OA\Response(response: 200, description: 'Logged out successfully')
        ]
    )]
    public function logout(): JsonResponse
    {
        return new JsonResponse([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    #[OA\Get(
        path: '/api/auth/me',
        summary: 'Get current authenticated user',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Current user information',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'user', type: 'object')
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Not authenticated')
        ]
    )]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Not authenticated'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
                'location' => $user->getLocation(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'timezone' => $user->getTimezone(),
                'createdAt' => $user->getCreatedAt()?->format('Y-m-d H:i:s'),
                'lastLoginAt' => $user->getLastLoginAt()?->format('Y-m-d H:i:s'),
            ]
        ]);
    }
}