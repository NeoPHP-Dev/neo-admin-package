<?php

declare(strict_types=1);

return [
    'title' => 'Administration',
    'description' => '',
    'route_prefix' => '/admin',

    'auth' => [
        'required_role' => 'ROLE_ADMIN',
        'redirect_after_login' => 'admin.panel.index',
        'session_key' => '_neo_admin_auth_user_id',
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
        // 'Settings' => [
        //      'general' => [
        //          'controller' => \Neo\Src\MyProject\App\Controllers\NeoAdmin\Settings\GeneralController::class,
        //          'icon' => 'cog',
        //          'title' => 'General'
        //      ],
        //      'profile' => [
        //          'controller' => \Neo\Src\MyProject\App\Controllers\NeoAdmin\Settings\ProfileController::class,
        //          'icon' => 'user',
        //          'title' => 'Profile'
        //      ],
        //  ]
    ]
];