<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\Page;

class AdminPageController extends PageController
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
            'Gestion des pages'=> $this->getContainer()->router->pathFor('admin/page.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $pageDAO = $daoFactory->getPageDAO();
            $pages = $pageDAO->findAll();
            $dataForView = [
                'pages' => $pages,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-page-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer une nouvelle page'] = $this->getContainer()->router->pathFor('admin/page.create');
            $dataForView = ['filAriane' => $this->filAriane, 'footer' => $this->getFooterData()];
            $this->render($response, 'admin-page-create.html.twig', ['data' => $dataForView, 'footer' => $this->getFooterData()]);

        }catch(\Exception $e){
            $dataForView = [
                'error' => 'Un problème est survenu lors du traitement de cette page.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-page-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer une nouvelle page'] = $this->getContainer()->router->pathFor('admin/page.create');

            $id = $request->getParsedBody()['id'];
            $titre = $request->getParsedBody()['titre'];
            $contenu = htmlspecialchars($request->getParsedBody()['contenu'], ENT_HTML5, "UTF-8");

            if(empty(trim($id))){
                throw new \Exception("Identifiant incorrect.");
            }


            $daoFactory = new DAOFactory($this->getContainer()->db);
            $pageDAO = $daoFactory->getPageDAO();

            $page = new Page($id, $titre, $contenu);
            $pageDAO->create($page);
            return $this->redirect($response, "admin/page.index");

        }catch(\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'p' => [
                    'id'        => $id,
                    'titre'     => $titre,
                    'contenu'   => htmlspecialchars_decode($contenu, ENT_HTML5)
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-page-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier une page'] = $this->getContainer()->router->pathFor('admin/page.edit', ['id'=>$id]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $pageDAO = $daoFactory->getPageDAO();
            $page = $pageDAO->find($id);

            if(is_null($page)){
                throw new \Exception("Page inconnue.");
            }

            $dataForView = [
                'page'    => $page,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-page-edit.html.twig', ['data'  => $dataForView]);
        }catch (\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-page-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier une page'] = $this->getContainer()->router->pathFor('admin/page.edit', ['id'=>$id]);
            $id2 = $request->getParsedBody()['id'];
            $titre = $request->getParsedBody()['titre'];
            $contenu = htmlspecialchars($request->getParsedBody()['contenu'], ENT_HTML5, "UTF-8");

            if($id != $id2 ){
                throw new \Exception("Identifiant incorrect.");
            }


            $daoFactory = new DAOFactory($this->getContainer()->db);

            $pageDAO = $daoFactory->getPageDAO();
            $pageNew = new Page($id, $titre, $contenu);
            $pageDAO->update($pageNew);


            return $this->redirect($response, "admin/page.edit",['id'=>$id]);


        }catch (\Exception $e){

            $dataForView = [
                'error' => $e->getMessage(),
                'page' => [
                    'id'        => $id,
                    'titre'     => $titre,
                    'contenu'   => $contenu
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-page-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $id = $args['id'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $pageDAO = $daoFactory->getPageDAO();
            $page = $pageDAO->find($id);
            $pages = $pageDAO->findAll();
            $pageDAO->delete($page);
            return $this->redirect($response, "admin/page.index");


        }catch(\Exception $e){
            $dataForView = [
                'pages' => $pages,
                'error' => 'Le groupe produit  n\'a pas été supprimé.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-page-index.html.twig', ['data' => $dataForView]);
        }
    }

}