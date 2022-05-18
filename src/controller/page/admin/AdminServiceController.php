<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\Service;

class AdminServiceController extends PageController
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
            'Gestion des services'=> $this->getContainer()->router->pathFor('admin/service.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $serviceDAO = $daoFactory->getServiceDAO();
            $services = $serviceDAO->findAll();
            $dataForView = [
                'services' => $services,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-service-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer un nouveau service'] = $this->getContainer()->router->pathFor('admin/service.create');
            $dataForView = ['filAriane' => $this->filAriane, 'footer' => $this->getFooterData()];
            $this->render($response, 'admin-service-create.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){
            $dataForView = [
                'error' => 'Un problème est survenu lors du traitement de cette page.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-service-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $intitule = $request->getParsedBody()['intitule'];
            $description = $request->getParsedBody()['description'];
            $contenu = htmlspecialchars($request->getParsedBody()['contenu'], ENT_HTML5, "UTF-8");
            $image = $request->getParsedBody()['image'];

            if(empty(trim($intitule)) ){
                throw new \Exception("Intitulé incorrect.");
            }
            if(empty(trim($description)) ){
                throw new \Exception("Description incorrect.");
            }
            if(empty(trim($contenu)) ){
                throw new \Exception("Contenu incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $serviceDAO = $daoFactory->getServiceDAO();
            $ordre = $serviceDAO->getOrdreMax();
            if(is_null($ordre)){
                $ordre = 1;
            }
            else{
                $ordre = strval($ordre + 1);
            }
            $service = new Service('', $intitule, $description, $contenu, $image, $ordre);

            $serviceDAO->create($service);
            return $this->redirect($response, "admin/service.index");

        }catch(\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'service' => [
                    'intitule'     => $intitule,
                    'description' => $description,
                    'contenu'   => htmlspecialchars_decode($contenu, ENT_HTML5),
                    'image' => $image
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-service-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier un service'] = $this->getContainer()->router->pathFor('admin/service.edit', ['id'=>$id]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $serviceDAO = $daoFactory->getServiceDAO();
            $service = $serviceDAO->find($id);
            if(is_null($service)){
                throw new \Exception("Service inconnue.");
            }

            $dataForView = [
                'service'    => $service,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-service-edit.html.twig', ['data'  => $dataForView]);
        }catch (\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-service-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier une service'] = $this->getContainer()->router->pathFor('admin/service.edit', ['id'=>$id]);

            $id2 = $request->getParsedBody()['id'];
            $intitule = $request->getParsedBody()['intitule'];
            $description = $request->getParsedBody()['description'];
            $contenu = htmlspecialchars($request->getParsedBody()['contenu'], ENT_HTML5, "UTF-8");
            $image = $request->getParsedBody()['image'];

            if($id != $id2){
                throw new \Exception("Identifiant incorrect.");
            }
            if(empty(trim($intitule)) ){
                throw new \Exception("Intitulé incorrect.");
            }
            if(empty(trim($description)) ){
                throw new \Exception("Description incorrect.");
            }
            if(empty(trim($contenu)) ){
                throw new \Exception("Contenu incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $serviceDAO = $daoFactory->getServiceDAO();
            $serviceOld = $serviceDAO->find($id);
            $serviceNew = new Service($id, $intitule, $description, $contenu, $image, $serviceOld->getOrdre());
            $serviceDAO->update($serviceNew);
            return $this->redirect($response, "admin/service.edit",['id'=>$id]);


        }catch (\Exception $e){

            $dataForView = [
                'error' => $e->getMessage(),
                'service' => [
                    'id'        => $id,
                    'intitule'     => $intitule,
                    'description' => $description,
                    'contenu'   => htmlspecialchars_decode($contenu, ENT_HTML5),
                    'image' => $image
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-service-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $id = $args['id'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $serviceDAO = $daoFactory->getServiceDAO();
            $service = $serviceDAO->find($id);
            $services = $serviceDAO->findAll();
            $serviceDAO->delete($service);
            return $this->redirect($response, "admin/service.index");


        }catch(\Exception $e){
            $dataForView = [
                'services' => $services,
                'error' => 'Le service  n\'a pas été supprimé.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-service-index.html.twig', ['data' => $dataForView]);
        }
    }

    public function order(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $this->filAriane['Modifier l\'ordre des services'] = $this->getContainer()->router->pathFor('admin/service.order');
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $serviceDAO = $daoFactory->getServiceDAO();
            $services = $serviceDAO->findAll();
            $dataForView = [
                'services' => $services,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-service-order.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            $dataForView = [
                'services' => $services,
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-service-order.html.twig', ['data' => $dataForView]);
        }


    }
    public function orderWebService(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            // La methode getParsedBody() convertit le JSON recu en tableau associatif (pas besoin de json_decode())
            $data = $request->getParsedBody();
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $serviceDAO = $daoFactory->getServiceDAO();
            foreach ($data as $id => $order){
                $service = $serviceDAO->find($id);
                $service->setOrdre($order);
                $serviceDAO->update($service);
            }
            return $response->withStatus(200);
        }
        catch(\Exception $e){
            return $response->withStatus(500, $e->getMessage());
        }


    }

}