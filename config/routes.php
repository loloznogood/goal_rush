<?php

use Slim\App;

return function (App $app) {
    $app->get('/', \App\Action\HomeAction::class)->setName('home');
    $app->get('/parties', [\App\Controller\PartieController::class, 'parties'])->setName('home');
    $app->post('/teams', \App\Action\TeamCreateAction::class);
    // Example route
    // Please note how $view is created from the request
    $app->get('/hello/{name}', function ($request, $response, $args) {
        $view = \Slim\Views\Twig::fromRequest($request);
        return $view->render($response, 'hello.twig', [
            'name' => $args['name']
        ]);
    });

};