<?php

namespace src\middleware;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\dao\DAOFactory;
use src\model\dao\ServiceDAO;

class ServiceExistsMiddleware
{
    private $container;

    /**
     * ServiceExistsMiddleware constructor.
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
            $serviceId = $args['id'];
            $daoFactory = new DAOFactory($this->container->db);
            $serviceDAO = $daoFactory->getServiceDAO();
            $service = $serviceDAO->find($serviceId);

            if(is_null($service)){
                return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('service.index'));
            }
            else{
                return $next($request, $response);
            }
        }
        else{
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('service.index'));
        }

    }
}