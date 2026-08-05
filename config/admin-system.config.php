<?php

declare(strict_types=1);

return [
    'title' => 'Administration',
    'description' => '',
    'route_prefix' => '/admin',

    'auth' => [
        'required_role' => 'ROLE_ADMIN'
    ],

    'sidebar' => [
        'dashboard' => [
            'controller' => \Vendor\NeoPHP\AdminPackage\Controllers\DashboardController::class,
            'icon' => 'layout-dashboard',
            'title' => 'Dashboard',
        ],

        // 'users' => [
        //     'controller' => \Neo\Src\MyProject\App\Controllers\NeoAdmin\UsersController::class,
        //     'icon' => 'users',
        //     'title' => 'Users',
        // ],
    ]
];