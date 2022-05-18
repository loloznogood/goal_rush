<?php

namespace src\controller\page;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\dao\DAOFactory;
use src\model\metier\LP;


abstract class PageController
{
    private $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function render(ResponseInterface $response, $file, array $data = []){
        $this->container->view->render($response, $file, $data);
    }

    public function redirect(ResponseInterface $response, $name, array $data = [], array $queryParams = []){
        return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor($name, $data, $queryParams));
    }


    public function getContainer(){
        return $this->container;
    }

    public function getFooterData(){
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $lpDAO = $daoFactory->getLPDAO();
        $lps = $lpDAO->findAll();
        return [
            'produits' => $lps,
            'annee' => date('Y')
        ];
    }

}