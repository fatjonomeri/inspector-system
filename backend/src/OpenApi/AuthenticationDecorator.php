<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\OpenApi\OpenApi;

final class AuthenticationDecorator implements OpenApiFactoryInterface
{
    public function __construct(
        private OpenApiFactoryInterface $decorated
    ) {
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $pathItem = new Model\PathItem(
            ref: 'Authentication',
            post: new Model\Operation(
                operationId: 'postAuthLogin',
                tags: ['Authentication'],
                summary: 'Login to get a session cookie',
                description: 'Login with email and password to receive a session cookie',
                requestBody: new Model\RequestBody(
                    description: 'Login credentials',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['email', 'password'],
                                'properties' => [
                                    'email' => [
                                        'type' => 'string',
                                        'format' => 'email',
                                        'example' => 'inspector@example.com'
                                    ],
                                    'password' => [
                                        'type' => 'string',
                                        'format' => 'password',
                                        'example' => 'password123'
                                    ]
                                ]
                            ]
                        ]
                    ]),
                    required: true
                ),
                responses: [
                    '200' => [
                        'description' => 'Login successful',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'success' => ['type' => 'boolean', 'example' => true],
                                        'message' => ['type' => 'string', 'example' => 'Login successful'],
                                        'user' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'integer'],
                                                'email' => ['type' => 'string'],
                                                'roles' => ['type' => 'array', 'items' => ['type' => 'string']],
                                                'location' => ['type' => 'string'],
                                                'firstName' => ['type' => 'string'],
                                                'lastName' => ['type' => 'string']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Invalid credentials'
                    ]
                ]
            )
        );
        $openApi->getPaths()->addPath('/api/auth/login', $pathItem);

        $registerPathItem = new Model\PathItem(
            ref: 'Authentication',
            post: new Model\Operation(
                operationId: 'postAuthRegister',
                tags: ['Authentication'],
                summary: 'Register a new inspector',
                description: 'Create a new user account',
                requestBody: new Model\RequestBody(
                    description: 'Registration details',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'required' => ['email', 'password', 'location'],
                                'properties' => [
                                    'email' => ['type' => 'string', 'format' => 'email', 'example' => 'inspector@example.com'],
                                    'password' => ['type' => 'string', 'format' => 'password', 'example' => 'password123'],
                                    'location' => ['type' => 'string', 'enum' => ['UK', 'Mexico', 'India'], 'example' => 'UK'],
                                    'firstName' => ['type' => 'string', 'example' => 'John'],
                                    'lastName' => ['type' => 'string', 'example' => 'Doe'],
                                    'role' => ['type' => 'string', 'enum' => ['ROLE_INSPECTOR', 'ROLE_ADMIN'], 'example' => 'ROLE_INSPECTOR']
                                ]
                            ]
                        ]
                    ]),
                    required: true
                ),
                responses: [
                    '201' => [
                        'description' => 'User registered successfully'
                    ],
                    '400' => [
                        'description' => 'Validation error'
                    ],
                    '409' => [
                        'description' => 'User already exists'
                    ]
                ]
            )
        );
        $openApi->getPaths()->addPath('/api/auth/register', $registerPathItem);

        $logoutPathItem = new Model\PathItem(
            ref: 'Authentication',
            post: new Model\Operation(
                operationId: 'postAuthLogout',
                tags: ['Authentication'],
                summary: 'Logout and destroy session',
                description: 'Logout current user and clear session',
                responses: [
                    '200' => [
                        'description' => 'Logged out successfully'
                    ]
                ]
            )
        );
        $openApi->getPaths()->addPath('/api/auth/logout', $logoutPathItem);

        $mePathItem = new Model\PathItem(
            ref: 'Authentication',
            get: new Model\Operation(
                operationId: 'getAuthMe',
                tags: ['Authentication'],
                summary: 'Get current authenticated user',
                description: 'Returns information about the currently logged in user',
                responses: [
                    '200' => [
                        'description' => 'Current user information'
                    ],
                    '401' => [
                        'description' => 'Not authenticated'
                    ]
                ]
            )
        );
        $openApi->getPaths()->addPath('/api/auth/me', $mePathItem);

        return $openApi;
    }
}
