<?php

namespace src\controller\page;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use src\model\dao\DAOFactory;
use src\model\metier\Bet;

class BetController extends PageController{
    public function __construct($container)
    {
        parent::__construct($container);
    }

    public function show(RequestInterface $request, ResponseInterface $response, array $args)
    {
        $daoFactory = new DAOFactory($this->getContainer()->db);

        $betDAO = $daoFactory->getBetDAO();
        $bet = $betDAO->findAll();
        print_r($bet);
        $dataForView = [
            'bet' => $bet
        ];
        $this->render($response, 'bet.html.twig', ['data' => $dataForView]);
    }
}
