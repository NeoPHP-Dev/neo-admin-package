<?php

declare(strict_types=1);

namespace Vendor\NeoPHP\AdminPackage;

use Neo\Core\Package\Abstract\AbstractPackage;

class NeoAdminPackage extends AbstractPackage
{
    public function getName(): string
    {
        return 'NeoAdmin';
    }

    public function getPath(): string
    {
        return dirname(__DIR__);
    }
}