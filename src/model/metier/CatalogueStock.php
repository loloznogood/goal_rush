<?php

namespace src\model\metier;
use \src\model\ToolModel;


class CatalogueStock
{   
    /**
    * Constante pour le type catalogue standard integral 
    */
    const TYPE_ENTIER = "entier";
    /**
    * Constante pour le type catalogue standard par ligne de produit
    */
    const TYPE_LP = "parLP";
    /**
    * Constante pour le type catalogue standard par groupe produit
    */
    const TYPE_GP = "parGP";
    /**
    *Constante de l'emplacement ou sauvgarder les catalogue standards PDF
    */
    const DIR_PDF = "/upload/cataloguestandard/pdf/";
    /**
    *Constante de l'emplacement ou sauvgarder les catalogue standards Magzine
    */
    const DIR_MAG = "/upload/cataloguestandard/magazine/";

    /**
     * @var Integer|null $id identifiant du catalogue standard
     */
    private $id;
    /**
     * @var Integer|null $lpId idantifiant de ligne de produit
     */

    /**
     * @var Integer|null $lp numero de ligne de produit
     */
    private $lp;
    /**
     * @var Integer|null $ga numero de groupe article
     */

    /**
     * @var Integer|null $gp numero de groupe produit
     */

    /**
     * @var String $type type du catalgue standard
     */

    /**
     * @var String $designation designation du catalgue standard
     */
    private $designation;
    /**
     * @var String $dateAjout date de mise a jour du catalogue standard
     */
    private $dateAjout;
    /**
     * @var String $fichierPdf chemin du fichier pdf du catalogue standard
     */

    /**
     * @var String $fichierMag chemin du fichier magazine du catalogue standard
     */

    /**
     * @var Boolean $actifPdf indique si le fichier pdf est actif ou non
     */
   
    /**
     * @var Boolean $actifMag indique si le fichier magazine est actif ou non
     */
    private $texte;

    /**
     * CatalogueStandard constructor.
     * @param int|null $id
   
     * @param int|null $lp


     * @param String $designation
     * @param String $dateAjout
     * @param String $texte
     */
    public function __construct(?int $id, ?int $lp, String $designation, String $dateAjout, String $texte)
    {
        $this->id = $id;
        $this->lp = $lp;
        $this->designation = $designation;
        $this->dateAjout = $dateAjout;
        $this->texte = $texte;
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
     * @return int|null
     */
    public function getLp(): ?int
    {
        return $this->lp;
    }

    /**
     * @param int|null $lp
     */
    public function setLp(?int $lp): void
    {
        $this->lp = $lp;
    }

    /**
     * @return String
     */
    public function getdesignation(): String
    {
        return $this->designation;
    }

    /**
     * @param String $designation
     */
    public function setdesignation(String $designation): void
    {
        $this->designation = $designation;
    }

    /**
     * @return String
     */
    public function getdateAjout(): String
    {
        return $this->dateAjout;
    }

    /**
     * @param String $dateAjout
     */
    public function setdateAjout(String $dateAjout): void
    {
        $this->dateAjout = $dateAjout;
    }

    public function getTexte(): String
    {
        return $this->texte;
    }

    /**
     * @param String $dateAjout
     */
    public function setTexte(String $texte): void
    {
        $this->texte = $texte;
    }


    /**
     * @return string
     */
    public function getUrl(): String
    {
        $t = new ToolModel();
        $designation = $t->urlFriendly($this->designation);
        return '#';
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        if(!$this->actifPdf && !$this->actifMag){
            return [];
        }
        elseif ($this->actifPdf && !$this->actifMag){
            return [
                'pdf' => $this->fichierPdf
            ];
        }
        elseif (!$this->actifPdf && $this->actifMag){
            return  [
                'magazine' => $this->fichierMag
            ];
        }
        else{
            return  [
                'pdf' => $this->fichierPdf,
                'magazine' => $this->fichierMag
            ];
        }
    }



}