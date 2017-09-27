<?php

return [
    'migrations' => [
        'dir' => dirname(__FILE__) . '/../../../../data/migrations',
        'namespace' => 'ZfMigrations\Migrations',
        'show_log' => true
    ],
    'console' => [
        'router' => [
            'routes' => [
                'migration-version' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'migration version [--env=]',
                        'defaults' => [
                            'controller' => 'ZfMigrations\Controller\Migrate',
                            'action' => 'version'
                        ]
                    ]
                ],
                'migration-list' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'migration list [--env=] [--all]',
                        'defaults' => [
                            'controller' => 'ZfMigrations\Controller\Migrate',
                            'action' => 'list'
                        ]
                    ]
                ],
                'migration-apply' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'migration apply [<version>] [--env=] [--force] [--down] [--fake]',
                        'defaults' => [
                            'controller' => 'ZfMigrations\Controller\Migrate',
                            'action' => 'apply'
                        ]
                    ]
                ],
                'migration-generate' => [
                    'type' => 'simple',
                    'options' => [
                        'route' => 'migration generate [--env=]',
                        'defaults' => [
                            'controller' => 'ZfMigrations\Controller\Migrate',
                            'action' => 'generateSkeleton'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'controllers' => [
        'invokables' => [
            'ZfMigrations\Controller\Migrate' => 'ZfMigrations\Controller\MigrateController'
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];
