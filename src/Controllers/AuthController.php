<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Controllers;

use Neo\Core\Controller\AbstractController;
use Neo\Core\Http\Request\Request;
use Neo\Core\Http\Response\Types\Response;
use Neo\Core\Routing\Attribute\MainRoute;
use Neo\Core\Routing\Attribute\Route;
use Neo\Core\Security\Auth\Exception\AuthException;

#[MainRoute(path: '/admin/auth', name: 'admin.auth')]
final class AuthController extends AbstractController
{
    #[Route(path: '/login', name: 'login', methods: ['GET'])]
    public function loginForm(): Response
    {
        return $this->render('@NeoAdmin/pages/login.html.twig');
    }

    #[Route(path: '/login', name: 'login.submit', methods: ['POST'])]
    public function login(Request $request): Response
    {
        try {
            $success = $this->auth()->attempt([
                'email' => $request->getPost('email', ''),
                'password' => $request->getPost('password', ''),
            ]);
        } catch (AuthException $e) {
            return $this->render('@NeoAdmin/pages/login.html.twig', [
                'error' => 'Authentication is not configured for this project: ' . $e->getMessage(),
            ]);
        }

        if (!$success) {
            return $this->render('@NeoAdmin/pages/login.html.twig', [
                'error' => 'Invalid credentials.',
            ]);
        }

        return $this->redirectToRoute('admin.dashboard.index');
    }

    #[Route(path: '/logout', name: 'logout', methods: ['GET'])]
    public function logout(): Response
    {
        $this->auth()->logout();

        return $this->redirectToRoute('admin.auth.login');
    }
}