<?php

declare(strict_types=1);

use Neo\Core\Database\DatabaseManager;
use Neo\Core\Database\Migration\Interface\MigrationInterface;

final class MigrationVersion_NeoAdmin_0003 implements MigrationInterface
{
    public function up(DatabaseManager $db): void
    {
        $db->execute("ALTER TABLE `neo_admin_users` DROP COLUMN `name`");
        $db->execute("ALTER TABLE `neo_admin_users` ADD COLUMN `first_name` VARCHAR(100) NOT NULL AFTER `password`");
        $db->execute("ALTER TABLE `neo_admin_users` ADD COLUMN `last_name` VARCHAR(100) NOT NULL AFTER `first_name`");
    }

    public function down(DatabaseManager $db): void
    {
        $db->execute("ALTER TABLE `neo_admin_users` ADD COLUMN `name` VARCHAR(255) NOT NULL AFTER `password`");
        $db->execute("ALTER TABLE `neo_admin_users` DROP COLUMN `first_name`");
        $db->execute("ALTER TABLE `neo_admin_users` DROP COLUMN `last_name`");
    }
}