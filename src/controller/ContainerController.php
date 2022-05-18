<?php

namespace src\controller;

use \Slim\Container as Container;
use src\controller\DatabaseController as DatabaseController;
use \Slim\Views\TwigExtension as TwigExtension;
use \src\model\dao\DAOFactory as DAOFactory;

class ContainerController
{
    private $container;

    public function __construct(Container $c)
    {
        $this->container = $c;
        $this->initContainer();
    }

    /**
     * Cette methode initilise le container de l'application Slim.
     * On charge toutes les dependences que l'on a besoin en ajoutant
     * une cle tableau $this->container
     */
    private function initContainer()
    {

        //Gestion des vues avec TWIG
        $this->container['view'] = function ($c) {
            $dir = dirname(__DIR__);
            $view = new \Slim\Views\Twig($dir.'/view',[
                //'cache' => $dir.'/tmp/cache'
                'cache' => false
            ]);
            // Instantiate and add Slim specific extension
            $basePath = rtrim(str_ireplace('index.php', '', $c->get('request')->getUri()->getBasePath()), '/');
            $view->addExtension(new TwigExtension($c->get('router'), $basePath));

            return $view;
        };

        //Gestion erreur 404
        $this->container['notFoundHandler'] = function ($c) {
            return function ($request, $response) use ($c) {
                return $c['response']
                    ->withStatus(404)
                    ->withHeader('Content-Type', 'text/html')
                    ->write("Erreur 404 : la page demandée n'existe pas.");
            };
        };

        //Gestion erreur 500
        $this->container['errorHandler'] = function ($c) {
            return function ($request, $response, $exception) use ($c) {
                return $c['response']->withStatus(500)
                    ->withHeader('Content-Type', 'text/html')
                    ->write('Erreur 500 : '.$exception->getMessage());
            };
        };

        //Gestion base de donnees avec PDO
        $this->container['db'] = function ($c) {
            $connectionData = $c['settings']['db'];
            $dbCtrl = DatabaseController::connexion($connectionData);
            return $dbCtrl;
        };

        //Gestion des cookies
        $this->container['cookie'] = function ($c) {
            $cookieCtrl = new CookieController();
            return $cookieCtrl;
        };



    }

}