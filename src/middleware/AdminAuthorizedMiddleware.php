<?php

namespace src\middleware;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\CookieModel;
use src\model\SecurityModel;
use src\controller\CookieController;
use src\model\dao\DAOFactory;
use src\model\dao\AdminDAO;

class AdminAuthorizedMiddleware
{
    private $container;

    /**
     * AdminAuthorizedMiddleware constructor.
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
        if(is_null($adminIdCookie)){
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('admin/home.index'));
        }
        $id = $adminIdCookie->getValue();
        $admin = $adminDAO->find($id);

        $route = $request->getAttribute('route');
        $callable = $route->getCallable(); // chemin/.../nom_de_la_classe:methode_appelle
        $classe = explode(':', $callable)[0];
        $objetCtrl = new $classe($this->container);


        if(defined($classe.'::DROITS')){
            if($admin->getDroits() >= constant($classe.'::DROITS')){
                return $next($request, $response);
            }
            else{
                return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('admin/home.index'));
            }
        }
        else{
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('admin/home.index'));
        }

    }
}