<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutSuccessHandler
{
    public function onLogout(LogoutEvent $event): void
    {
        $response = new JsonResponse([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
        
        $event->setResponse($response);
    }
}
