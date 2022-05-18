<?php

namespace src\controller\page;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;

class AliController extends PageController
{
    private $filAriane;
    public function __construct($container)
    {
        parent::__construct($container);

        $this->filAriane = [
            'Ali' => "aliali",
            'Beguendouz'=> "123"
        ];
    }

    public function index(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $msg = "Il était une fois dans l'est";

        $daoFactory = new DAOFactory($this->getContainer()->db);
        $actuDAO = $daoFactory->getActualiteDAO();
        $lastActu = $actuDAO->findAll()[0];

       // var_dump($lastActu);

        $dataForView = array(
            'phrase' => $msg,
            'dernier' => $lastActu //objet
        );


        $this->render($response, 'ali.html.twig', ['data' => $dataForView]);
    }
}


?>