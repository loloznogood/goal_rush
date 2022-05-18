<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\Actualite;

class ActualiteController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Actualités, salons'=> $this->getContainer()->router->pathFor('actualite.index')
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $actuDAO = $daoFactory->getActualiteDAO();
        $pageDAO = $daoFactory->getPageDAO();
        $page = $pageDAO->find('actus/salons');
        $actus = $actuDAO->findAll();

        $dataForView = array(
            'actus' => $actus,
            'filAriane' => $this->filAriane,
            'page'      => $page,
            'footer' => $this->getFooterData()
        );


        $this->render($response, 'actualite.html.twig', ['data' => $dataForView]);

    }

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $actuDAO = $daoFactory->getActualiteDAO();
        $pageDAO = $daoFactory->getPageDAO();
        $page = $pageDAO->find('actus/salons');
        $actu = $actuDAO->find($id);

        $this->filAriane[$actu->getIntitule()] = $actu->getUrl();

        $dataForView = array(
            'actu' => $actu,
            'filAriane' => $this->filAriane,
            'page'      => $page,
            'footer' => $this->getFooterData()
        );


        $this->render($response, 'actualite-show.html.twig', ['data' => $dataForView]);

    }
}