<?php


/*
* Les instructions suivantes ont pour but de pallier le fait 
* que l'on ne peut pas modifier la configuration Apache DOCUMENT_ROOT 
* de façon à la faire pointer vers le dossier public.
*/
if(basename($_SERVER['DOCUMENT_ROOT']) != 'public') $_SERVER['DOCUMENT_ROOT'] .= '/public';
if(basename($_SERVER['CONTEXT_DOCUMENT_ROOT']) != 'public') $_SERVER['CONTEXT_DOCUMENT_ROOT'] .= '/public';
/* Fin instructions */


require '../vendor/autoload.php';

use src\controller\ConfigurationController;
use src\model\ConfigurationModel;
use src\controller\ContainerController;
use src\middleware\AdminNotLoggedInMiddleware;
use src\middleware\AdminAlreadyLoggedInMiddleware;
use src\middleware\AdminAuthorizedMiddleware;
use src\middleware\CatalogueStandardExistsMiddleware;


session_start();


//Chargement configurations
$configCtrl = new ConfigurationController();
$config = $configCtrl->checkMode(ConfigurationModel::MODE_DEV);
//$config = $configCtrl->checkMode(ConfigurationModel::MODE_PRODUCTION);

//Creation du site
$app = new \Slim\App([
    'settings' => $config->getSettings()
]);

//Initilalisation du container
$container = $app->getContainer();
$containerCtrl = new ContainerController($container);


/* ROUTES PAGES VISITEURS DU SITE */
// PARTIES
$app->get('/parties', \src\controller\page\PartieController::class . ':all')
    ->setName('partie.index');


$app->run();
