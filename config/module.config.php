<?php

return array(
    'migrations' => array(
        'dir' => dirname(__FILE__) . '/../../../../data/migrations',
        'namespace' => 'ZfMigrations\Migrations',
        'show_log' => true
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'migration-version' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'migration version [--env=]',
                        'defaults' => array(
                            'controller' => 'ZfMigrations\Controller\Migrate',
                            'action' => 'version'
                        )
                    )
                ),
                'migration-list' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'migration list [--env=] [--all]',
                        'defaults' => array(
                            'controller' => 'ZfMigrations\Controller\Migrate',
                            'action' => 'list'
                        )
                    )
                ),
                'migration-apply' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'migration apply [<version>] [--env=] [--force] [--down] [--fake]',
                        'defaults' => array(
                            'controller' => 'ZfMigrations\Controller\Migrate',
                            'action' => 'apply'
                        )
                    )
                ),
                'migration-generate' => array(
                    'type' => 'simple',
                    'options' => array(
                        'route' => 'migration generate [--env=]',
                        'defaults' => array(
                            'controller' => 'ZfMigrations\Controller\Migrate',
                            'action' => 'generateSkeleton'
                        )
                    )
                )
            )
        )
    ),
    'controllers' => array(
        'invokables' => array(
            'ZfMigrations\Controller\Migrate' => 'ZfMigrations\Controller\MigrateController'
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
