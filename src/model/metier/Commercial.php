<?php
/**
 * Created by PhpStorm.
 * User: hugow
 * Date: 24/06/2018
 * Time: 10:51
 */

namespace src\model\metier;


class Commercial
{

    const IMAGE_DIR = '/upload/commerciaux/';
    const NO_IMAGE = '/img/nophoto.jpg';

    /**
     * @var
     */
    private $id;
    /**
     * @var
     */
    private $nom;
    /**
     * @var
     */
    private $prenom;
    /**
     * @var
     */
    private $numRepr;
    /**
     * @var
     */
    private $fonction;
    /**
     * @var
     */
    private $mail;
    /**
     * @var
     */
    private $tel;
    /**
     * @var
     */
    private $fax;
    /**
     * @var
     */
    private $image;

    /**
     * Commercial constructor.
     * @param $id
     * @param $nom
     * @param $prenom
     * @param $numRepr
     * @param $fonction
     * @param $tel
     * @param $fax
     * @param $image
     */
    public function __construct($id, $nom, $prenom, $numRepr, $fonction, $mail,  $tel, $fax, $image)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->numRepr = $numRepr;
        $this->fonction = $fonction;
        $this->mail = $mail;
        $this->tel = $tel;
        $this->fax = $fax;
        $this->image = $image;
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
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getNumRepr()
    {
        return $this->numRepr;
    }

    /**
     * @param mixed $numRepr
     */
    public function setNumRepr($numRepr): void
    {
        $this->numRepr = $numRepr;
    }

    /**
     * @return mixed
     */
    public function getFonction()
    {
        return $this->fonction;
    }

    /**
     * @param mixed $fonction
     */
    public function setFonction($fonction): void
    {
        $this->fonction = $fonction;
    }

    /**
     * @return mixed
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel): void
    {
        $this->tel = $tel;
    }

    /**
     * @return mixed
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param mixed $fax
     */
    public function setFax($fax): void
    {
        $this->fax = $fax;
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
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * @param mixed $mail
     */
    public function setMail($mail): void
    {
        $this->mail = $mail;
    }






}