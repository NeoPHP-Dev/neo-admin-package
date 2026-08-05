<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Database\Repository;

use Neo\Core\Database\ORM\Persistence\EntityRepository;
use Vendor\NeoPHP\AdminPackage\Database\Entity\AdminUser;

/**
 * @extends EntityRepository<AdminUser>
 */
class AdminUserRepository extends EntityRepository
{
    public function findByEmail(string $email): ?AdminUser
    {
        return $this->findOneBy(['email' => $email]);
    }
}