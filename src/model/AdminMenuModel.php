<?php


namespace src\model;


/**
 * Class AdminMenuModel
 * @package src\model
 */
class AdminMenuModel
{
    /**
     * @var array $groupes tableau de groupe d'item
     */
    private $groupes;

    /**
     * AdminMenuModel constructor.
     * @param array $groupes
     */
    public function __construct()
    {
        $this->groupes = [];
    }

    public function addGroupe(AdminMenuGroupe $groupe) : void
    {
        array_push($this->groupes, $groupe);
    }

    /**
     * @return array
     */
    public function getGroupes(): array
    {
        return $this->groupes;
    }




}