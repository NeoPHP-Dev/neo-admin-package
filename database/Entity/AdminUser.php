<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Database\Entity;

use Neo\Core\Database\ORM\Mapping\Attribute as ORM;
use Vendor\NeoPHP\AdminPackage\Database\Repository\AdminUserRepository;

#[ORM\Entity(repositoryClass: AdminUserRepository::class)]
#[ORM\Table(name: 'neo_admin_users')]
class AdminUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', unsigned: true)]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(type: 'string', length: 255)]
    private string $password;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: AdminRole::class)]
    #[ORM\JoinColumn(name: 'role_id', referencedColumnName: 'id', nullable: false)]
    private AdminRole $role;

    #[ORM\Column(type: 'datetime', name: 'created_at')]
    private \DateTime $createdAt;

    public function __construct(string $email, string $password, string $name, AdminRole $role)
    {
        $this->email = $email;
        $this->password = $password;
        $this->name = $name;
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

    public function getName(): string
    {
        return $this->name;
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