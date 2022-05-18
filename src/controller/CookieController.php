<?php


namespace src\controller;
use src\middleware\AdminNotLoggedInMiddleware;
use \src\model\CookieModel as CookieModel;
use src\model\SecurityModel;


class CookieController
{

    /**
     * CookieController constructor.
     */
    public function __construct()
    {
    }

    /**
     * Obtenir les valeurs d'un cookie
     * @param $name
     * @return mixed
     */
    public function getCookie($name)
    {

        if(in_array($name, array_keys($_COOKIE))){
            $v = SecurityModel::getValueFromSecureValue($_COOKIE[$name]);
            return new CookieModel($name, $v);
        }
        else{
            return null;
        }


    }

    /**
     * Definir ou mettre a jour un cookie
     * @param CookieModel $cookie
     */
    public function setCookie(CookieModel $cookie) : void
    {
        setcookie($cookie->getName(), $cookie->getValue(), $cookie->getExpire());
    }

    /**
     * Supprimer un cookie
     * @param $name
     */
    public function removeCookie($name) : void
    {
        if(in_array($name, array_keys($_COOKIE))) {
            unset($_COOKIE[$name]);
            setcookie($name, "", time() - 3600);
        }
    }



}