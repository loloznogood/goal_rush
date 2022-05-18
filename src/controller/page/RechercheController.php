<?php

namespace src\controller\page;

use function FastRoute\TestFixtures\empty_options_cached;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\SearchResultItem;
use src\model\ToolModel;

class RechercheController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Rechercher'=> $this->getContainer()->router->pathFor('recherche.index')
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $pageDAO = $daoFactory->getPageDAO();
        $page = $pageDAO->find('recherche');

        $dataForView = array(
            'filAriane' => $this->filAriane,
            'page'      => $page,
            'footer' => $this->getFooterData(),
            'resultat' => array()
        );

        $searchFlag = False;

        if(isset($request->getParsedBody()['recherche']) && !empty(trim($request->getParsedBody()['recherche'])) ){
            $terme = $request->getParsedBody()['recherche'];
            $dataForView['terme'] = trim($terme);
            $searchFlag = True;
        }
        if(isset($request->getParsedBody()['rechercheHeader']) && !empty(trim($request->getParsedBody()['rechercheHeader'])) ){
            $terme = $request->getParsedBody()['rechercheHeader'];
            $dataForView['terme'] = trim($terme);
            $searchFlag = True;
        }

        if($searchFlag){
            $tool = new ToolModel();
            $terme = $tool->removeAccent($terme);
            $lpDAO = $daoFactory->getLPDAO();
            if( !empty($lpDAO->search($terme)) ){
                foreach($lpDAO->search($terme) as $lp){
                    $r = new SearchResultItem($lp->getIntitule(), $lp->getUrl(), 'produit');
                    $dataForView['resultat']['produit'][] = $r;
                }
            }

            $gaDAO = $daoFactory->getGADAO();
            if( !empty($gaDAO->search($terme)) ){
                foreach($gaDAO->search($terme) as $ga){
                    $r = new SearchResultItem($ga->getIntitule(), $ga->getUrl(), 'produit');
                    $dataForView['resultat']['produit'][] = $r;
                }
            }

            $gpDAO = $daoFactory->getGPDAO();
            if( !empty($gpDAO->search($terme)) ){
                foreach($gpDAO->search($terme) as $gp){
                    $r = new SearchResultItem($gp->getIntitule(), $gp->getUrl(), 'produit');
                    $dataForView['resultat']['produit'][] = $r;
                }
            }

            $serviceDAO = $daoFactory->getServiceDAO();
            if( !empty($serviceDAO->search($terme)) ){
                foreach($serviceDAO->search($terme) as $service){
                    $r = new SearchResultItem($service->getIntitule(), $service->getUrl(), 'service');
                    $dataForView['resultat']['service'][] = $r;
                }
            }

            $actualiteDAO = $daoFactory->getActualiteDAO();
            if( !empty($actualiteDAO->search($terme)) ){
                foreach($actualiteDAO->search($terme) as $a){
                    $r = new SearchResultItem($a->getIntitule(), $a->getUrl(), 'actualité');
                    $dataForView['resultat']['actualite'][] = $r;
                }
            }

            $catalogueDAO = $daoFactory->getCatalogueStandardDAO();
            if( !empty($catalogueDAO->search($terme)) ){
                foreach($catalogueDAO->search($terme) as $c){
                    $url = '#';
                    if($c->getType() == "parGP"){
                        $url = $this->getContainer()->router->pathFor('catalogue-standard.showGP', ['lp'=>$c->getLp(), 'ga'=>$c->getGa(), 'gp'=>$c->getGp()]);
                    }
                    if($c->getType() == "parLP"){
                        $url = $this->getContainer()->router->pathFor('catalogue-standard.showLP', ['id'=>$c->getId(), 'lp'=>$c->getLp()]);
                    }
                    $r = new SearchResultItem($c->getIntitule(), $url, 'catalogue standard');
                    $dataForView['resultat']['catalogue'][] = $r;
                }
            }

            $gpDAOstock = $daoFactory->getGPDAOstock();
            if( !empty($gpDAOstock->search($terme)) ){
                foreach($gpDAOstock->search($terme) as $gpstock){
                    $r = new SearchResultItem($gpstock->getDesignation(),$gpstock->getUrl(), 'stock');
                    $dataForView['resultat']['stock'][] = $r;
                }
                var_dump($r);
            }

        }

        $this->render($response, 'recherche.html.twig', ['data' => $dataForView]);
    }

}