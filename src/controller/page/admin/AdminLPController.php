<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\LP;

class AdminLPController extends PageController
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
            'Gestion des lignes de produits'=> $this->getContainer()->router->pathFor('admin/lp.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAll();
            $dataForView = [
                'lps' => $lps,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-lp-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }


    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $id = $args['id'];
        $this->filAriane['Modifier une ligne de produits'] = $this->getContainer()->router->pathFor('admin/lp.edit', ['id'=>$id]);
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $lpDAO = $daoFactory->getLPDAO();
        $lp = $lpDAO->find($id);

        $dataForView = [
            'lp'   => $lp,
            'filAriane' => $this->filAriane,
            'footer' => $this->getFooterData()
        ];

        $this->render($response, 'admin-lp-edit.html.twig', ['data'  => $dataForView]);

    }

    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            var_dump($request->getParsedBody());
            $id = $args['id'];
            $this->filAriane['Modifier une ligne de produits'] = $this->getContainer()->router->pathFor('admin/lp.edit', ['id'=>$id]);
            $id2 = $request->getParsedBody()['id'];
            $lpNum = $request->getParsedBody()['lp'] == "" ? null : $request->getParsedBody()['lp'];
            $intitule = $request->getParsedBody()['intitule'];
            $image = $request->getParsedBody()['image'];
            $fabfr = $request->getParsedBody()['fabfr'];

            if($id != $id2 || empty(trim($id))){
                throw new \Exception("Identifiant incorrect.");
            }
            if(!is_null($lpNum) && intval($lpNum)==0){
                throw new \Exception("Numéro de LP incorrect.");
            }
            if(empty(trim($intitule))){
                throw new \Exception("Intitulé incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lpOld = $lpDAO->find($id);
            $lpNew = new LP($id, $lpNum, $intitule,$lpOld->getOrdre(), $image,$fabfr);
            $lpDAO->update($lpNew);
            return $this->redirect($response, "admin/lp.edit",['id'=>$id]);


        }catch (\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'lp' => [
                    'id' => $id,
                    'lp' => (is_null($lpNum) ? "" : $lpNum),
                    'intitule' => $intitule,
                    'image' => $image,
                    'filAriane' => $this->filAriane
                ],
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-lp-edit.html.twig', $dataForView);
        }

    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $id = $args['id'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $gaDAO = $daoFactory->getGADAO();
            $lps = $lpDAO->findAll();
            $lp = $lpDAO->find($id);
            $gas = $gaDAO->findByLp($lp->getLp());
            if(!empty($gas) ){
                throw new \Exception("Impossible de supprimer la ligne de produits car elle contient des groupes articles. Vous devez d'abord supprimer ces groupes articles possédant un #LP égal à ".$lp->getLp()." .");
            }
            $lpDAO->delete($lp);
            return $this->redirect($response, "admin/lp.index");


        }catch(\Exception $e){
            $dataForView = [
                'lps' => $lps,
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-lp-index.html.twig', ['data' => $dataForView]);
        }
    }

    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer une nouvelle ligne de produits'] = $this->getContainer()->router->pathFor('admin/lp.create');
            $dataForView = [
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-lp-create.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){
            $dataForView = [
                'error' => 'Un problème est survenu lors du traitement de cette page.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-lp-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer une nouvelle ligne de produits'] = $this->getContainer()->router->pathFor('admin/lp.create');

            $lpNum = $request->getParsedBody()['lp'] == "" ? null : $request->getParsedBody()['lp'];
            $intitule = $request->getParsedBody()['intitule'];
            $image = $request->getParsedBody()['image'];

            if(!is_null($lpNum) && intval($lpNum)==0){
                throw new \Exception("Numéro de LP incorrect.");
            }
            if(empty(trim($intitule))){
                throw new \Exception("Intitulé incorrect.");
            }

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $ordre = $lpDAO->getOrdreMax();
            if(is_null($ordre)){
                $ordre = 1;
            }
            else{
                $ordre = strval($ordre + 1);
            }
            $lp = new LP('', $lpNum, $intitule,$ordre, $image);
            $lpDAO->create($lp);
            return $this->redirect($response, "admin/lp.index");

        }catch(\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'lp' => [
                    'lp'        => $lpNum,
                    'intitule'  => $intitule,
                    'image'     => $image
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-lp-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function order(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $this->filAriane['Modifier l\'ordre des lignes de produits'] = $this->getContainer()->router->pathFor('admin/lp.order');
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            $lps = $lpDAO->findAll();
            $dataForView = [
                'lps' => $lps,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-lp-order.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            $dataForView = [
                'lps' => $lps,
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-lp-order.html.twig', ['data' => $dataForView]);
        }


    }
    public function orderWebService(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            // La methode getParsedBody() convertit le JSON recu en tableau associatif (pas besoin de json_decode())
            $data = $request->getParsedBody();
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $lpDAO = $daoFactory->getLPDAO();
            foreach ($data as $id => $order){
                $lp = $lpDAO->find($id);
                $lp->setOrdre($order);
                $lpDAO->update($lp);
            }
            return $response->withStatus(200);
        }
        catch(\Exception $e){
            return $response->withStatus(500, $e->getMessage());
        }


    }
}