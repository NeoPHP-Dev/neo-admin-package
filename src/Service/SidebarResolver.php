<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Service;

use Neo\Core\Routing\Attribute\MainRoute;
use Neo\Core\Routing\Attribute\Route;
use Neo\Core\Routing\RouterManager;
use Neo\Core\Utils\Config\ConfigManager;
use Neo\Core\Utils\Scanner\ScannerAttributeManager;
use ReflectionMethod;
use RuntimeException;

final class SidebarResolver
{
    public function __construct(
        private ConfigManager $config,
        private RouterManager $router,
    ) {}

    public function resolve(): array
    {
        /** @var array<string, array{controller: class-string, icon: string, title: string}> $sidebar */
        $sidebar = $this->config->from('admin-system')->get('sidebar', []);

        $items = [];

        foreach ($sidebar as $key => $entry) {
            $controller = $entry['controller'] ?? null;

            if (!is_string($controller) || !class_exists($controller)) {
                throw new RuntimeException(
                    sprintf("Sidebar entry '%s' does not reference a valid controller class.", $key)
                );
            }

            $items[] = [
                'key' => $key,
                'title' => $entry['title'] ?? $key,
                'icon' => $entry['icon'] ?? 'circle',
                'url' => $this->router->generateUrl($this->resolveRouteName($controller, $key)),
            ];
        }

        return $items;
    }

    private function resolveRouteName(string $controller, string $entryKey): string
    {
        $classResults = new ScannerAttributeManager($controller)
            ->onClass()
            ->withAttribute(MainRoute::class)
            ->scan();

        if (empty($classResults)) {
            throw new RuntimeException(
                sprintf("Controller '%s' referenced by sidebar entry '%s' has no #[MainRoute] attribute.", $controller, $entryKey)
            );
        }

        $mainRoute = $classResults[0]->getAttribute();

        $methodResults = new ScannerAttributeManager($controller)
            ->onMethods(ReflectionMethod::IS_PUBLIC)
            ->withAttribute(Route::class)
            ->scan();

        foreach ($methodResults as $entry) {
            $reflection = $entry->getReflection();

            if ($reflection instanceof ReflectionMethod && $reflection->getName() === 'index') {
                $route = $entry->getAttribute();

                return $mainRoute->name . '.' . $route->name;
            }
        }

        throw new RuntimeException(
            sprintf(
                "Controller '%s' referenced by sidebar entry '%s' has no 'index' method with a #[Route] attribute.",
                $controller,
                $entryKey
            )
        );
    }
}