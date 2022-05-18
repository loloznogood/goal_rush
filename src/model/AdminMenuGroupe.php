<?php


namespace src\model;


use src\model\AdminMenuItem;

class AdminMenuGroupe
{

    /**
     * @var String $intitule intitule du groupe
     */
    private $intitule;
    /**
     * @var array $items tableau d'items
     */
    private $items;

    /**
     * AdminMenuGroupe constructor.
     * @param String $intitule
     */
    public function __construct(String $intitule)
    {
        $this->intitule = $intitule;
        $this->items = [];
    }

    /**
     * @param AdminMenuItem $item
     */
    public function addItem(AdminMenuItem $item) : void
    {
        array_push($this->items, $item);
    }

    /**
     * @return String
     */
    public function getIntitule(): String
    {
        return $this->intitule;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }




}