<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;

class EntrepriseController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Entreprise'=> $this->getContainer()->router->pathFor('entreprise.index')
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $pageDAO = $daoFactory->getPageDAO();
        $page = $pageDAO->find('entreprise');

        $dataForView = array(
            'filAriane' => $this->filAriane,
            'page'      => $page,
            'footer' => $this->getFooterData()
        );


        $this->render($response, 'entreprise.html.twig', ['data' => $dataForView]);
    }
}