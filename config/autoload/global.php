<?php
return [
    'db' => [
        'driver' => 'Pdo_Mysql',
    ],
    'service_manager' => [
        'factories' => [
            \Laminas\Db\Adapter\AdapterInterface::class =>
                \Laminas\Db\Adapter\AdapterServiceFactory::class,
        ],
    ],
];