<?php

namespace src\middleware;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\dao\DAOFactory;
use src\model\dao\DepartementDAO;

class DepartementExistsMiddleware
{
    private $container;

    /**
     * DepartementExistsMiddleware constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }


    public function __invoke(RequestInterface $request, ResponseInterface $response, callable $next)
    {

        $route = $request->getAttribute('route');
        $args= $route->getArguments();

        if(isset($args['dept']) && ($args['dept'] != -1)){
            $departementId = $args['dept'];
            $daoFactory = new DAOFactory($this->container->db);
            $departementDAO = $daoFactory->getdepartementDAO();
            $departement = $departementDAO->find($departementId);

            if(is_null($departement)){
                return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('contact.index'));
            }
            else{
                return $next($request, $response);
            }
        }
        else{
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('contact.index'));
        }

    }
}