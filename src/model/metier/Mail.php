<?php


namespace src\model\metier;


class Mail
{
    /**
     * @var String $nom_camp du mailing
     */
    private $nom_camp;
    /**
     * @var int nombre de personne ayant reçu le mailing.
     */
    private $comp_camp;
    /**
     * @var int nombre de personnes ayant ouvert le mailing.
     */
    private $nb_camp;
    /**
     * @var int $id du mailing.
     */
    private $id;

    /**
     * Page constructor.
     * @param String $nom_camp
     * @param int $comp_camp
     * @param int $nb_camp
     * @param int $id
     */

    public function __construct(int $id, String $nom_camp, int $comp_camp, int $nb_camp)
    {
        $this->nom_camp = $nom_camp;
        $this->comp_camp = $comp_camp;
        $this->nb_camp = $nb_camp;
        $this->id = $id;
    }

    /**
     * @return String
     */
    public function getNom_camp(): String
    {
        return $this->nom_camp;
    }

    /**
     * @param String $nom_camp
     */
    public function setNom_camp(String $nom_camp): void
    {
        $this->nom_camp = $nom_camp;
    }

    /**
     * @return int
     */
    public function getComp_camp(): String
    {
        return $this->comp_camp;
    }

    /**
     * @param int $comp_camp
     */
    public function setComp_camp(int $comp_camp): void
    {
        $this->comp_camp = $comp_camp;
    }

   /**
     * @return int
     */
    public function getNb_camp(): String
    {
        return $this->nb_camp;
    }

    /**
     * @param int $nb_camp
     */
    public function setNb_camp(int $nb_camp): void
    {
        $this->nb_camp = $nb_camp;
    }

     /**
     * @return int 
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

}