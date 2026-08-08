<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Collector;

use Neo\Core\Database\Form\PropertyAccessor;
use Neo\Core\Profiler\Interface\CollectorInterface;
use Vendor\NeoPHP\AdminPackage\Database\Entity\AdminUser;
use Vendor\NeoPHP\AdminPackage\Service\AdminAuthManager;
use ReflectionClass;
use ReflectionProperty;

final class AdminAuthCollector implements CollectorInterface
{
    private const array MASKED_FIELDS = ['password', 'passwordHash', 'hashedPassword', 'secret', 'token'];

    private readonly PropertyAccessor $accessor;

    public function __construct(private readonly AdminAuthManager $auth)
    {
        $this->accessor = new PropertyAccessor();
    }

    public function getName(): string
    {
        return 'admin-auth';
    }

    public function collect(): array
    {
        $authenticated = $this->auth->check();
        $user = $authenticated ? $this->auth->user() : null;

        return [
            'authenticated' => $authenticated,
            'sessionKey' => $this->auth->getSessionKey(),
            'email' => $user !== null ? $this->readValue($user, 'email') : null,
            'role' => $user !== null ? $this->resolveRole($user) : null,
            'entityClass' => $user !== null ? $user::class : null,
            'properties' => $user !== null ? $this->dumpProperties($user) : [],
        ];
    }

    public function inToolbar(): bool
    {
        return true;
    }

    public function inProfiler(): bool
    {
        return true;
    }

    public function toolbarData(): array
    {
        $data = $this->collect();

        return [
            'label' => 'Admin',
            'value' => $data['authenticated'] ? ('@' . ($data['email'] ?? '?')) : 'Guest',
            'badge' => null,
        ];
    }

    public function profilerData(): array
    {
        $data = $this->collect();

        $blocks = [
            [
                'type' => 'kv',
                'section' => null,
                'rows' => [
                    ['label' => 'Authenticated', 'value' => $data['authenticated'] ? 'Yes' : 'No'],
                    ['label' => 'Session key', 'value' => $data['sessionKey']],
                    ['label' => 'Email', 'value' => $data['email'] ?? 'n/a'],
                    ['label' => 'Role', 'value' => $data['role'] ?? 'n/a'],
                    ['label' => 'Entity class', 'value' => $data['entityClass'] ?? 'n/a'],
                ],
            ],
        ];

        if ($data['authenticated'] && $data['properties'] !== []) {
            $blocks[] = [
                'type' => 'table',
                'section' => 'AdminUser entity properties',
                'columns' => ['Property', 'Value'],
                'rows' => array_map(
                    static fn (string $name, string $value) => [$name, $value],
                    array_keys($data['properties']),
                    array_values($data['properties'])
                ),
            ];
        }

        return [
            'title' => 'Admin Auth',
            'badge' => $data['authenticated'] ? $data['email'] : null,
            'blocks' => $blocks,
        ];
    }

    private function readValue(AdminUser $user, string $property): ?string
    {
        $value = $this->accessor->getValue($user, $property);

        return $value !== null ? (string) $value : null;
    }

    private function resolveRole(AdminUser $user): ?string
    {
        $role = $user->getRole();

        return $role !== null ? $role->getName() : null;
    }

    /**
     * @return array<string, string>
     */
    private function dumpProperties(AdminUser $user): array
    {
        $ref = new ReflectionClass($user);
        $result = [];

        foreach ($ref->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE) as $prop) {
            if (!$prop->isInitialized($user)) {
                continue;
            }

            $name = $prop->getName();

            if ($this->isMasked($name)) {
                $result[$name] = '••••••••';
                continue;
            }

            $value = $prop->getValue($user);
            $result[$name] = $this->stringify($value);
        }

        return $result;
    }

    private function isMasked(string $propertyName): bool
    {
        $lower = strtolower($propertyName);

        foreach (self::MASKED_FIELDS as $masked) {
            if (str_contains($lower, strtolower($masked))) {
                return true;
            }
        }

        return false;
    }

    private function stringify(mixed $value): string
    {
        return match (true) {
            $value === null => 'null',
            is_bool($value) => $value ? 'true' : 'false',
            is_scalar($value) => (string) $value,
            $value instanceof \DateTimeInterface => $value->format('Y-m-d H:i:s'),
            is_object($value) => $value::class,
            is_array($value) => json_encode($value) ?: '[]',
            default => (string) $value,
        };
    }
}