<?php
use DI\ContainerBuilder;
use Slim\App;

require_once __DIR__ . '/../vendor/autoload.php';

$containerBuilder = new ContainerBuilder();

// Set up settings
$containerBuilder->addDefinitions(__DIR__ . '/container.php');

// Build PHP-DI Container instance
$container = $containerBuilder->build();

// Create App instance
$app = $container->get(App::class);

// Create Twig
$twig = \Slim\Views\Twig::create(__DIR__ . '/../templates',
    ['cache' => __DIR__ . '/../cache']);

// Add Twig-View Middleware
$app->add(\Slim\Views\TwigMiddleware::create($app, $twig));

// Set Base Path
$app->setBasePath('/goal_rush');

// Register routes
(require __DIR__ . '/routes.php')($app);

// Register middleware
(require __DIR__ . '/middleware.php')($app);

return $app;

?>