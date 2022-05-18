<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\Actualite;

class AdminActualiteController extends PageController
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
            'Gestion des actualites'=> $this->getContainer()->router->pathFor('admin/actualite.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $actualiteDAO = $daoFactory->getActualiteDAO();
            $actualites = $actualiteDAO->findAll();
            $dataForView = [
                'actualites' => $actualites,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-actualite-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer une nouvelle actualite'] = $this->getContainer()->router->pathFor('admin/actualite.create');
            $dataForView = ['filAriane' => $this->filAriane, 'footer' => $this->getFooterData()];
            $this->render($response, 'admin-actualite-create.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){
            $dataForView = [
                'error' => 'Un problème est survenu lors du traitement de cette page.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-actualite-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $intitule = $request->getParsedBody()['intitule'];
            $type = $request->getParsedBody()['type'];
            $description = $request->getParsedBody()['description'];
            $contenu = htmlspecialchars($request->getParsedBody()['contenu'], ENT_HTML5, "UTF-8");
            $lien = $request->getParsedBody()['lien'];
            $dateDeb = $request->getParsedBody()['dateDeb'];
            $dateFin = $request->getParsedBody()['dateFin'];
            $image = $request->getParsedBody()['image'];
            $carousel = (isset($request->getParsedBody()['carousel']) && $request->getParsedBody()['carousel']=="on") ? true : false;

            if(empty(trim($type)) || $type == "-1" ){
                throw new \Exception("Type incorrect.");
            }
            if(empty(trim($intitule)) ){
                throw new \Exception("Intitulé incorrect.");
            }
            if(empty(trim($description)) ){
                throw new \Exception("Description incorrect.");
            }
            if(empty(trim($dateDeb)) ){
                throw new \Exception("Date de début incorrecte.");
            }
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $actualiteDAO = $daoFactory->getActualiteDAO();

            $actualite = new Actualite(null, $type, $intitule, $description, $contenu, $dateDeb, $dateFin, $lien, $image, $carousel);

            $actualiteDAO->create($actualite);
            return $this->redirect($response, "admin/actualite.index");

        }catch(\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'actualite' => [
                    'type'      => $type,
                    'intitule'     => $intitule,
                    'description' => $description,
                    'contenu'   => htmlspecialchars_decode($contenu, ENT_HTML5),
                    'dateDeb' => $dateDeb,
                    'dateFin' => $dateFin,
                    'lien' => $lien,
                    'image' => $image,
                    'carousel' => $carousel,
                    'footer' => $this->getFooterData()
                ],
                'filAriane' => $this->filAriane
            ];

            $this->render($response, 'admin-actualite-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier une actualité'] = $this->getContainer()->router->pathFor('admin/actualite.edit', ['id'=>$id]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $actualiteDAO = $daoFactory->getActualiteDAO();
            $actualite = $actualiteDAO->find($id);
            if(is_null($actualite)){
                throw new \Exception("Actualite inconnue.");
            }

            $dataForView = [
                'actualite'    => $actualite,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-actualite-edit.html.twig', ['data'  => $dataForView]);
        }catch (\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-actualite-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier une actualite'] = $this->getContainer()->router->pathFor('admin/actualite.edit', ['id'=>$id]);

            $id2 = $request->getParsedBody()['id'];
            $intitule = $request->getParsedBody()['intitule'];
            $type = $request->getParsedBody()['type'];
            $description = $request->getParsedBody()['description'];
            $contenu = htmlspecialchars($request->getParsedBody()['contenu'], ENT_HTML5, "UTF-8");
            $lien = $request->getParsedBody()['lien'];
            $dateDeb = $request->getParsedBody()['dateDeb'];
            $dateFin = $request->getParsedBody()['dateFin'];
            $image = $request->getParsedBody()['image'];
            $carousel = (isset($request->getParsedBody()['carousel']) && $request->getParsedBody()['carousel']=="on") ? true : false;

            if(empty(trim($type)) || $type == "-1" ){
                throw new \Exception("Type incorrect.");
            }
            if(empty(trim($intitule)) ){
                throw new \Exception("Intitulé incorrect.");
            }
            if(empty(trim($description)) ){
                throw new \Exception("Description incorrect.");
            }
            if(empty(trim($dateDeb)) ){
                throw new \Exception("Date de début incorrecte.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $actualiteDAO = $daoFactory->getActualiteDAO();
            $actualite = new Actualite($id, $type, $intitule, $description, $contenu, $dateDeb, $dateFin, $lien, $image, $carousel);
            $actualiteDAO->update($actualite);
            return $this->redirect($response, "admin/actualite.edit",['id'=>$id]);


        }catch (\Exception $e){

            $dataForView = [
                'error' => $e->getMessage(),
                'actualite' => [
                    'id'        => $id,
                    'intitule'     => $intitule,
                    'description' => $description,
                    'type' => $type,
                    'contenu'   => htmlspecialchars_decode($contenu, ENT_HTML5),
                    'dateDebut' => $dateDeb,
                    'dateFin' => $dateFin,
                    'lien' => $lien,
                    'image' => $image,
                    'carousel' => $carousel,
                    'footer' => $this->getFooterData()
                ],
                'filAriane' => $this->filAriane
            ];
            $this->render($response, 'admin-actualite-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $id = $args['id'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $actualiteDAO = $daoFactory->getActualiteDAO();
            $actualite = $actualiteDAO->find($id);
            $actualites = $actualiteDAO->findAll();
            $actualiteDAO->delete($actualite);
            return $this->redirect($response, "admin/actualite.index");


        }catch(\Exception $e){
            $dataForView = [
                'actualites' => $actualites,
                'error' => 'Le actualite  n\'a pas été supprimée.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-actualite-index.html.twig', ['data' => $dataForView]);
        }
    }


}