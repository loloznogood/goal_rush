<?php


namespace src\model\metier;

use \src\model\ToolModel;
/**
 * Class Actualite
 * @package src\model\metier
 */
class Actualite
{

    const IMAGE_DIR = '/upload/news/';

    const NO_IMAGE = '/img/nophoto.jpg';

    /**
     * @var Integer|null $id identifiant de la news
     */
    private $id;
    /**
     * @var String $type type de la news
     */
    private $type;
    /**
     * @var String $intitule intitule de la news
     */
    private $intitule;
    /**
     * @var String $description description de la news
     */
    private $description;
    /**
     * @var String $contenu contenu de la news
     */
    private $contenu;
    /**
     * @var String $dateDebut date de debut de la news
     */
    private $dateDebut;
    /**
     * @var null|String $dateFin date de fin de la news
     */
    private $dateFin;
    /**
     * @var String $lien lien d'information supplémentaire
     */
    private $lien;
    /**
     * @var string $image url de l'image
     */
    private $image;

    /**
     * @var boolean $carousel si la news doit aparaitre dans la carousel
     */
    private $carousel;

    /**
     * Actualite constructor.
     * @param int|null $id
     * @param String $type
     * @param String $intitule
     * @param String $description
     * @param String $contenu
     * @param String $dateDebut
     * @param String|null $dateFin
     * @param String $lien
     * @param string $image
     * @param bool $carousel
     */
    public function __construct(?int $id, String $type, String $intitule, String $description, String $contenu, String $dateDebut, ?String $dateFin, String $lien, string $image, bool $carousel)
    {
        $this->id = $id;
        $this->type = $type;
        $this->intitule = $intitule;
        $this->description = $description;
        $this->contenu = $contenu;
        $this->dateDebut = $dateDebut;
        if( empty($dateFin) ){
            $this->dateFin = null;
        }
        else{
            $this->dateFin = $dateFin;
        }
        $this->dateFin = $dateFin;
        $this->lien = $lien;
        $this->image = $image;
        $this->carousel = $carousel;

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
    public function getDescription(): String
    {
        return $this->description;
    }

    /**
     * @param String $description
     */
    public function setDescription(String $description): void
    {
        $this->description = $description;
    }

    /**
     * @return String
     */
    public function getContenu(): String
    {
        return $this->contenu;
    }

    /**
     * @param String $contenu
     */
    public function setContenu(String $contenu): void
    {
        $this->contenu = $contenu;
    }

    /**
     * @return String
     */
    public function getDateDebut(): String
    {
        return $this->dateDebut;
    }

    /**
     * @param String $dateDebut
     */
    public function setDateDebut(String $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return null|String
     */
    public function getDateFin(): ?String
    {

        if(empty($this->dateFin)){
            return null;
        }
        else{
            return $this->dateFin;
        }
    }

    /**
     * @param null|String $dateFin
     */
    public function setDateFin(?String $dateFin): void
    {
        $this->dateFin = $dateFin;
    }


    /**
     * @return String
     */
    public function getLien(): String
    {
        return $this->lien;
    }

    /**
     * @param String $lien
     */
    public function setLien(String $lien): void
    {
        $this->lien = $lien;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @return bool
     */
    public function isCarousel(): bool
    {
        return $this->carousel;
    }

    /**
     * @param bool $carousel
     */
    public function setCarousel(bool $carousel): void
    {
        $this->carousel = $carousel;
    }

    /**
     * @return bool
     */
    public function isNew(): bool
    {
        $now = new \DateTime("now");
        $dateNews = new \DateTime($this->getDateDebut());
        $interval = new \DateInterval('P1M');
        $dateNews->add($interval); //date news + 1 mois
        return $now < $dateNews;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        $now = new \DateTime("now");
        $dateNews = new \DateTime($this->getDateFin());
        return $now < $dateNews;
    }

    /**
     * @return string
     */
    public function getDateDebutFR(): string
    {
        $dateNews = new \DateTime($this->getDateDebut());
        return $dateNews->format('d/m/Y');
    }

    /**
     * @return string
     */
    public function getDateFinFR(): string
    {
        $dateNews = new \DateTime($this->getDateFin());
        return $dateNews->format('d/m/Y');
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        if( !empty(trim($this->getImage()))
            && file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$this->getImage()) 
            && !is_dir($_SERVER['DOCUMENT_ROOT'].'/'.$this->getImage()) 
        ){
            return $this->getImage();
        }
        else{
            return self::NO_IMAGE;
        }
    }

    /**
     * @return mixed
     */
    public function getContenuDecode()
    {
        return htmlspecialchars_decode($this->contenu, ENT_HTML5);
    }

    public function getUrl(){
        $t = new ToolModel();
        $intitule = $t->urlFriendly($this->intitule);
        return '/news/'.$this->getId().'/'.$intitule;
    }



}