<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\LP;
use src\model\metier\CatalogueStock;
use src\model\metier\GA;
use src\model\metier\GPstock;
use src\model\metier\GP;

class CatalogueStockController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Catalogue Stock'=> $this->getContainer()->router->pathFor('catalogue-stock.index')
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $daoFactory = new DAOFactory($this->getContainer()->db);

        $lpDAO = $daoFactory->getLPDAO();
        $gaDAO = $daoFactory->getGADAO();

        $pageDAO = $daoFactory->getPageDAO();
        $page = $pageDAO->find("catalogue-stock");

        

        $dataForView = array(
            'LP'        => array(),
            'page'      => $page,
            'filAriane' => $this->filAriane,
            'footer' => $this->getFooterData()
        );

        $lps = $lpDAO->findAll();

        //Nombre de GA a afficher par LP
        $NB_GA_PER_LP = 3;


        foreach ($lps as $lp){
            $gas1 = $gaDAO->findByLp($lp->getLp());
            $gas2 = $gaDAO->findByLpId($lp->getId());
            $gas = array_merge($gas1, $gas2);
            $gas = array_slice($gas, 0, $NB_GA_PER_LP-1);
            $gaexist1 = $gaDAO->findByLp($lp->getLP());
           // $gaexist2 = $gaDAO->findByLpId($lp->getId());
            $gaexist2 = $gaDAO->findGaStock($lp->getLp());
            $gaexist = array_merge($gaexist2);
            $gaexist = array_slice($gaexist, 0, $NB_GA_PER_LP-1);

            $dataForView['LP'][] = [
                'lp' => $lp,
                'gas'=> $gas,
                'gaexist' => $gaexist
            ];
        }


        $this->render($response, 'catalogue-stock-index.html.twig', ['data' => $dataForView]);

    }

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $NB_GA_PER_LP = 10;

        $id = $args['id'];
        $lp = $args['lp'];

        $daoFactory = new DAOFactory($this->getContainer()->db);

        $lpDAO = $daoFactory->getLPDAO();
        $gaDAO = $daoFactory->getGADAO();


        $lp = $lpDAO->find($id);
        $gas1 = $gaDAO->findbylp($lp->getLp());
        $gas2 = $gaDAO->findByLpId($lp->getId());
        $gas = array_merge($gas1, $gas2);
        $lps = $lpDAO->findAll();
        $gaexist2 = $gaDAO->findGaStock($lp->getLp());
        $gaexist = array_merge($gaexist2);
        $gaexist = array_slice($gaexist, 0, $NB_GA_PER_LP-1);
        $lpexist = $lpDAO->findLpStock();
        $this->filAriane[$lp->getIntitule()] = $lp->getUrlStock();

        $dataForView = array(
            'lp' => $lp,
            'gas' => $gas,
            'gaexist' => $gaexist,
            'lpexist' => $lpexist,
            'lps' => $lps,
            'filAriane'     => $this->filAriane,
            'footer' => $this->getFooterData()
        );

        $this->render($response, 'catalogue-stock-show.html.twig', ['data' => $dataForView]);
        

    }

    public function showGA(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $NB_GA_PER_LP = 6;
        //Parametres requete HTTP
        $lpNum = $args['lp'];
        $gaNum = $args['ga'];

        //DAO Fcatory
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $lpDAO = $daoFactory->getLPDAO();
        $gaDAO = $daoFactory->getGADAO();
        $gpDAO = $daoFactory->getGPDAOstock();
        $prodDao = $daoFactory->getGPDAO();

        $lp = $lpDAO->findByLp($lpNum);
        $ga = $gaDAO->find([
            'lp' => $lpNum,
            'ga' => $gaNum
        ]);

        //Lignes de produits concernes par la groupe atricle selectionne
        $lpsConcerned = array();
        $lpsC = $lpDAO->findAllConcernedByGa($lpNum, $gaNum);
        foreach($lpsC as $lp){
            $lpsConcerned[] = $lp->getId();
        }

        //Lignes de produits
        $lps = $lpDAO->findAll();
        $lpexist = $lpDAO->findLpStock();

        //Groupes articles
        $gas1 = $gaDAO->findbylp($lp->getLp());
        $gas2 = $gaDAO->findByLpId($lp->getId());
        $gas = array_merge($gas1, $gas2);
        $gaexist2 = $gaDAO->findGaStock($lp->getLp());
        $gaexist = array_merge($gaexist2);
        $gaexist = array_slice($gaexist, 0, $NB_GA_PER_LP-1);


        $gps = $gpDAO->findByGa($lpNum, $gaNum);

        //Groupes produits
        $prod = $prodDao->findByGa($lpNum, $gaNum);

        //Fil d'Arianne
        $this->filAriane[$lp->getIntitule()] = $lp->getUrlstock();
        $this->filAriane[$ga->getIntitule()] = $ga->getUrlstock();

        //Donnes passees a la vue
        $dataForView = array(
            "lpsConcerned" => $lpsConcerned,
            "lps" => $lps,
            "ga" => $ga,
            "gas" => $gas,
            'gaexist' => $gaexist,
            'lpexist' => $lpexist,
            "prod" => $prod,
            'gps' => $gps,
            'footer' => $this->getFooterData(),
            'filAriane' => $this->filAriane
        );

        $this->render($response, 'catalogue-stock-GA.html.twig', [
            'data'          =>$dataForView
        ]);
    }

    public function showGP(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $NB_GP_PER_GA = 6;
        //Parametres requete HTTP
        $lpNum = $args['lp'];
        $gaNum = $args['ga'];
        $gpNum = $args['gp'];
        $TnNum = $args['tn'];


        //DAO Fcatory
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $lpDAO = $daoFactory->getLPDAO();
        $gaDAO = $daoFactory->getGADAO();
        $gpDAO = $daoFactory->getGPDAOstock();
        $prodDao = $daoFactory->getGPDAO();

        $lp = $lpDAO->findByLp($lpNum);
        $ga = $gaDAO->find([
            'lp' => $lpNum,
            'ga' => $gaNum
        ]);

        //$tnok = $gpDAO->findByTn($tn->getTn());

        $gp = $gpDAO->find([
            'tn' => $TnNum,
            'lp' => $lpNum,
            'ga' => $gaNum,
            'gp' => $gpNum
        ]);

        //Lignes de produits concernes par la groupe atricle selectionne
        $lpsConcerned = array();
        $lpsC = $lpDAO->findAllConcernedByGa($lpNum, $gaNum);
        foreach($lpsC as $lp){
            $lpsConcerned[] = $lp->getId();
        }

        //Groupes articles concernes par la groupe atricle du gp selectionne
        $gasConcerned = array($ga->getIntitule());

        // produit concernés
        $prod = $prodDao->find([
            'lp' => $lpNum,
            'ga' => $gaNum,
            'gp' => $gpNum
        ]);
        //Lignes de produits
        $lps = $lpDAO->findAll();

        //Groupes articles
        $gas1 = $gaDAO->findByLp($lp->getLp());
        $gas2 = $gaDAO->findByLpId($lp->getId());
        $gas = array_merge($gas1, $gas2);
        $gaexist2 = $gaDAO->findGaStock($lp->getLp());
        $gaexist = array_merge($gaexist2);
        $gps = $gpDAO->findByGa($lpNum, $gaNum);

        //Fil d'Arianne
        $this->filAriane[$lp->getIntitule()] = $lp->getUrlstock();
        $this->filAriane[$ga->getIntitule()] = $ga->getUrlstock();
        $this->filAriane[$gp->getDesignation()] = $gp->getUrlstock();

        $lpexist = $lpDAO->findLpStock();

        //Donnes passees a la vue
        $dataForView = array(
            "lpsConcerned" => $lpsConcerned,
            "gasConcerned" => $gasConcerned,
            "lps" => $lps,
            "gas" => $gas,
            'prod' => $prod,
            'gaexist' => $gaexist,
            'lpexist' => $lpexist,
            'gp' => $gp,
            'gps' => $gps,
            'footer' => $this->getFooterData(),
            'filAriane'     => $this->filAriane
        );

        $gptest = $gpDAO->findAll();

       // var_dump($gp->getTn());


        $this->render($response, 'catalogue-stock-GP.html.twig', [
            'data'          =>$dataForView
        ]);

    }
}


?>