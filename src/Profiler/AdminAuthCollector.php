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

    public function renderTab(array $data): string
    {
        $connected = $data['connected'] ?? false;
        $color = $connected ? '#4ade80' : '#71717a';
        $label = $connected ? ($data['email'] ?? 'Admin') : 'Guest';

        return sprintf(
            '<div class="n-tab" onclick="neoSwitch(\'admin\')"><span class="n-label">Admin</span><span class="n-value" style="color:%s">%s</span></div>',
            $color,
            htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
        );
    }

    public function renderPanel(array $data): string
    {
        $connected = $data['connected'] ?? false;
        $chipClass = $connected ? 'on' : 'off';
        $chipLabel = $connected ? 'Connected' : 'Not connected';

        $html = sprintf(
            '<div class="n-auth-chip %s"><span class="n-auth-chip-dot"></span>%s</div>',
            $chipClass,
            htmlspecialchars($chipLabel, ENT_QUOTES, 'UTF-8')
        );

        if ($connected) {
            $html .= sprintf(
                '<dl class="n-kv"><dt>Email</dt><dd>%s</dd><dt>Role</dt><dd>%s</dd></dl>',
                htmlspecialchars($data['email'] ?? '', ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($data['role'] ?? '', ENT_QUOTES, 'UTF-8')
            );
        }

        return $html;
    }
}