<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Middleware;

use Neo\Core\Security\Middleware\Interface\MiddlewareInterface;
use Vendor\NeoPHP\AdminPackage\Service\AdminAuthManager;

final class AdminAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private AdminAuthManager $auth,
        private string $requiredRole,
    ) {
    }

    public function handle(): bool
    {
        if (!$this->auth->check()) {
            return false;
        }

        return $this->auth->hasRole($this->requiredRole);
    }
}