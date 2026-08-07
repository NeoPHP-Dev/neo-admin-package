<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Database\Entity;

use Neo\Core\Database\ORM\Mapping\Attribute\Column;
use Neo\Core\Database\ORM\Mapping\Attribute\Entity;
use Neo\Core\Database\ORM\Mapping\Attribute\GeneratedValue;
use Neo\Core\Database\ORM\Mapping\Attribute\Id;
use Neo\Core\Database\ORM\Mapping\Attribute\JoinColumn;
use Neo\Core\Database\ORM\Mapping\Attribute\ManyToOne;
use Neo\Core\Database\ORM\Mapping\Attribute\Table;
use Vendor\NeoPHP\AdminPackage\Database\Repository\AdminUserRepository;

#[Entity(repositoryClass: AdminUserRepository::class)]
#[Table(name: 'neo_admin_users')]
final class AdminUser
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: 'integer', unsigned: true)]
    private ?int $id = null;

    #[Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[Column(type: 'string', length: 255)]
    private string $password;

    #[Column(type: 'string', name: 'first_name', length: 100)]
    private string $firstName;

    #[Column(type: 'string', name: 'last_name', length: 100)]
    private string $lastName;

    #[ManyToOne(targetEntity: AdminRole::class)]
    #[JoinColumn(name: 'role_id', referencedColumnName: 'id', nullable: false)]
    private AdminRole $role;

    #[Column(type: 'datetime', name: 'created_at')]
    private \DateTime $createdAt;

    public function __construct(
        string $email,
        string $password,
        string $firstName,
        string $lastName,
        AdminRole $role,
    )
    {
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->role = $role;
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getRole(): AdminRole
    {
        return $this->role;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}