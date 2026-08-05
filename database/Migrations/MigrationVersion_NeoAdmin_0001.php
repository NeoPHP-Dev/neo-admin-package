<?php

declare(strict_types=1);

use Neo\Core\Database\DatabaseManager;
use Neo\Core\Database\Migration\Interface\MigrationInterface;

class MigrationVersion_NeoAdmin_0001 implements MigrationInterface
{
    public function up(DatabaseManager $db): void
    {
        $db->execute("
            CREATE TABLE IF NOT EXISTS `neo_admin_roles` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(100) NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_neo_admin_roles_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        $db->execute("INSERT INTO `neo_admin_roles` (`name`) VALUES ('ROLE_ADMIN')");
    }

    public function down(DatabaseManager $db): void
    {
        $db->execute("DROP TABLE IF EXISTS `neo_admin_roles`");
    }
}