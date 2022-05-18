<?php

namespace src\model\metier;

use \src\model\SecurityModel;

class Admin
{

    /**
     * @var Integer|null $id identifiant de l'admin
     */
    private $id;
    /**
     * @var String $login login de l'admin permettant la connexion
     */
    private $login;
    /**
     * @var String $pass mot de pass de l'admin permettant de la connexion
     */
    private $pass;
    /**
     * @var String nom d'utilisateur de l'admin
     */
    private $nom;
    /**
     * @var String $droits droits d'acces au different item du menu administrateur
     */
    private $droits;

    /**
     * Admin constructor.
     * @param int|null $id
     * @param String $login
     * @param String $pass
     * @param String $nom
     * @param String $droits
     */
    public function __construct($id, $login, $pass, $nom, $droits)
    {
        $this->id = $id;
        $this->login = $login;
        $this->pass = $pass;
        $this->nom = $nom;
        $this->droits = $droits;
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }


    /**
     * @return String
     */
    public function getLogin(): String
    {
        return $this->login;
    }

    /**
     * @param String $login
     */
    public function setLogin(String $login): void
    {
        $this->login = $login;
    }

    /**
     * @return String
     */
    public function getPass(): String
    {
        return $this->pass;
    }

    /**
     * @param String $pass
     */
    public function setPass(String $pass): void
    {
        $this->pass = $pass;
    }

    /**
     * @return String
     */
    public function getNom(): String
    {
        return $this->nom;
    }

    /**
     * @param String $nom
     */
    public function setNom(String $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return String
     */
    public function getDroits()
    {
        return $this->droits;
    }

    /**
     * @param String $droits
     */
    public function setDroits($droits)
    {
        $this->droits = $droits;
    }



    public function getSecurePass() : String
    {
        return SecurityModel::passwordHash($this->getPass());
    }




}