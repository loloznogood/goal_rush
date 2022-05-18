<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\CatalogueStandard;

class CatalogueStandardController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Catalogues Standards'=> $this->getContainer()->router->pathFor('catalogue-standard.index')
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $pageDAO = $daoFactory->getPageDAO();
            $csDAO = $daoFactory->getCatalogueStandardDAO();
            $lpDAO = $daoFactory->getLPDAO();

            $page = $pageDAO->find('catalogue-standard');

            $csIntegral = $csDAO->findCatalogueStandardEntier();

            $dataForView = array(
                'CS'        => array(),
                'page'      => $page,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData(),
                'csIntegral' =>$csIntegral
            );

            $lps = $lpDAO->findAll();

            //Nombre de catalogue standard a afficher par LP
            $NB_CS_PER_LP = 5;


            foreach ($lps as $lp){

                if(is_null($lp->getLp())){
                    $csLp = $csDAO->findByLpId($lp->getId());
                }else{
                    $csLp = $csDAO->findByLp($lp->getLp());
                }


                $cssLp1 = $csDAO->findAllWithLp($lp->getLp());
                $cssLp2 = $csDAO->findAllWithLpId($lp->getId());

                $cssLp = array_merge($cssLp1, $cssLp2);
                $cssLp = array_slice($cssLp, 0, $NB_CS_PER_LP-1);

                $dataForView['CS'][] = [
                    'lp' => $lp,
                    'csLp' => $csLp,
                    'cssLp'=> $cssLp
                ];
            }


            $this->render($response, 'catalogue-standard-index.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            if(isset($args['ga'])){
                $ga = $args['ga'];
            }
            if(isset($args['gp'])){
                $gp = $args['gp'];
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $csDAO = $daoFactory->getCatalogueStandardDAO();
            $lpDAO = $daoFactory->getLPDAO();
            $gpDAO = $daoFactory->getLPDAO();

            $dataForView = array(
                'lp'        => null,
                'csLp'        => null,
                'cssLp' => null,
                'csGp'      => null,
                'csConnexe' => array(),
                'lpConnexe' => null,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            );


            //CS LP
            if(isset($args['id'])){
                $id = $args['id'];
                $lpNum = null;
                if(isset($args['lp']) && (trim($args['lp'])!='' ) ){
                    $lpNum = $args['lp'];
                }
                $lp = $lpDAO->find($id);

                $csLp = $csDAO->findByLpId($id);
                $cssLp = null;
                if(is_null($lpNum)){
                    $cssLp = $csDAO->findAllWithLpId($id);
                }
                else{
                    $cssLp = $csDAO->findAllWithLp($lpNum);
                }
                $dataForView['lp'] = $lp;
                $dataForView['csLp'] = $csLp;
                $dataForView['cssLp'] = $cssLp;
                $this->filAriane[$lp->getIntitule()] = $this->getContainer()->router->pathFor('catalogue-standard.showLP',['id'=>$lp->getId(), 'lp'=>$lp->getLp()]);
            }

            //CS GP
            if(isset($args['lp']) && isset($args['ga']) && isset($args['gp'])){
                $lpNum = $args['lp'];
                $ga = $args['ga'];
                $gp = $args['gp'];
                $csGp = $csDAO->findByGp($lpNum, $ga, $gp);
                $dataForView['csGp'] = $csGp;

                $csIntegral = $csDAO->findCatalogueStandardEntier();
                $csLp = $csDAO->findByLp($lpNum);
                $lpConnexe = $lpDAO->findByLp($lpNum);
                $dataForView['csConnexe'][] = $csIntegral;
                $dataForView['csConnexe'][] = $csLp;
                if(!is_null($lpConnexe)){
                    $dataForView['lpConnexe'] = $lpConnexe;
                }
                $this->filAriane[$csGp->getIntitule()] = $this->getContainer()->router->pathFor('catalogue-standard.showGP',['lp'=>$lpNum, 'ga'=>$ga, 'gp'=>$gp]);

            }

            $dataForView['filAriane'] = $this->filAriane;
            $this->render($response, 'catalogue-standard-show.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){
            echo $e->getMessage();
        }
    }
}