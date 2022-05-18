<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\Service;
use src\model\metier\GA;
use src\model\metier\LP;

class PartieController extends PageController
{
    public function __construct($container)
    {
        parent::__construct($container);
    }

    public function all(RequestInterface $request, ResponseInterface $response, array $args) {
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $partieDao = $daoFactory->getPartieDao();
        $dataForView = [
            'partie' =>  $partieDao->findAll(),
        ];
        $this->render($response, 'partie.html.twig', ['data' => print_r($dataForView)]);
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $daoFactory = new DAOFactory($this->getContainer()->db);

        $lienDAO = $daoFactory->getLienDAO();
        $gaDAO = $daoFactory->getGADAO();
        $serviceDAO = $daoFactory->getServiceDAO();
        $pageDAO = $daoFactory->getPageDAO();
        $actuDAO = $daoFactory->getActualiteDAO();
        $lpDAO = $daoFactory->getLPDAO();


        $actusCarousel = $actuDAO->findAllForCarousel();



        $services = $serviceDAO->findAll();
        $page = $pageDAO->find("accueil");

        $lienCateg = $lienDAO->findAllCateg();
        $lienSousCateg = $lienDAO->findAllSousCateg();

        $dataForView = array(
            'lienCateg'        => $lienCateg,
            'lienSousCateg'    =>$lienSousCateg,
            'services'  => $services,
            'page'      => $page,
            'actusCarousel' => $actusCarousel,
            'LP' => array(),
            'footer' => $this->getFooterData()
        );

        $lps = $lpDAO->findAll();

        //Nombre de GA a afficher par LP
        $NB_GA_PER_LP = 8;


        foreach ($lps as $lp){
            $gas1 = $gaDAO->findByLp($lp->getLp());
            $gas2 = $gaDAO->findByLpId($lp->getId());
            $gas = array_merge($gas1, $gas2);
            $gas = array_slice($gas, 0, $NB_GA_PER_LP-1);

            $dataForView['LP'][] = [
                'lp' => $lp,
                'gas'=> $gas
            ];
        }

        $this->render($response, 'accueil.html.twig', ['data' => $dataForView]);
    }
}