<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Extension;

use Neo\Core\DI\Container;
use Neo\Core\Extension\Attribute\Extension;
use Neo\Core\Extension\Enum\ExtensionTypeEnum;
use Neo\Core\Utils\Config\ConfigManager;
use Neo\Core\View\Interface\TwigExtensionInterface;
use Vendor\NeoPHP\AdminPackage\Service\SidebarResolver;

#[Extension(type: ExtensionTypeEnum::VIEW)]
final class AdminViewExtension implements TwigExtensionInterface
{
    public function __construct(private readonly Container $container) {}

    public function getFunctions(): array
    {
        return [
            'adminConfig' => function () {
                $config = $this->container->get(ConfigManager::class);
                return $config->from('admin-system')->all();
            },
            'adminSidebar' => function () {
                $resolver = $this->container->get(SidebarResolver::class);
                return $resolver->resolve();
            },
        ];
    }

    public function getFilters(): array
    {
        return [];
    }
}