<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Profiler;

use Neo\Core\DI\Container;
use Neo\Core\Profiler\Interface\CollectorAwareInterface;
use Neo\Core\Profiler\Interface\CollectorInterface;
use Vendor\NeoPHP\AdminPackage\Service\AdminAuthManager;

final class AdminAuthCollector implements CollectorInterface, CollectorAwareInterface
{
    private ?AdminAuthManager $auth = null;

    public function boot(Container $container): void
    {
        $this->auth = $container->get(AdminAuthManager::class);
    }

    public function getName(): string
    {
        return 'admin';
    }

    public function collect(): array
    {
        $connected = $this->auth?->check() ?? false;
        $user = $connected ? $this->auth?->user() : null;

        return [
            'connected' => $connected,
            'email' => $user?->getEmail(),
            'role' => $user?->getRole()->getName(),
        ];
    }

    public function showInToolbar(): bool
    {
        return true;
    }

    public function showInProfiler(): bool
    {
        return true;
    }

    public function renderToolbar(array $data): string
    {
        $connected = $data['connected'] ?? false;
        $color = $connected ? '#4ade80' : '#6b7280';
        $label = $connected ? ($data['email'] ?? 'Admin') : 'Guest';

        return sprintf(
            '<span class="n-label">Admin</span><span class="n-value" style="color:%s">%s</span>',
            $color,
            htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
        );
    }

    public function renderProfiler(array $data): string
    {
        $connected = $data['connected'] ?? false;

        if (!$connected) {
            return '<p class="n-empty">Not connected.</p>';
        }

        return sprintf(
            '<p style="color:#4ade80;font-weight:600;margin-bottom:1.5rem">Connected</p><dl><dt>Email</dt><dd>%s</dd><dt>Role</dt><dd>%s</dd></dl>',
            htmlspecialchars($data['email'] ?? '', ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($data['role'] ?? '', ENT_QUOTES, 'UTF-8')
        );
    }
}