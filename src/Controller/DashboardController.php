<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Controller;

use Neo\Core\Controller\AbstractController;
use Neo\Core\Http\Response\Types\Response;
use Neo\Core\Routing\Attribute\MainRoute;
use Neo\Core\Routing\Attribute\Route;
use Neo\Core\Security\Middleware\Attribute\Middleware;
use Vendor\NeoPHP\AdminPackage\Middleware\AdminAuthMiddleware;

#[MainRoute(path: '/admin/panel', name: 'admin.panel')]
#[Middleware(
    use: AdminAuthMiddleware::class,
    message: 'You must be an administrator to access this page.',
    onError: 'block',
    params: ['requiredRole' => 'ROLE_ADMIN'],
)]
final class DashboardController extends AbstractController
{
    #[Route(path: '/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('@NeoAdmin/pages/dashboard.html.twig');
    }
}