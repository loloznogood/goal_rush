<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\LP;
use src\model\metier\GA;
use src\model\metier\GP;

class AdminGPController extends PageController
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
            'Gestion des groupes produit'=> $this->getContainer()->router->pathFor('admin/gp.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $gpDAO = $daoFactory->getGPDAO();
            $gps = $gpDAO->findAll();
            $gpsEdited = $gpDAO->findAllEdited();
            $dataForView = [
                'gps' => $gps,
                'gpsEdited' => $gpsEdited,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-gp-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }
    }

    public function indexByGaWebService(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            // La methode getParsedBody() convertit le JSON recu en tableau associatif (pas besoin de json_decode())
            $rcvData = $request->getParsedBody();
            $lp = $rcvData['lp'];
            $ga = $rcvData['ga'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $gpDAO = $daoFactory->getGPDAO();
            $gps = $gpDAO->findByGa($lp, $ga);

            $sndData = array();
            foreach ($gps as $gp) {
                $sndData[] = $gp->toArray();
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
            $dataForView = [
                'lps' => $lps,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->filAriane['Créer un nouveau groupe produit'] = $this->getContainer()->router->pathFor('admin/gp.create');
            $this->render($response, 'admin-gp-create.html.twig', ['data' => $dataForView]);

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

            $this->render($response, 'admin-gp-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer un nouveau groupe produit'] = $this->getContainer()->router->pathFor('admin/gp.create');
            $lpNum = $request->getParsedBody()['lp'];
            $gaNum = $request->getParsedBody()['ga'];
            $gpNum = $request->getParsedBody()['gp'];
            $intitule = htmlspecialchars($request->getParsedBody()['intitule'], ENT_HTML5, "UTF-8");
            $description1 = $request->getParsedBody()['description1'];
            $description2 = htmlspecialchars($request->getParsedBody()['description2'], ENT_HTML5, "UTF-8");
            $namescarac = htmlspecialchars($request->getParsedBody()['namescarac'], ENT_HTML5, "UTF-8");
            $carac = htmlspecialchars($request->getParsedBody()['carac'], ENT_HTML5, "UTF-8");
            $typelabel = htmlspecialchars($request->getParsedBody()['typelabel'], ENT_HTML5, "UTF-8");
            $description1 = html_entity_decode($description1);
            $description1 = strip_tags($description1);

            if(empty(trim($lpNum)) || intval($lpNum)==0){
                throw new \Exception("Numéro de LP incorrect.");
            }
            if(empty(trim($gaNum)) || intval($gaNum)==0){
                throw new \Exception("Numéro de GA incorrect.");
            }
            if(empty(trim($gpNum)) || intval($gpNum)==0){
                throw new \Exception("Numéro de GP incorrect.");
            }
            if(empty(trim($intitule))){
                throw new \Exception("Intitulé incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAO();

            if(is_null($lpDAO->findByLp($lpNum))){
                throw new \Exception("Le numéro de LP ne correspond à aucune ligne de produit existante.");
            }

            if(is_null($gaDAO->find(['lp'=>$lpNum, 'ga'=> $gaNum]))){
                throw new \Exception("Le couple de valeurs (#LP, #GA) ne correspond à aucun groupe article existant.");
            }

            if(!is_null($gpDAO->find(['lp'=>$lpNum, 'ga'=> $gaNum, 'gp'=>$gpNum]))){
                throw new \Exception("Le groupe produit correspondant au couple de valeurs (#LP, #GA, #GP) existe déjà.");
            }

            $gp = new GP($lpNum, $gaNum, $gpNum, $intitule, $description1, $description2,$namescarac,$carac,$typelabel,0);
            $gpDAO->create($gp);
            return $this->redirect($response, "admin/gp.index");

        }catch(\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $lps = $lpDAO->findAllTrueLP();
            $gas = $gaDAO->findByLp($lpNum);
            $dataForView = [
                'error' => $e->getMessage(),
                'gp' => [
                    'lp'        => $lpNum,
                    'ga'        => $gaNum,
                    'gp'        => $gpNum,
                    'intitule'  => $intitule,
                    'description1' => htmlspecialchars_decode($description1, ENT_HTML5),
                    'description2' => htmlspecialchars_decode($description2, ENT_HTML5),
                    'namescarac' => htmlspecialchars_decode($namescarac, ENT_HTML5),
                    'carac' => htmlspecialchars_decode($carac, ENT_HTML5),
                    'typelabel' => htmlspecialchars_decode($typelabel, ENT_HTML5)
                ],
                'lps' => $lps,
                'gas' => $gas,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-gp-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $gpNum = $args['gp'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $gpDAO = $daoFactory->getGPDAO();
            $gps = $gpDAO->findAll();
            $gpsEdited = $gpDAO->findAllEdited();
            $gp = $gpDAO->find(['lp'=>$lpNum,'ga'=>$gaNum,'gp'=>$gpNum]);
            $gpDAO->delete($gp);
            return $this->redirect($response, "admin/gp.index");


        }catch(\Exception $e){
            $dataForView = [
                'gps' => $gps,
                'gpsEdited' => $gpsEdited,
                'error' => 'Le groupe produit  n\'a pas été supprimé.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-gp-index.html.twig', ['data' => $dataForView]);
        }
    }

    public function deleteSave(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $gpNum = $args['gp'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $gpDAO = $daoFactory->getGPDAO();
            $gps = $gpDAO->findAll();
            $gpsEdited = $gpDAO->findAllEdited();
            $gp = $gpDAO->find(['lp'=>$lpNum,'ga'=>$gaNum,'gp'=>$gpNum]);
            $gpDAO->deleteSave($gp);
            return $this->redirect($response, "admin/gp.index");


        }catch(\Exception $e){
            $dataForView = [
                'gps' => $gps,
                'gpsEdited' => $gpsEdited,
                'error' => 'La sauvegarde du groupe produit  n\'a pas été supprimé.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-gp-index.html.twig', ['data' => $dataForView]);
        }
    }


    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $gpNum = $args['gp'];
            $this->filAriane['Modifier un groupe produit'] = $this->getContainer()->router->pathFor('admin/gp.edit', ['lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAO();
            $lppAll = $lpDAO->findAllPersonalizedLP();
            $lpps = $lpDAO->findAllPersonalizedLPByGP($lpNum, $gaNum, $gpNum);
            $lps = $lpDAO->findAllTrueLP();
            $gas = $gaDAO->findByLp($lpNum);
            $gp = $gpDAO->findNoArray(['lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);

           // var_dump($lpps);

            //var_dump($lppAll);
            $dataForView = [
                'lps'   => $lps,
                'lpps'  => $lpps,
                'lppAll'  => $lppAll,
                'gas'   => $gas,
                'gp'    => $gp,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-gp-edit.html.twig', ['data'  => $dataForView]);
        }catch (\Exception $e){
            $dataForView = [
              'error' => "Une erreur s'est produite lors de traitement de cette page.",
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-gp-edit.html.twig', ['data'  => $dataForView]);
        }

    }


    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $gpNum = $args['gp'];
            $this->filAriane['Modifier un groupe produit'] = $this->getContainer()->router->pathFor('admin/gp.edit', ['lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);
            $lpNum2 = $request->getParsedBody()['lpSave'];
            $gaNum2 = $request->getParsedBody()['gaSave'];
            $lpNumNew = $request->getParsedBody()['lp'];
            $gaNumNew = $request->getParsedBody()['ga'];
            $gpNumNew = $request->getParsedBody()['gp'];
            $intitule = htmlspecialchars($request->getParsedBody()['intitule'], ENT_HTML5, "UTF-8");
            $description1 = $request->getParsedBody()['description1'];
            $description2 = htmlspecialchars($request->getParsedBody()['description2'], ENT_HTML5, "UTF-8");
            $namescarac = htmlspecialchars($request->getParsedBody()['namescarac'], ENT_HTML5, "UTF-8");
            $carac = htmlspecialchars($request->getParsedBody()['carac'], ENT_HTML5, "UTF-8");
            $typelabel = htmlspecialchars($request->getParsedBody()['typelabel'], ENT_HTML5, "UTF-8");
            $lppIds = $request->getParsedBody()['lpPerso'];
            $description1 = html_entity_decode($description1);
            $description1 = strip_tags($description1);

            if($lpNum != $lpNum2 || empty(trim($lpNum2))){
                throw new \Exception("Numéro de LP incorrect.");
            }

            if(empty(trim($lpNumNew)) || intval($lpNumNew)==0){
                throw new \Exception("Numéro de LP incorrect.");
            }

            if($gaNum != $gaNum2 || empty(trim($gaNum2))){
                throw new \Exception("Numéro de GA incorrect.");
            }

            if(empty(trim($gaNumNew)) || intval($gaNumNew)==0){
                throw new \Exception("Numéro de GA incorrect.");
            }

            if(empty(trim($gpNumNew)) || intval($gpNumNew)==0){
                throw new \Exception("Numéro de GP incorrect.");
            }
            if(empty(trim($intitule))){
                throw new \Exception("Intitulé incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $gpDAO = $daoFactory->getGPDAO();
            $gpOld = $gpDAO->find(['lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);
            $automatique = $gpOld->getAutomatique();
            $gpNew = new GP($lpNumNew, $gaNumNew, $gpNumNew, $intitule, $description1, $description2,$namescarac,$carac,$typelabel,$automatique);

            $gpDAO->deleteFromPersonlizedLP($gpOld);
            $lpps = [];
            foreach($lppIds as $id){
                if($id != '-1'){
                    $lpps[] = $lpDAO->find($id);
                    $gpDAO-> addToPersonnalizedLP($gpNew, $id);
                }
            }

            $gpDAO->update($gpNew);

            $lps = $lpDAO->findAllTrueLP();

            return $this->redirect($response, "admin/gp.edit",['lp'=>$lpNumNew, 'ga'=>$gaNumNew,  'gp'=>$gpNumNew]);


        }catch (\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $lps = $lpDAO->findAllTrueLP();
            $lppAll = $lpDAO->findAllPersonalizedLP();
            $gas = $gaDAO->findByLp($lpNum);
            $dataForView = [
                'error' => $e->getMessage(),
                'gp' => [
                    'lp'        => $lpNum,
                    'ga'        => $gaNum,
                    'gp'        => $gpNum,
                    'intitule' => htmlspecialchars_decode($intitule, ENT_HTML5),
                    'description1' => htmlspecialchars_decode($description1, ENT_HTML5),
                    'description2' => htmlspecialchars_decode($description2, ENT_HTML5),
                    'namescarac' => htmlspecialchars_decode($namescarac, ENT_HTML5),
                    'carac' => htmlspecialchars_decode($carac, ENT_HTML5),
                    'typelabel' => htmlspecialchars_decode($typelabel, ENT_HTML5)
                ],
                'lps' => $lps,
                'gas' => $gas,
                'lpps'  => $lpps,
                'lppAll'  => $lppAll,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-gp-edit.html.twig', ['data'  => $dataForView]);
        }

    }


}