<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\LP;
use src\model\metier\GA;

class AdminGAController extends PageController
{
    const DROITS = 1;

    private $filAriane;
    /**
     * AdminLPController constructor.
     */
    public function __construct($container)
    {
        parent::__construct($container);
        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Menu administrateur'=> $this->getContainer()->router->pathFor('admin/home.index'),
            'Gestion des groupes articles'=> $this->getContainer()->router->pathFor('admin/ga.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $gaDAO = $daoFactory->getGADAO();
            $gas = $gaDAO->findAll();
            $gasEdited = $gaDAO->findAllEdited();
            $dataForView = [
                'gas' => $gas,
                'gasEdited' => $gasEdited,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-ga-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }
    }

    public function indexByLpWebService(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            // La methode getParsedBody() convertit le JSON recu en tableau associatif (pas besoin de json_decode())
            $rcvData = $request->getParsedBody();
            $lp = $rcvData['lp'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $gaDAO = $daoFactory->getGADAO();
            $gas = $gaDAO->findByLp($lp);

            $sndData = array();
            foreach ($gas as $ga) {
                $sndData[] = $ga->toArray();
            }

            return $response->withStatus(200)->withJson($sndData);
        }
        catch(\Exception $e){
            return $response->withStatus(500, $e->getMessage());
        }


    }
    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAllTrueLP();
            $this->filAriane['Créer une nouveau groupe articles'] = $this->getContainer()->router->pathFor('admin/ga.create');
            $dataForView = [
                'lps' => $lps,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-ga-create.html.twig', [
                'data' => $dataForView
            ]);

        }catch(\Exception $e){

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAllTrueLP();
            $dataForView = [
                'lps' => $lps,
                'error' => 'Un problème est survenu lors du traitement de cette page.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-ga-index.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer une nouveau groupe articles'] = $this->getContainer()->router->pathFor('admin/ga.create');

            $lpNum = $request->getParsedBody()['lp'];
            $gaNum = $request->getParsedBody()['ga'];
            $intitule = $request->getParsedBody()['intitule'];

            if(empty(trim($lpNum)) || intval($lpNum)==0){
                throw new \Exception("Numéro de LP incorrect.");
            }
            if(empty(trim($gaNum)) || intval($gaNum)==0){
                throw new \Exception("Numéro de GA incorrect.");
            }
            if(empty(trim($intitule))){
                throw new \Exception("Intitulé incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();

            if(is_null($lpDAO->findByLp($lpNum))){
                throw new \Exception("Le numéro de LP ne correspond à aucune ligne de produit existante.");
            }

            $gaDAO = $daoFactory->getGADAO();
            $ga = new GA($lpNum, $gaNum, $intitule);
            $gaDAO->create($ga);
            return $this->redirect($response, "admin/ga.index");

        }catch(\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAllTrueLP();
            $dataForView = [
                'error' => $e->getMessage(),
                'ga' => [
                    'lp'        => $lpNum,
                    'ga'        => $gaNum,
                    'intitule'  => $intitule
                ],
                'lps' => $lps,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-ga-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAO();
            $gas = $gaDAO->findAll();
            $gasEdited = $gaDAO->findAllEdited();
            $ga = $gaDAO->find(['lp'=>$lpNum,'ga'=>$gaNum]);
            $gps = $gpDAO->findByGa($lpNum, $gaNum);
            if(!empty($gps) ){
                throw new \Exception("Impossible de supprimer le groupe article car il contient des groupes produit. Vous devez d'abord supprimer ces groupes produit possédant un #LP égal à ".$lpNum." et un #GA égal à ".$gaNum.".");
            }
            $gaDAO->delete($ga);
            return $this->redirect($response, "admin/ga.index");


        }catch(\Exception $e){
            $dataForView = [
                'gas' => $gas,
                'gasEdited' => $gasEdited,
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-ga-index.html.twig', ['data' => $dataForView]);
        }
    }

    public function deleteSave(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $gaDAO = $daoFactory->getGADAO();
            $gas = $gaDAO->findAll();
            $gasEdited = $gaDAO->findAllEdited();
            $ga = $gaDAO->find(['lp'=>$lpNum,'ga'=>$gaNum]);
            $gaDAO->deleteSave($ga);
            return $this->redirect($response, "admin/ga.index");


        }catch(\Exception $e){
            $dataForView = [
                'gas' => $gas,
                'gasEdited' => $gasEdited,
                'error' => 'La sauvegarde du groupe article  n\'a pas été supprimé.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-ga-index.html.twig', ['data' => $dataForView]);
        }
    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $this->filAriane['Modifier un groupe articles'] = $this->getContainer()->router->pathFor('admin/ga.edit', ['lp'=>$lpNum, 'ga'=>$gaNum]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $lps = $lpDAO->findAllTrueLP();
            $ga = $gaDAO->find(['lp'=>$lpNum, 'ga'=>$gaNum]);

            $dataForView = [
                'lps'   => $lps,
                'ga'    =>$ga,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-ga-edit.html.twig', ['data'  => $dataForView]);
        }catch (\Exception $e){
            $dataForView = [
                'error' => "Une erreur s'est produite lors de traitement de cette page.",
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-ga-edit.html.twig', ['data'  => $dataForView]);
        }

    }


    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $this->filAriane['Modifier un groupe articles'] = $this->getContainer()->router->pathFor('admin/ga.edit', ['lp'=>$lpNum, 'ga'=>$gaNum]);
            $lpNum2 = $request->getParsedBody()['lpSave'];
            $lpNumNew = $request->getParsedBody()['lp'];
            $gaNumNew = $request->getParsedBody()['ga'];
            $intitule = $request->getParsedBody()['intitule'];

            if($lpNum != $lpNum2 || empty(trim($lpNum2))){
                throw new \Exception("Numéro de LP incorrect.");
            }

            if(empty(trim($lpNumNew)) || intval($lpNumNew)==0){
                throw new \Exception("Numéro de LP incorrect.");
            }

            if(empty(trim($gaNumNew)) || intval($gaNumNew)==0){
                throw new \Exception("Numéro de GA incorrect.");
            }
            if(empty(trim($intitule))){
                throw new \Exception("Intitulé incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gaOld = $gaDAO->find(['lp'=>$lpNum, 'ga'=>$gaNum]);
            $gaNew = new GA($lpNumNew, $gaNumNew, $intitule);
            $gaDAO->updateTotal($gaOld, $gaNew);

            $lps = $lpDAO->findAllTrueLP();

            return $this->redirect($response, "admin/ga.edit",['lp'=>$lpNumNew, 'ga'=>$gaNumNew]);


        }catch (\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAllTrueLP();
            $dataForView = [
                'error' => $e->getMessage(),
                'ga' => [
                    'lp' => $lpNum,
                    'ga' => $gaNum,
                    'intitule' => $intitule,
                ],
                'lps' => $lps,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-ga-edit.html.twig', ['data'  => $dataForView]);
        }

    }


}