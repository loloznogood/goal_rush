<?php

namespace src\middleware;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use src\model\dao\DAOFactory;
use src\model\dao\GPDAO;
use src\model\dao\GADAO;
use src\model\dao\LPDAO;

class GPExistsMiddleware
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

        if(isset($args['lp']) && isset($args['ga']) && isset($args['gp'])){
            $lpNum = $args['lp'];
            $gaNum = $args['ga'];
            $gpNum = $args['gp'];

            $daoFactory = new DAOFactory($this->container->db);
            $GPDAO = $daoFactory->getGPDAO();

            $gp = $GPDAO->find(['lp'=>$lpNum, 'ga'=>$gaNum, 'gp'=>$gpNum]);

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
                        return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('ligne-produit.index'));
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
            return $response->withStatus(302)->withHeader('Location', $this->container->router->pathFor('ligne-produit.index'));
        }
        
    }
}