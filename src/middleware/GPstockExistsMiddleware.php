<?php

namespace src\middleware;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\dao\DAOFactory;
use src\model\dao\GADAO;
use src\model\dao\LPDAO;
use src\model\dao\GpDAOstock;


class GPstockExistsMiddleware
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

        if(isset($args['tn']) && isset($args['lp']) && isset($args['ga']) && isset($args['gp'])){
            $tnNum = $args['tn'];
            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $gpNum = $args['gp'];

            $daoFactory = new DAOFactory($this->container->db);
            $GPDAO = $daoFactory->getGPDAOstock();

            $gp = $GPDAO->find(['tn'=>$tnNum,'lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);

            if(is_null($gp)){

                $GADAO = $daoFactory->getGADAO();
                $ga = $GADAO->find(['lp'=>$lpNum, 'ga'=>$gaNum]);
                var_dump($ga);

                if(is_null($ga)){

                    $LPDAO = $daoFactory->getLPDAO();
                    $lp = $LPDAO->findByLp($lpNum);

                    if(!is_null($lp)){

                        return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor("ligne-produit.show", ['id' => $lp->getId(), 'lp' => $lp->getLp()]));
                    }
                    else{
                       // return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('ligne-produit.index'));
                       var_dump($ga);
                       var_dump($args);
                       var_dump($route);
                    }
                }
                else{
                    return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor("groupe-article.show", ['lp' => $lpNum, 'ga' => $gaNum]));
                }
            }
            else{
                return $next($request, $response);
            }
        }
        else{
            //return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('ligne-produit.index'));
            var_dump($ga);
        }
        
    }
}