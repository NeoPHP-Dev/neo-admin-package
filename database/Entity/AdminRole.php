<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage\Database\Entity;

use Neo\Core\Database\ORM\Mapping\Attribute as ORM;
use Vendor\NeoPHP\AdminPackage\Database\Repository\AdminRoleRepository;

#[ORM\Entity(repositoryClass: AdminRoleRepository::class)]
#[ORM\Table(name: 'neo_admin_roles')]
class AdminRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', unsigned: true)]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}