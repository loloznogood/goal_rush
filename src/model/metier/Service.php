<?php

namespace src\model\metier;
use \src\model\ToolModel;

class Service
{

    /**
     *
     */
    const IMAGE_DIR = '/upload/services/';
    const NO_IMAGE = '/img/nophoto.jpg';

    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $intitule;

    /**
     * @var
     */
    private $description;

    /**
     * @var
     */
    private $contenu;

    /**
     * @var
     */
    private $image;

    /**
     * @var
     */
    private $ordre;

    /**
     * Service constructor.
     * @param $id
     * @param $intitule
     * @param $contenu
     * @param $image
     * @param $ordre
    */
    public function __construct($id, $intitule, $description, $contenu, $image, $ordre)
    {
        $this->id = $id;
        $this->intitule = $intitule;
        $this->description = $description;
        $this->contenu = $contenu;
        $this->image = $image;
        $this->ordre = $ordre;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIntitule()
    {
        return $this->intitule;
    }

    /**
     * @param mixed $intitule
     */
    public function setIntitule($intitule): void
    {
        $this->intitule = $intitule;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }



    /**
     * @return mixed
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * @return mixed
     */
    public function getContenuDecode()
    {
        return htmlspecialchars_decode($this->contenu, ENT_HTML5);
    }


    /**
     * @param mixed $contenu
     */
    public function setContenu($contenu): void
    {
        $this->contenu = $contenu;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * @param mixed $ordre
     */
    public function setOrdre($ordre): void
    {
        $this->ordre = $ordre;
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
    public function getUrl()
    {
        $t = new ToolModel();
        $intitule = $t->urlFriendly($this->intitule);
        return '/services/'.$this->getId().'/'.$intitule;
    }

}