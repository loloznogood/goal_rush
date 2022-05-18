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

class AdminDeconnexionController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

    }

    public function disconnect(RequestInterface $request, ResponseInterface $response, array $args)
    {
        var_dump($_COOKIE);
        $cookieCtrl = new CookieController();
        $cookieCtrl->removeCookie('admin-id');
        $cookieCtrl->removeCookie('admin-authentification');
        var_dump($_COOKIE);
        return $this->redirect($response, "admin/connexion.show");

    }

}