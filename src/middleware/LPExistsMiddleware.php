<?php

namespace src\middleware;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\dao\DAOFactory;
use src\model\dao\LPDAO;

class LPExistsMiddleware
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

        $route = $request->getAttribute('route');
        $args= $route->getArguments();

        if(isset($args['id'])){
            $lpId = $args['id'];
            $daoFactory = new DAOFactory($this->container->db);
            $LPDAO = $daoFactory->getLPDAO();
            $lp = $LPDAO->find($lpId);
            if(is_null($lp)){
                return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('ligne-produit.index'));
            }
            else{
                return $next($request, $response);
            }
        }
        else{
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('ligne-produit.index'));
        }

    }
}