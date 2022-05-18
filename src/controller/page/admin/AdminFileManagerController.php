<?php

namespace src\controller\page\admin;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use \src\controller\page\PageController;

class AdminFileManagerController extends PageController
{
    const DROITS = 1;

    private $filAriane;
    /**
     * AdminFileManagerController constructor.
     */
    public function __construct($container)
    {
        parent::__construct($container);
        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Menu administrateur'=> $this->getContainer()->router->pathFor('admin/home.index'),
            'Gestionnaire de fichiers'=> $this->getContainer()->router->pathFor('admin/filemanager.index')

        ];
    }


    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try {
            $dataForView= ['filAriane' => $this->filAriane, 'footer' => $this->getFooterData()];
            $this->render($response, "admin-filemanager-index.html.twig",['data' => $dataForView]);
        }
        catch(\Exception $e){
            $dataForView = ['filAriane' => $this->filAriane, 'error' => $e->getMessage()];
            $this->render($response, "admin-filemanager-index.html.twig",['data' => $dataForView]);
        }

    }

    public function connector(RequestInterface $request, ResponseInterface $response, array $args)
    {
        include __DIR__.'/../../../connector/filemanager.php';
        return $response;
    }

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {
        try{
            $this->render($response, "admin-filemanager-show.html.twig", []);
        }
        catch(\Exception $e){
            $dataForView = ['filAriane' => $this->filAriane, 'footer' => $this->getFooterData(), 'error' => $e->getMessage()];
            $this->render($response, "admin-filemanager-index.html.twig",['data' => $dataForView]);
        }
    }


}