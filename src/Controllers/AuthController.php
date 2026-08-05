<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Controllers;

use Neo\Core\Controller\AbstractController;
use Neo\Core\Http\Request\Request;
use Neo\Core\Http\Response\Types\Response;
use Neo\Core\Routing\Attribute\MainRoute;
use Neo\Core\Routing\Attribute\Route;
use Neo\Core\Utils\Config\ConfigManager;
use Vendor\NeoPHP\AdminPackage\Service\AdminAuthManager;

#[MainRoute(path: '/admin/auth', name: 'admin.auth')]
final class AuthController extends AbstractController
{
    #[Route(path: '/login', name: 'login', methods: ['GET'])]
    public function loginForm(): Response
    {
        return $this->render('@NeoAdmin/pages/login.html.twig');
    }

    #[Route(path: '/login', name: 'login.submit', methods: ['POST'])]
    public function login(Request $request, AdminAuthManager $auth, ConfigManager $config): Response
    {
        $success = $auth->attempt(
            $request->getPost('email', ''),
            $request->getPost('password', ''),
        );

        if (!$success) {
            return $this->render('@NeoAdmin/pages/login.html.twig', [
                'error' => 'Invalid credentials.',
            ]);
        }

        $redirectRoute = $config->from('admin-system')
            ->get('auth.redirect_after_login', 'admin.panel.index');

        return $this->redirectToRoute($redirectRoute);
    }

    #[Route(path: '/logout', name: 'logout', methods: ['GET'])]
    public function logout(AdminAuthManager $auth): Response
    {
        $auth->logout();

        return $this->redirectToRoute('admin.auth.login');
    }
}