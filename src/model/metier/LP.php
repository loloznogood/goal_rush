<?php

namespace src\model\metier;
use \src\model\ToolModel;

/**
 * Class LP
 * @package src\model\metier
 */
class LP
{
    /**
     *
     */
    const IMAGE_DIR = '/upload/lp/';

    const NO_IMAGE = '/img/nophoto.jpg';

    /**
     * @var string|null $id identifiant de la ligne de produit
     */
    private $id;

    /**
     * @var string|null $lp numero de la ligne de produit
     */
    private $lp;

    /**
     * @var string $intitule intitule de la ligne de produit
     */
    private $intitule;

    /**
     * @var int $ordre ordre d'apparaition de la ligne de produit
     */
    private $ordre;

    /**
     * @var string $image chemin d'acces du fichier image
     */
    private $image;

    /**
     * @var int $fabfr = = 1 si lp fabriquée en France
     */
    private $fabfr;

    /**
     * LP constructor.
     * @param null|string $id
     * @param string|null $lp
     * @param string $intitule
     * @param int $ordre
     * @param string $image
     * @param int $fabfr
     */
    public function __construct($id, $lp, $intitule, $ordre, $image, $fabfr)
    {
        $this->id = $id;
        $this->lp = $lp;
        $this->intitule = $intitule;
        $this->ordre = $ordre;
        $this->image = $image;
        $this->fabfr = $fabfr;
    }

    /**
     * @return null|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getLp()
    {
        return $this->lp;
    }

    /**
     * @param string|null $lp
     */
    public function setLp($lp)
    {
        $this->lp = $lp;
    }

    /**
     * @return string
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * @param string $intitule
     */
    public function setIntitule($intitule)
    {
        $this->intitule = $intitule;
    }

    /**
     * @return int
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * @param int $ordre
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return int
     */
    public function getFabfr()
    {
        return $this->fabfr;
    }

    /**
     * @param int $fabfr
     */
    public function setFabfr($fabfr)
    {
        $this->fabfr = $fabfr;
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
     * @return string
     */
    public function getUrl(){
        $t = new ToolModel();
        $intitule = $t->urlFriendly($this->intitule);
        return '/produits/lp-'.$this->getId().'-'.$this->getLp().'/'.$intitule;
    }

    /**
     * @return string
     */
    public function getUrlStock(){
        $t = new ToolModel();
        $intitule = $t->urlFriendly($this->intitule);
        return '/catalogue-stock/lp-'.$this->getId().'-'.$this->getLp().'/'.$intitule;
    }




}