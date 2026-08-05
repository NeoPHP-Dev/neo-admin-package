<?php

declare(strict_types=1);

use Neo\Core\Database\DatabaseManager;
use Neo\Core\Database\Migration\Interface\MigrationInterface;

class MigrationVersion_NeoAdmin_0002 implements MigrationInterface
{
    public function up(DatabaseManager $db): void
    {
        $db->execute("
            CREATE TABLE IF NOT EXISTS `neo_admin_users` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `email` VARCHAR(255) NOT NULL,
                `password` VARCHAR(255) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `role_id` INT UNSIGNED NOT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uq_neo_admin_users_email` (`email`),
                CONSTRAINT `fk_neo_admin_users_role`
                    FOREIGN KEY (`role_id`) REFERENCES `neo_admin_roles` (`id`)
                    ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    public function down(DatabaseManager $db): void
    {
        $db->execute("DROP TABLE IF EXISTS `neo_admin_users`");
    }
}