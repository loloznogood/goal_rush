<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\controller\CookieController;
use src\model\CookieModel;
use \src\model\metier\Admin;
use src\model\dao\AdminDAO;
use src\model\dao\DAOFactory;
use src\model\SecurityModel;
use \src\controller\page\PageController;

class AdminConnexionController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Connexion administrateur'=> $this->getContainer()->router->pathFor('admin/connexion.show')
        ];
    }

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $dataForView = ['filAriane' => $this->filAriane, 'footer' => $this->getFooterData()];
        $this->render($response, 'admin-connexion.html.twig', ['data' => $dataForView]);
    }
    public function connect(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $login = $request->getParsedBody()['login'];
        $pass = $request->getParsedBody()['pass'];
        $daoFactory = new DAOFactory($this->getContainer()->db);
        $adminDAO = $daoFactory->getAdminDAO();

        $admin = $adminDAO->connexion($login);

        $errorFlag = false;
        if(!$admin){
            $errorFlag = true;
        }
        else{
            if(!SecurityModel::passwordVerify($pass, $admin->getPass())){
                $errorFlag = true;
            }
        }


        $dataForView = array('footer' => $this->getFooterData());

        if($errorFlag){
            $error = "Connexion impossible, veuillez renseigner un nom d'utilissateur et un mot de passe correctes.";
            $dataForView['error'] = $error;
            $dataForView['login'] = $login;
            $dataForView['filAriane'] = $this->filAriane;
            $this->render($response, 'admin-connexion.html.twig', ['data' => $dataForView]);

        }
        else{

            $adminIdCookie = new CookieModel(
                "admin-id",
                SecurityModel::makeSecureValue($admin->getId())
            );
            $adminAuthCookie = new CookieModel(
                "admin-authentification",
                SecurityModel::makeSecureValue($admin->getId() . $admin->getLogin() )
            );

            $cookieCtrl = new CookieController();
            $cookieCtrl->setCookie($adminIdCookie);
            $cookieCtrl->setCookie($adminAuthCookie);

            return $this->redirect($response, "admin/home.index");
        }

    }
}