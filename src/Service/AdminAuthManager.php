<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Service;

use Neo\Core\Database\ORM\Persistence\EntityManager;
use Neo\Core\Http\Client\Session\Session;
use Neo\Core\Security\Auth\PasswordManager;
use Neo\Core\Utils\Config\ConfigManager;
use Vendor\NeoPHP\AdminPackage\Database\Entity\AdminUser;
use Vendor\NeoPHP\AdminPackage\Database\Repository\AdminUserRepository;

class AdminAuthManager
{
    private const string DEFAULT_SESSION_KEY = '_neo_admin_auth_user_id';

    public function __construct(
        private Session $session,
        private EntityManager $entityManager,
        private PasswordManager $passwordManager,
        private ConfigManager $configManager,
    ) {
    }

    public function getSessionKey(): string
    {
        return $this->configManager->from('admin-system')
            ->get('auth.session_key', self::DEFAULT_SESSION_KEY);
    }

    public function attempt(string $email, string $password): bool
    {
        /** @var AdminUserRepository $repository */
        $repository = $this->entityManager->getRepository(AdminUser::class);
        $user = $repository->findByEmail($email);

        if ($user === null) {
            return false;
        }

        if (!$this->passwordManager->verify($password, $user->getPassword())) {
            return false;
        }

        $this->login($user);

        return true;
    }

    public function login(AdminUser $user): void
    {
        $this->session->regenerate();
        $this->session->set($this->getSessionKey(), $user->getId());
    }

    public function logout(): void
    {
        $this->session->remove($this->getSessionKey());
    }

    public function check(): bool
    {
        return $this->session->has($this->getSessionKey());
    }

    public function user(): ?AdminUser
    {
        if (!$this->check()) {
            return null;
        }

        $id = $this->session->get($this->getSessionKey());

        if ($id === null) {
            $this->logout();
            return null;
        }

        /** @var AdminUser $user */
        $user = $this->entityManager->find(AdminUser::class, $id);

        if ($user === null) {
            $this->logout();
        }

        return $user;
    }

    public function hasRole(string $roleName): bool
    {
        $user = $this->user();

        return $user !== null && $user->getRole()->getName() === $roleName;
    }
}