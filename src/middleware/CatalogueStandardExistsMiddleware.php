<?php

namespace src\middleware;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\dao\DAOFactory;
use src\model\dao\CatalogueStandardDAO;

class CatalogueStandardExistsMiddleware
{
    private $container;

    /**
     * CatalogueStandardExistsMiddleware constructor.
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
        $daoFactory = new DAOFactory($this->container->db);
        $catalogueDAO = $daoFactory->getCatalogueStandardDAO();

        $n = count($args);

        //LP Perso
        if($n == 1){
            $lpId = $args['id'];
            $catalogue = $catalogueDAO->findByLpId($lpId);
            if(is_null($catalogue)){
                return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('catalogue-standard.index'));
            }
            else{
                return $next($request, $response);
            }
        }
        //LP
        elseif ($n == 2){
            $lpId = $args['id'];
            $lpNum = $args['lp'];
            $catalogue = $catalogueDAO->findByLpId($lpId);
            if(is_null($catalogue) || ($catalogue->getLp()!= $lpNum)){
                return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('catalogue-standard.index'));
            }
            else{
                return $next($request, $response);
            }
        }
        //GP
        elseif ($n == 3){
            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $gpNum = $args['gp'];
            $catalogue = $catalogueDAO->findByGp($lpNum, $gaNum, $gpNum);
          //  var_dump($catalogue);
            if(is_null($catalogue)){
                return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('catalogue-standard.index'));
            }
            else{
                return $next($request, $response);
            }

        }
        else{
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('catalogue-standard.index'));

        }

    }
}