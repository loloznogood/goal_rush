<?php

namespace src\model\metier;
use \src\model\ToolModel;


class CatalogueStandard
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
    private $lpId;
    /**
     * @var Integer|null $lp numero de ligne de produit
     */
    private $lp;
    /**
     * @var Integer|null $ga numero de groupe article
     */
    private $ga;
    /**
     * @var Integer|null $gp numero de groupe produit
     */
    private $gp;
    /**
     * @var String $type type du catalgue standard
     */
    private $type;
    /**
     * @var String $intitule intitule du catalgue standard
     */
    private $intitule;
    /**
     * @var String $dateMaj date de mise a jour du catalogue standard
     */
    private $dateMaj;
    /**
     * @var String $fichierPdf chemin du fichier pdf du catalogue standard
     */
    private $fichierPdf;
    /**
     * @var String $fichierMag chemin du fichier magazine du catalogue standard
     */
    private $fichierMag;
    /**
     * @var Boolean $actifPdf indique si le fichier pdf est actif ou non
     */
    private $actifPdf;
    /**
     * @var Boolean $actifMag indique si le fichier magazine est actif ou non
     */
    private $actifMag;

    /**
     * CatalogueStandard constructor.
     * @param int|null $id
     * @param int|null $lpId
     * @param int|null $lp
     * @param int|null $ga
     * @param int|null $gp
     * @param String $type
     * @param String $intitule
     * @param String $dateMaj
     * @param String $fichierPdf
     * @param String $fichierMag
     * @param bool $actifPdf
     * @param bool $actifMag
     */
    public function __construct(?int $id, ?int $lpId, ?int $lp, ?int $ga, ?int $gp, String $type, String $intitule, String $dateMaj, String $fichierPdf, String $fichierMag, bool $actifPdf, bool $actifMag)
    {
        $this->id = $id;
        $this->lpId = $lpId;
        $this->lp = $lp;
        $this->ga = $ga;
        $this->gp = $gp;
        $this->type = $type;
        $this->intitule = $intitule;
        $this->dateMaj = $dateMaj;
        $this->fichierPdf = $fichierPdf;
        $this->fichierMag = $fichierMag;
        $this->actifPdf = $actifPdf;
        $this->actifMag = $actifMag;
    }

    /**
     * @return int|null
     */
    public function getLpId(): ?int
    {
        return $this->lpId;
    }

    /**
     * @param int|null $lpId
     */
    public function setLpId(?int $lpId): void
    {
        $this->lpId = $lpId;
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
     * @return int|null
     */
    public function getGa(): ?int
    {
        return $this->ga;
    }

    /**
     * @param int|null $ga
     */
    public function setGa(?int $ga): void
    {
        $this->ga = $ga;
    }

    /**
     * @return int|null
     */
    public function getGp(): ?int
    {
        return $this->gp;
    }

    /**
     * @param int|null $gp
     */
    public function setGp(?int $gp): void
    {
        $this->gp = $gp;
    }

    /**
     * @return String
     */
    public function getType(): String
    {
        return $this->type;
    }

    /**
     * @param String $type
     */
    public function setType(String $type): void
    {
        $this->type = $type;
    }

    /**
     * @return String
     */
    public function getIntitule(): String
    {
        return $this->intitule;
    }

    /**
     * @param String $intitule
     */
    public function setIntitule(String $intitule): void
    {
        $this->intitule = $intitule;
    }

    /**
     * @return String
     */
    public function getDateMaj(): String
    {
        return $this->dateMaj;
    }

    /**
     * @param String $dateMaj
     */
    public function setDateMaj(String $dateMaj): void
    {
        $this->dateMaj = $dateMaj;
    }

    /**
     * @return String
     */
    public function getFichierPdf(): String
    {
        return $this->fichierPdf;
    }

    /**
     * @param String $fichierPdf
     */
    public function setFichierPdf(String $fichierPdf): void
    {
        $this->fichierPdf = $fichierPdf;
    }

    /**
     * @return String
     */
    public function getFichierMag(): String
    {
        return $this->fichierMag;
    }

    /**
     * @param String $fichierMag
     */
    public function setFichierMag(String $fichierMag): void
    {
        $this->fichierMag = $fichierMag;
    }

    /**
     * @return bool
     */
    public function isActifPdf(): bool
    {
        return $this->actifPdf;
    }

    /**
     * @param bool $actifPdf
     */
    public function setActifPdf(bool $actifPdf): void
    {
        $this->actifPdf = $actifPdf;
    }

    /**
     * @return bool
     */
    public function isActifMag(): bool
    {
        return $this->actifMag;
    }

    /**
     * @param bool $actifMag
     */
    public function setActifMag(bool $actifMag): void
    {
        $this->actifMag = $actifMag;
    }

    /**
     * @return string
     */
    public function getUrl(): String
    {
        $t = new ToolModel();
        $intitule = $t->urlFriendly($this->intitule);
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