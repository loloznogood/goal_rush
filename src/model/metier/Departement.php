<?php

namespace src\model\metier;


class Departement
{
    /**
     * @var String $num numero de departement
     */
    private $num;
    /**
     * @var String $nom nom du departement
     */
    private $nom;
    /**
     * @var String $secteur secteur jumo
     */
    private $secteur;
    /**
     * @var Integer|null $idDir identifiant du directeur commercial
     */
    private $idDir;
    /**
     * @var Integer|null $idTcs identifiant du TCS
     */
    private $idTcs;
    /**
     * @var Integer|null $idItc identifiant du ITC
     */
    private $idItc;
    /**
     * @var Integer|null $idAc identifiant de l'assistante commerciale
     */
    private $idAc;

    /**
     * Departement constructor.
     * @param String $num
     * @param String $nom
     * @param String $secteur
     * @param int $idDir
     * @param int $idTcs
     * @param int $idItc
     * @param int $idAc
     */
    public function __construct(String $num, String $nom, String $secteur, $idDir, $idTcs, $idItc, $idAc)
    {
        $this->num = $num;
        $this->nom = $nom;
        $this->secteur = $secteur;
        $this->idDir = $idDir;
        $this->idTcs = $idTcs;
        $this->idItc = $idItc;
        $this->idAc = $idAc;
    }

    /**
     * @return String
     */
    public function getNum(): String
    {
        return $this->num;
    }

    /**
     * @param String $num
     */
    public function setNum(String $num): void
    {
        $this->num = $num;
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
    public function getSecteur(): String
    {
        return $this->secteur;
    }

    /**
     * @param String $secteur
     */
    public function setSecteur(String $secteur): void
    {
        $this->secteur = $secteur;
    }

    /**
     * @return int|null
     */
    public function getIdDir(): ?int
    {
        return $this->idDir;
    }

    /**
     * @param int|null $idDir
     */
    public function setIdDir(?int $idDir): void
    {
        $this->idDir = $idDir;
    }

    /**
     * @return int|null
     */
    public function getIdTcs(): ?int
    {
        return $this->idTcs;
    }

    /**
     * @param int|null $idTcs
     */
    public function setIdTcs(?int $idTcs): void
    {
        $this->idTcs = $idTcs;
    }

    /**
     * @return int|null
     */
    public function getIdItc(): ?int
    {
        return $this->idItc;
    }

    /**
     * @param int|null $idItc
     */
    public function setIdItc(?int $idItc): void
    {
        $this->idItc = $idItc;
    }

    /**
     * @return int|null
     */
    public function getIdAc(): ?int
    {
        return $this->idAc;
    }

    /**
     * @param int|null $idAc
     */
    public function setIdAc(?int $idAc): void
    {
        $this->idAc = $idAc;
    }




    public function getUrl() : string
    {
        return "/contact/".$this->getNum();
    }




}