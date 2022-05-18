<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;
use src\model\metier\Admin;

class AdminAdminController extends PageController
{
    const DROITS = 9;

    private $filAriane;
    /**
     * AdminAdminController constructor.
     */
    public function __construct($container)
    {
        parent::__construct($container);
        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Menu administrateur'=> $this->getContainer()->router->pathFor('admin/home.index'),
            'Gestion des admins'=> $this->getContainer()->router->pathFor('admin/admin.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {

            $daoFactory = new DAOFactory($this->getContainer()->db);
            $adminDAO = $daoFactory->getAdminDAO();
            $admins = $adminDAO->findAll();
            $dataForView = [
                'admins' => $admins,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-admin-index.html.twig', ['data' => $dataForView]);
        }
        catch(\Exception $e){
            echo $e->getMessage();
        }

    }

    public function create(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer un nouvel admin'] = $this->getContainer()->router->pathFor('admin/admin.create');
            $dataForView = ['filAriane' => $this->filAriane, 'footer' => $this->getFooterData()];
            $this->render($response, 'admin-admin-create.html.twig', ['data' => $dataForView]);

        }catch(\Exception $e){
            $dataForView = [
                'error' => 'Un problème est survenu lors du traitement de cette admin.',
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-admin-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function store(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->filAriane['Créer un nouvel admin'] = $this->getContainer()->router->pathFor('admin/admin.create');

            $login = $request->getParsedBody()['login'];
            $pass = $request->getParsedBody()['pass'];
            $passConfirm = $request->getParsedBody()['passConfirm'];
            $nom = $request->getParsedBody()['nom'];
            $droits = $request->getParsedBody()['droits'];

            if(empty(trim($login))){
                throw new \Exception("Login incorrect.");
            }
            if(empty(trim($pass))){
                throw new \Exception("Mot de passe incorrect.");
            }
            if(empty(trim($passConfirm))){
                throw new \Exception("Confirmation du mot de passe incorrecte.");
            }

            if($pass != $passConfirm){
                throw new \Exception("Le mot de passe et sa confirmation sont différents.");
            }

            if(intval($droits) == 0){
                throw new \Exception("Droits de l'utilisateurs incorrects.");
            }


            $daoFactory = new DAOFactory($this->getContainer()->db);
            $adminDAO = $daoFactory->getAdminDAO();

            $admin = new admin(null, $login, $pass, $nom, $droits);
            $adminDAO->create($admin);
            return $this->redirect($response, "admin/admin.index");

        }catch(\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'admin' => [
                    'login'     => $login,
                    'pass'   => $pass,
                    'passConfirm' => $passConfirm,
                    'nom' => $nom,
                    'droits' => $droits
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-admin-create.html.twig', ['data' => $dataForView]);
        }
    }

    public function edit(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier un admin'] = $this->getContainer()->router->pathFor('admin/admin.edit', ['id'=>$id]);
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $adminDAO = $daoFactory->getAdminDAO();
            $admin = $adminDAO->find($id);

            if(is_null($admin)){
                throw new \Exception("Admin inconnue.");
            }

            $dataForView = [
                'admin'    => $admin,
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-admin-edit.html.twig', ['data'  => $dataForView]);
        }catch (\Exception $e){
            $dataForView = [
                'error' => $e->getMessage(),
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-admin-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function update(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $id = $args['id'];
            $this->filAriane['Modifier un admin'] = $this->getContainer()->router->pathFor('admin/admin.edit', ['id'=>$id]);
            $id2 = $request->getParsedBody()['id'];
            $login = $request->getParsedBody()['login'];
            $pass = $request->getParsedBody()['pass'];
            $passConfirm = $request->getParsedBody()['passConfirm'];
            $nom = $request->getParsedBody()['nom'];
            $droits = $request->getParsedBody()['droits'];

            if($id != $id2 ){
                throw new \Exception("Identifiant incorrect.");
            }
            if(empty(trim($login))){
                throw new \Exception("Login incorrect.");
            }

            $newPass = null;
            if((!empty(trim($pass))) || (!empty(trim($passConfirm)))) {

                if (empty(trim($pass))) {
                    throw new \Exception("Mot de passe incorrecte.");
                }
                if (empty(trim($passConfirm))) {
                    throw new \Exception("Confirmation du mot de passe incorrecte.");
                }
                if ($pass != $passConfirm) {
                    throw new \Exception("Le mot de passe et sa confirmation sont différents.");
                }
                $newPass = $pass;
            }

            if(intval($droits) == 0){
                throw new \Exception("Droits de l'utilisateurs incorrects.");
            }



            $daoFactory = new DAOFactory($this->getContainer()->db);

            $adminDAO = $daoFactory->getAdminDAO();
            if(is_null($newPass)){
                $adminOld = $adminDAO->find($id);
                $admin = new admin($id, $login, $adminOld->getPass(), $nom, $droits);
                $adminDAO->update($admin);
            }
            else{
                $admin = new admin($id, $login, $newPass, $nom, $droits);
                $adminDAO->update($admin);
            }


            return $this->redirect($response, "admin/admin.edit",['id'=>$id]);


        }catch (\Exception $e){

            $dataForView = [
                'error' => $e->getMessage(),
                'admin' => [
                    'id' => $id,
                    'login'     => $login,
                    'pass'   => $pass,
                    'passConfirm' => $passConfirm,
                    'nom' => $nom,
                    'droits' => $droits
                ],
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];
            $this->render($response, 'admin-admin-edit.html.twig', ['data'  => $dataForView]);
        }

    }

    public function delete(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{

            $id = $args['id'];
            $daoFactory = new DAOFactory($this->getContainer()->db);
            $adminDAO = $daoFactory->getAdminDAO();
            $admin = $adminDAO->find($id);
            $admins = $adminDAO->findAll();
            $adminDAO->delete($admin);
            return $this->redirect($response, "admin/admin.index");


        }catch(\Exception $e){
            $dataForView = [
                'admins' => $admins,
                'error' => "L'admin n'a pas été supprimé.",
                'filAriane' => $this->filAriane,
                'footer' => $this->getFooterData()
            ];

            $this->render($response, 'admin-admin-index.html.twig', ['data' => $dataForView]);
        }
    }

}