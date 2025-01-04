<?php

// config/services.php

use App\Database;
use App\Repository\StatRepository;
use App\Repository\StatRepositoryMysql;
use App\Repository\StatRepositorySqlite;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

return function(ContainerConfigurator $container): void {
    // default configuration for services in *this* file
    $services = $container->services()
        ->defaults()
            ->autowire()      // Automatically injects dependencies in your services.
            ->autoconfigure() // Automatically registers your services as commands, event subscribers, etc.
    ;

    // makes classes in src/ available to be used as services
    // this creates a service per class whose id is the fully-qualified class name
    $services->load('App\\', '../src/')
        ->exclude('../src/{DependencyInjection,Entity,Kernel.php}');

    // order is important in this file because service definitions
    // always *replace* previous ones; add your own service configuration below
    $services->set(Database::class)
        ->arg('$dsn', env('DATABASE_DSN'))
        ->arg('$username', env('DATABASE_USER'))
        ->arg('$password', env('DATABASE_PASSWORD'));


    $services->set(StatRepository::class)
        ->factory([StatRepository::class, 'create']);
    // if (str_starts_with(env('DATABASE_DSN')->string(), 'sqlite:')) {
    //     $services->alias(StatRepository::class, StatRepositorySqlite::class);
    // } else {
    //     $services->alias(StatRepository::class, StatRepositoryMysql::class);
    // }

};
