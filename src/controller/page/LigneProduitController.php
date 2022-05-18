<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\GA;
use src\model\metier\LP;

class LigneProduitController extends PageController
{

    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Lignes de produit'=> $this->getContainer()->router->pathFor('ligne-produit.index')
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $daoFactory = new DAOFactory($this->getContainer()->db);

        $lpDAO = $daoFactory->getLPDAO();
        $gaDAO = $daoFactory->getGADAO();

        $pageDAO = $daoFactory->getPageDAO();
        $page = $pageDAO->find("produits");

        $dataForView = array(
            'LP'        => array(),
            'page'      => $page,
            'filAriane' => $this->filAriane,
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


        $this->render($response, 'lignes-produits.html.twig', ['data' => $dataForView]);

    }

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $id = $args['id'];
        $lp = $args['lp'];

        $daoFactory = new DAOFactory($this->getContainer()->db);

        $lpDAO = $daoFactory->getLPDAO();
        $gaDAO = $daoFactory->getGADAO();


        $lp = $lpDAO->find($id);
        $gas1 = $gaDAO->findByLp($lp->getLp());
        $gas2 = $gaDAO->findByLpId($lp->getId());
        $gas = array_merge($gas1, $gas2);
        $lps = $lpDAO->findAll();

        $_SESSION['fabfr'] = $lp->getFabfr();

        $this->filAriane[$lp->getIntitule()] = $lp->getUrl();
        
        $dataForView = array(
            'lp' => $lp,
            'gas' => $gas,
            'lps' => $lps,
            'filAriane'     => $this->filAriane,
            'footer' => $this->getFooterData()
        );

        $this->render($response, 'ligne-produit.html.twig', ['data' => $dataForView]);
        

    }

}