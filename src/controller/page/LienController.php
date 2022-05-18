<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\GA;
use src\model\metier\LP;

class LienController extends PageController
{

    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Produits'=> $this->getContainer()->router->pathFor('lien-produits.index')
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $daoFactory = new DAOFactory($this->getContainer()->db);

        $lienDAO = $daoFactory->getLienDAO();
        
        $pageDAO = $daoFactory->getPageDAO();
        $page = $pageDAO->find("produits");

        $lienCateg = $lienDAO->findAllCateg();
        $lienSousCateg = $lienDAO->findAllSousCateg();
        $dataForView = array(
            'lienCateg'        => $lienCateg,
            'lienSousCateg'    =>$lienSousCateg,
            'page'      => $page,
            'filAriane' => $this->filAriane,
            'footer' => $this->getFooterData()
        );

        
        $this->render($response, 'lien-produits.html.twig', ['data' => $dataForView]);

    }

    

}