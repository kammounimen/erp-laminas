<?php

declare(strict_types=1);

namespace Application;

use Application\Controller\AuthController;
use Application\Controller\IndexController;
use Application\Controller\UserController;
use Application\Model\User;
use Application\Model\UserTable;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ServiceManager\Factory\InvokableFactory;

return [

    // ===========================================================
    // ROUTES
    // ===========================================================
    'router' => [
        'routes' => [

            // /  → IndexController::index
            'home' => [
                'type'    => \Laminas\Router\Http\Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

            // /auth/login   /auth/logout
            'auth' => [
                'type'    => \Laminas\Router\Http\Segment::class,
                'options' => [
                    'route'       => '/auth[/:action]',
                    'constraints' => ['action' => '[a-zA-Z][a-zA-Z0-9_-]*'],
                    'defaults'    => [
                        'controller' => AuthController::class,
                        'action'     => 'login',
                    ],
                ],
            ],

            // /user          → list
            // /user/add      → add form
            // /user/edit/3   → edit form
            // /user/delete/3 → delete confirm
            'user' => [
                'type'    => \Laminas\Router\Http\Segment::class,
                'options' => [
                    'route'       => '/user[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => UserController::class,
                        'action'     => 'index',
                    ],
                ],
            ],

        ],
    ],

    // ===========================================================
    // CONTROLLERS
    // ===========================================================
    'controllers' => [
        'factories' => [
            // IndexController n'a pas de dépendance → InvokableFactory
            IndexController::class => InvokableFactory::class,

            // AuthController a besoin de UserTable → factory manuelle
            AuthController::class => function ($container) {
                return new AuthController(
                    $container->get(UserTable::class)
                );
            },

            // UserController a besoin de UserTable → factory manuelle
            UserController::class => function ($container) {
                return new UserController(
                    $container->get(UserTable::class)
                );
            },
        ],
    ],

    // ===========================================================
    // SERVICES (modèles / BDD)
    // ===========================================================
    'service_manager' => [
        'factories' => [

            // TableGateway pour la table `users`
            \Laminas\Db\TableGateway\TableGatewayInterface::class => function ($container) {
                $dbAdapter  = $container->get(AdapterInterface::class);
                $resultSet  = new ResultSet();
                $resultSet->setArrayObjectPrototype(new User());
                return new TableGateway('users', $dbAdapter, null, $resultSet);
            },

            // UserTable consomme le TableGateway
            UserTable::class => function ($container) {
                return new UserTable(
                    $container->get(\Laminas\Db\TableGateway\TableGatewayInterface::class)
                );
            },
        ],
    ],

    // ===========================================================
    // VUES
    // ===========================================================
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],

];