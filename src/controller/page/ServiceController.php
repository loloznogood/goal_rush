<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\Service;

class ServiceController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Accueil' => $this->getContainer()->router->pathFor('accueil.index'),
            'Services'=> $this->getContainer()->router->pathFor('service.index')
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $daoFactory = new DAOFactory($this->getContainer()->db);
        $serviceDAO = $daoFactory->getServiceDAO();
        $pageDAO = $daoFactory->getPageDAO();
        $services = $serviceDAO->findAll();
        $page = $pageDAO->find('services');
        $dataForView = array(
            'services'  => $services,
            'filAriane' => $this->filAriane,
            'page'      => $page,
            'footer' => $this->getFooterData()
        );

        $this->render($response, 'service.html.twig', ['data' => $dataForView]);
    }

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {

        $id = $args['id'];

        $daoFactory = new DAOFactory($this->getContainer()->db);

        $serviceDAO = $daoFactory->getServiceDAO();


        $service = $serviceDAO->find($id);
        $services = $serviceDAO->findAll();

        $this->filAriane[$service->getIntitule()] = $service->getUrl();

        $dataForView = array(
            'service' => $service,
            'services' => $services,
            'filAriane' => $this->filAriane,
            'footer' => $this->getFooterData()
        );

        $this->render($response, 'service-show.html.twig', ['data'=>$dataForView]);


    }
}