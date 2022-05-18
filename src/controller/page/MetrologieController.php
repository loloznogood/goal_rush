<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;

class MetrologieController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Métrologie'=> $this->getContainer()->router->pathFor('metrologie.index')
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $pageDAO = $daoFactory->getPageDAO();
        $page = $pageDAO->find("metrologie");
        $dataForView = [
            'page' => $page,
            'filAriane' => $this->filAriane,
            'footer' => $this->getFooterData()
        ];
        $this->render($response, 'metrologie.html.twig', ['data' => $dataForView]);
    }
}