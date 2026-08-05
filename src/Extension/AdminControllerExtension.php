<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Extension;

use Neo\Core\Controller\AbstractController;
use Neo\Core\Controller\Interface\ControllerExtensionInterface;
use Neo\Core\DI\Container;
use Neo\Core\Extension\Attribute\Extension;
use Neo\Core\Extension\Enum\ExtensionTypeEnum;
use Neo\Core\Utils\Config\ConfigManager;
use Vendor\NeoPHP\AdminPackage\Service\SidebarResolver;

/**
 * @method array<string, mixed> adminConfig()
 * @method list<array{key: string, title: string, icon: string, url: string}> adminSidebar()
 */
#[Extension(type: ExtensionTypeEnum::CONTROLLER)]
final class AdminControllerExtension implements ControllerExtensionInterface
{
    public function extend(AbstractController $controller, Container $container): void
    {
        $controller->registerMethod('adminConfig', function () use ($container) {
            $config = $container->get(ConfigManager::class);
            return $config->from('admin-system')->all();
        });

        $controller->registerMethod('adminSidebar', function () use ($container) {
            $resolver = $container->get(SidebarResolver::class);
            return $resolver->resolve();
        });
    }
}