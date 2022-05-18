<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\Lien;

class AdminLienController extends PageController
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
            'Gestion des liens'=> $this->getContainer()->router->pathFor('admin/lien.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lienDAO = $daoFactory->getLienDAO();
            $liens = $lienDAO->findAll();
            $dataForView = [
                'liens' => $liens,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-lien-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer un nouveau lien'] = $this->getContainer()->router->pathFor('admin/lien.create');

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lienDAO = $daoFactory->getLienDAO();
            $categ=$lienDAO->findAllCateg();
            
            $dataForView = ['filAriane' => $this->filAriane, 'footer' => $this->getFooterData(), 'categ'=>$categ];
            $this->render($response, 'admin-lien-create.html.twig', ['data' => $dataForView, 'footer' => $this->getFooterData()]);

        }catch(\Exception $e){
            $dataForView = [
                'categ'     =>$categ,
                'error' => 'Un problème est survenu lors du traitement de ce lien.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-lien-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {                                                                       
        try{
            $this->filAriane['Créer un nouvau lien'] = $this->getContainer()->router->pathFor('admin/lien.create');

            $type = $request->getParsedBody()['type'];
            $titre = $request->getParsedBody()['titre'];
            $url = htmlspecialchars($request->getParsedBody()['url'], ENT_HTML5, "UTF-8");
            
            if($type=="Categorie"){
                $idCategorie=NULL;
            }
            else if($type=="Sous-categorie"){ 
                $idCategorie = $request->getParsedBody()['categorie'];

                if($idCategorie==-1){
                    throw new \Exception("Catégorie incorrect.");
                }
            }
            if($type==-1){
                throw new \Exception("Type incorrect.");

            }
            if($titre==""){
                throw new \Exception("url incorrect.");

            }
            if($url==""){
                throw new \Exception("url incorrect.");

            }
            
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lienDAO = $daoFactory->getLienDAO();
            
            $lien = new Lien(0,$type, $titre, $url, $idCategorie);
            $lienDAO->create($lien);
            return $this->redirect($response, "admin/lien.index");

        }catch(\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lienDAO = $daoFactory->getLienDAO();
            $categ=$lienDAO->findAllCateg();
            $dataForView = [
                'error' => $e->getMessage(),
                'lien' => [
                    'id'        => $id,
                    'titre'     => $titre,
                    'url'   => $url,
                    'type' =>$type,
                    'idCategorie' =>$idCategorie
                ],
                'categ'=> $categ,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-lien-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier un lien'] = $this->getContainer()->router->pathFor('admin/lien.edit', ['id'=>$id]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lienDAO = $daoFactory->getLienDAO();
            $lien = $lienDAO->find($id);
            $categ=$lienDAO->findAllCateg();
            echo count($categ);
          
            if(is_null($lien)){
                throw new \Exception("lien inconnue.");
            }

            $dataForView = [
                'categ'     =>$categ,
                'lien'    => $lien,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-lien-edit.html.twig', ['data'  => $dataForView]);
        }catch (\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-lien-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            
            $this->filAriane['Modifier un lien'] = $this->getContainer()->router->pathFor('admin/lien.edit', ['id'=>$id]);
            $type = $request->getParsedBody()['type'];
            $titre = $request->getParsedBody()['titre'];
            $url = htmlspecialchars($request->getParsedBody()['url'], ENT_HTML5, "UTF-8");

            $daoFactory = new DAOFactory($this->getContainer()->db);
            
            $lienDAO = $daoFactory-> getLienDAO();

            
            echo $type."<br>";
            echo $titre."<br>";
            echo $url."<br>";
            
            if($type=="Categorie"){
                $idCategorie=NULL;
            }
            else if($type=="Sous-categorie"){ 
                $idCategorie = $request->getParsedBody()['categorie'];

                if($idCategorie==-1){
                    throw new \Exception("Catégorie incorrect.");
                }
            }
            if($type==-1){
                throw new \Exception("Type incorrect.");

            }
            if($titre==""){
                throw new \Exception("url incorrect.");

            }
            if($url==""){
                throw new \Exception("url incorrect.");

            }
            

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lienDAO = $daoFactory->getLienDAO();

            $lien = new Lien($id,$type, $titre, $url, $idCategorie);


          
            $lienDAO->update($lien);
            return $this->redirect($response, "admin/lien.edit",['id'=>$id]);

           
           


        }catch (\Exception $e){
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lienDAO = $daoFactory->getLienDAO();
            $categ=$lienDAO->findAllCateg();
            $dataForView = [
                'error' => $e->getMessage(),
                'lien' => [
                    'id'        => $id,
                    'titre'     => $titre,
                    'url'   => $url,
                    'type' =>$type,
                    'idCategorie' =>$idCategorie
                ],
                'categ'=> $categ,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-lien-edit.html.twig', ['data' => $dataForView]);
        }

    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $id = $args['id'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lienDAO = $daoFactory->getLienDAO();
            $lien = $lienDAO->find($id);
            $liens = $lienDAO->findAll();
            $lienDAO->delete($lien);
            return $this->redirect($response, "admin/lien.index");


        }catch(\Exception $e){
            $dataForView = [
                'liens' => $liens,
                'error' => 'Le lien  n\'a pas été supprimé.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-lien-index.html.twig', ['data' => $dataForView]);
        }
    }

}