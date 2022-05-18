<?php

namespace src\middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\CookieModel;
use src\model\SecurityModel;
use src\controller\CookieController;
use src\model\dao\DAOFactory;
use src\model\dao\AdminDAO;

class AdminNotLoggedInMiddleware
{
    private $container;

    /**
     * AdminNotLoggedInMiddleware constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }


    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {

        $daoFactory = new DAOFactory($this->container->db);
        $adminDAO = $daoFactory->getAdminDAO();
        $cookieCtrl = new CookieController();

        $adminIdCookie = $cookieCtrl->getCookie('admin-id');
        $adminAuthCookie = $cookieCtrl->getCookie('admin-authentification');

        if((!is_null($adminIdCookie)) && (!is_null($adminAuthCookie))){

            $id = $adminIdCookie->getValue();
            $auth = $adminAuthCookie->getValue();

            if((!is_null($id)) && (!is_null($auth))){

                $daoFactory = new DAOFactory($this->container->db);
                $adminDAO = $daoFactory->getAdminDAO();
                $admin = $adminDAO->find($id);

                if(!is_null($admin)){

                    if($auth == $admin->getId().$admin->getLogin()){
                        return $next($request, $response);
                    }
                    else{
                        return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('admin/connexion.show'));
                    }
                }
                else{
                    return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('admin/connexion.show'));
                }

            }
            else{
                return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('admin/connexion.show'));
            }


        }
        else{
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('admin/connexion.show'));
        }

    }
}