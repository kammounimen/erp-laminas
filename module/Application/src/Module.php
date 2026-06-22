<?php

declare(strict_types=1);

namespace Application;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

/**
 * Point d'entrée du module Application
 */
class Module implements ConfigProviderInterface
{
    public function getConfig(): array
    {
        // Charge module.config.php
        return include __DIR__ . '/../config/module.config.php';
    }
}