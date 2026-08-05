<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Middleware;

use Neo\Core\Security\Auth\AuthManager;
use Neo\Core\Security\Middleware\Interface\MiddlewareInterface;

final class AdminAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly AuthManager $auth,
        private readonly string $requiredRole,
    ) {}

    public function handle(): bool
    {
        if (!$this->auth->check()) {
            return false;
        }

        return $this->auth->hasRole($this->requiredRole);
    }
}