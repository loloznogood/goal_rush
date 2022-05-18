<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;

class LegalController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Informations légales'=> $this->getContainer()->router->pathFor('legal.index')
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $pageDAO = $daoFactory->getPageDAO();
        $page = $pageDAO->find('legal');

        $dataForView = array(
            'filAriane' => $this->filAriane,
            'page'      => $page,
            'footer' => $this->getFooterData()
        );


        $this->render($response, 'legal.html.twig', ['data' => $dataForView]);
    }
}