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

    /**
     * @return list<array{
     *     key: string,
     *     title: string,
     *     icon: string,
     *     type: 'link'|'group',
     *     url?: string,
     *     children?: list<array{key: string, title: string, icon: string, url: string}>
     * }>
     */
    public function resolve(): array
    {
        /** @var array<string, mixed> $sidebar */
        $sidebar = $this->config->from('admin-system')->get('sidebar', []);

        $items = [];

        foreach ($sidebar as $key => $entry) {
            if (!is_array($entry)) {
                throw new RuntimeException(sprintf("Sidebar entry '%s' must be an array.", $key));
            }

            $items[] = $this->isGroup($entry)
                ? $this->resolveGroup($key, $entry)
                : $this->resolveLink($key, $entry);
        }

        return $items;
    }

    /**
     * @param array<string, mixed> $entry
     */
    private function isGroup(array $entry): bool
    {
        return !array_key_exists('controller', $entry);
    }

    /**
     * @param array<string, mixed> $entry
     * @return array{key: string, title: string, icon: string, type: 'link', url: string}
     */
    private function resolveLink(string $key, array $entry): array
    {
        $controller = $entry['controller'] ?? null;

        if (!is_string($controller) || !class_exists($controller)) {
            throw new RuntimeException(
                sprintf("Sidebar entry '%s' does not reference a valid controller class.", $key)
            );
        }

        return [
            'key' => $key,
            'title' => $entry['title'] ?? $key,
            'icon' => $entry['icon'] ?? 'circle',
            'type' => 'link',
            'url' => $this->router->generateUrl($this->resolveRouteName($controller, $key)),
        ];
    }

    /**
     * @param array<string, mixed> $entry
     * @return array{key: string, title: string, icon: string, type: 'group', children: list<array<string, mixed>>}
     */
    private function resolveGroup(string $key, array $entry): array
    {
        $children = [];

        foreach ($entry as $childKey => $childEntry) {
            if (!is_array($childEntry) || !array_key_exists('controller', $childEntry)) {
                continue;
            }

            $children[] = $this->resolveLink($key . '.' . $childKey, $childEntry);
        }

        return [
            'key' => $key,
            'title' => $key,
            'icon' => 'folder',
            'type' => 'group',
            'children' => $children,
        ];
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