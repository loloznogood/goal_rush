<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\GP;

class GroupeProduitController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Lignes de produit' => $this->getContainer()->router->pathFor('ligne-produit.index')
        ];
    }

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {

        //Parametres requete HTTP
        $lpNum = $args['lp'];
        $gaNum = $args['ga'];
        $gpNum = $args['gp'];

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

        $gp = $gpDAO->find([
            'lp' => $lpNum,
            'ga' => $gaNum,
            'gp' => $gpNum
        ]);

        //Lignes de produits concernes par la groupe atricle selectionne
        $lpsConcerned = array();
        $lpsC = $lpDAO->findAllConcernedByGa($lpNum, $gaNum);
        foreach ($lpsC as $lp) {
            $lpsConcerned[] = $lp->getId();
        }

        //Groupes articles concernes par la groupe atricle du gp selectionne
        $gasConcerned = array($ga->getIntitule());

        //Lignes de produits
        $lps = $lpDAO->findAll();

        //Groupes articles
        $gas1 = $gaDAO->findByLp($lp->getLp());
        $gas2 = $gaDAO->findByLpId($lp->getId());
        $gas = array_merge($gas1, $gas2);

        //Groupes produits édités
        $gpsEdited = $gpDAO->findAllEdited();

        //Fiche technique
        $fichetec = "https://jumo-france.fr/public/upload/pdf/" . $gp->getNumProduit() . ".pdf";
        $file_headers = @get_headers($fichetec);
        if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {
            $file_exists = false;
        } else {
            $file_exists = true;
        }

        //Fil d'Arianne
        $this->filAriane[$lp->getIntitule()] = $lp->getUrl();
        $this->filAriane[$ga->getIntitule()] = $ga->getUrl();
        $this->filAriane[$gp->getIntitule()] = $gp->getUrl();

        //Donnes passees a la vue
        $dataForView = array(
            "lpsConcerned" => $lpsConcerned,
            "gasConcerned" => $gasConcerned,
            "lps" => $lps,
            "gas" => $gas,
            "gpsEdited" => $gpsEdited,
            'gp' => $gp,
            'fiche' => $file_exists,
            'footer' => $this->getFooterData(),
            'filAriane'     => $this->filAriane,
        );

        $this->render($response, 'groupe-produit.html.twig', [
            'data'          => $dataForView
        ]);
    }
}
