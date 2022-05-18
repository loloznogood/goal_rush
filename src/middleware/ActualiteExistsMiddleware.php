<?php

namespace src\middleware;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\dao\DAOFactory;
use src\model\dao\ActualiteDAO;

class ActualiteExistsMiddleware
{
    private $container;

    /**
     * ActualiteExistsMiddleware constructor.
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
            $actualiteId = $args['id'];
            $daoFactory = new DAOFactory($this->container->db);
            $actualiteDAO = $daoFactory->getActualiteDAO();
            $actualite = $actualiteDAO->find($actualiteId);

            if(is_null($actualite)){
                return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('actualite.index'));
            }
            else{
                return $next($request, $response);
            }
        }
        else{
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('actualite.index'));
        }

    }
}