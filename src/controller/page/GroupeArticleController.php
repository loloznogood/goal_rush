<?php

namespace src\controller\page;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\GA;
use src\model\metier\LP;
use src\model\metier\GP;


class GroupeArticleController extends PageController
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

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {
        //Parametres requete HTTP
        $lpNum = $args['lp'];
        $gaNum = $args['ga'];

        //DAO Fcatory
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $lpDAO = $daoFactory->getLPDAO();
        $gaDAO = $daoFactory->getGADAO();
        $gpDAO = $daoFactory->getGPDAO();

        $lp = $lpDAO->findByLp($lpNum);
        $ga = $gaDAO->find([
            'lp' => $lpNum,
            'ga' => $gaNum
        ]);

        //Lignes de produits concernes par le groupe atricle selectionne
        $lpsConcerned = array();
        $lpsC = $lpDAO->findAllConcernedByGa($lpNum, $gaNum);
        foreach($lpsC as $lp){
            $lpsConcerned[] = $lp->getId();
        }

        //Lignes de produits
        $lps = $lpDAO->findAll();


        //Groupes articles
        $gas1 = $gaDAO->findByLp($lp->getLp());
        $gas2 = $gaDAO->findByLpId($lp->getId());
        $gas = array_merge($gas1, $gas2);

        //Groupes produits
        $gps = $gpDAO->findByGa($lpNum, $gaNum);
        

        //Fil d'Arianne
        $this->filAriane[$lp->getIntitule()] = $lp->getUrl();
        $this->filAriane[$ga->getIntitule()] = $ga->getUrl();

        $fabfr = $_SESSION['fabfr'];
        $gpss = $gpDAO->findByLpEdit($lp->getId(), $ga->getGa());
        
        //Donnes passees a la vue
        $dataForView = array(
            "lpsC" => $lpsC,
            "lps" => $lps,
            "ga" => $ga,
            "gas" => $gas,
            'gps' => $gps,
            'fabfr' => $fabfr,
            'gpss' => $gpss,
            'lpNum' => $lpNum,
            'footer' => $this->getFooterData(),
            'filAriane' => $this->filAriane
        );

    


        $this->render($response, 'groupe-article.html.twig', [
            'data'          =>$dataForView
        ]);
    }

}