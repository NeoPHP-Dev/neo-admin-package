<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Command;

use Neo\Core\Application\ApplicationPaths;
use Neo\Core\Console\Abstract\AbstractCommand;
use Neo\Core\Console\Attribute\Command;
use Neo\Core\Console\Enum\ExitCode;
use Neo\Core\Console\Input\Input;
use Neo\Core\Console\Input\InputOption;
use Neo\Core\Console\Output\Output;
use Neo\Core\Database\Access\Connection\DatabaseConnection;
use Neo\Core\Database\ORM\Persistence\EntityManager;
use Neo\Core\DI\Container;
use Neo\Core\Security\Auth\PasswordManager;
use Vendor\NeoPHP\AdminPackage\Database\Entity\AdminRole;
use Vendor\NeoPHP\AdminPackage\Database\Entity\AdminUser;
use Vendor\NeoPHP\AdminPackage\Database\Repository\AdminUserRepository;

#[Command(
    name: 'admin:create-administrator',
    description: 'Create a new administrator account for neo-admin-package',
    category: 'Admin',
)]
final class CreateAdministratorCommand extends AbstractCommand
{
    public function __construct(
        private Container $container
    ) {
    }

    public function configure(): void
    {
        $this->addOption(
            name: 'project',
            shortcut: null,
            mode: InputOption::REQUIRED,
            description: 'Target project',
        );
    }

    public function do(Input $input, Output $output): ExitCode
    {
        $project = $input->getOption('project');
        $basePath = ROOT_DIR . "/src/$project";

        if (!is_dir($basePath)) {
            Output::error("Project '$project' not found.");
            return ExitCode::FAILURE;
        }

        try {
            new ApplicationPaths($this->container)->register($project);
            $this->container->get(DatabaseConnection::class);

            if (!DatabaseConnection::isConnected()) {
                Output::error('Database not connected.');
                return ExitCode::FAILURE;
            }

            $em = $this->container->get(EntityManager::class);

            $email = Input::ask('Email');
            $firstName = Input::ask('First name');
            $lastName = Input::ask('Last name');
            $password = Input::secret('Password');
            $passwordConfirm = Input::secret('Confirm password');

            if ($password !== $passwordConfirm) {
                Output::error('Passwords do not match.');
                return ExitCode::FAILURE;
            }

            if (strlen($password) < 8) {
                Output::error('Password must be at least 8 characters long.');
                return ExitCode::FAILURE;
            }

            /** @var AdminUserRepository $userRepo */
            $userRepo = $em->getRepository(AdminUser::class);

            if ($userRepo->findByEmail($email) !== null) {
                Output::error("An administrator with email '$email' already exists.");
                return ExitCode::FAILURE;
            }

            $roleRepo = $em->getRepository(AdminRole::class);
            $role = $roleRepo->findOneBy(['name' => 'ROLE_ADMIN']);

            if ($role === null) {
                Output::error("Role 'ROLE_ADMIN' not found. Run migrations first: php bin/neo database:migration:migrate --project=$project");
                return ExitCode::FAILURE;
            }

            $passwordManager = $this->container->get(PasswordManager::class);
            $hashedPassword = $passwordManager->hash($password);

            $user = new AdminUser($email, $hashedPassword, $firstName, $lastName, $role);
            $em->persist($user);
            $em->flush();

            Output::success("Administrator '$email' created successfully.");

            return ExitCode::SUCCESS;
        } catch (\Throwable $e) {
            Output::error('Failed to create administrator: ' . $e->getMessage());
            return ExitCode::FAILURE;
        }
    }
}