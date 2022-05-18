<?php

namespace src\model\metier;
use src\model\ToolModel;

class Tickets
{
    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $date;

    /**
     * @var
     */
    private $result;

    /**
     * @var
     */
    private $bet;

    /**
     * @var
     */
    private $potentiel;

    /**
     * Bet constructor.
     * @param $id
     * @param $date
     * @param $result
     * @param $bet
     * @param $potentiel
     */

    public function __construct($id, $date, $result, $bet, $potentiel)
    {
        $this->id = $id;
        $this->date = $date;
        $this->result = $result;
        $this->bet = $bet;
        $this->potentiel = $potentiel;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;
    }

    public function getBet()
    {
        return $this->bet;
    }

    public function setBet($bet)
    {
        $this->bet = $bet;
    }

    public function getPotentiel(){
        return $this->potentiel;
    }

    public function setPotentiel($potentiel){
        $this->potentiel = $potentiel;
    }
}
