<?php

namespace src\model;


/**
 * Class AdminMenuItem
 * @package src\model\metier
 */
class AdminMenuItem
{
    /**
     * @var String $intitule de l'item
     */
    private $intitule;
    /**
     * @var String $lien lien vers la page d'administration
     */
    private $lien;
    /**
     * @var Integer $droits droits que doit avoir l'utilisateur pour acceder à la page d'administration
     */
    private $droits;

    /**
     * AdminMenuItem constructor.
     * @param String $intitule
     * @param String $lien
     * @param int $droits
     */
    public function __construct(String $intitule, String $lien, int $droits)
    {
        $this->intitule = $intitule;
        $this->lien = $lien;
        $this->droits = $droits;
    }

    /**
     * @return String
     */
    public function getIntitule(): String
    {
        return $this->intitule;
    }

    /**
     * @return String
     */
    public function getLien(): String
    {
        return $this->lien;
    }

    /**
     * @return int
     */
    public function getDroits(): int
    {
        return $this->droits;
    }



}