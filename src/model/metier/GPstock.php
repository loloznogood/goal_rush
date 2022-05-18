<?php


namespace src\model\metier;

use \src\model\ToolModel;

class GPstock
{
    /**
     *
     */
    const IMAGE_DIR = '/upload/produits/';

    const NO_IMAGE_PRODUCT = '/img/nophoto-product.jpg';
    const NO_IMAGE = '/img/nophoto.jpg';

    /**
     * @var
     */
    private $tn;

    /**
     * @var
     */
    private $lp;

    /**
     * @var
     */
    private $ga;

    /**
     * @var
     */
    private $gp;

    /**
     * @var String $designation
     */
    private $designation;

    /**
     * @var
     */
    private $texte;


    /**
     * GP constructor.
     * @param $tn
     * @param $lp
     * @param $ga
     * @param $gp
     * @param $designation
     * @param $texte
     */
    public function __construct($tn, $lp, $ga, $gp, $designation, $texte)
    {
        $this->tn = $tn;
        $this->lp = $lp;
        $this->ga = $ga;
        $this->gp = $gp;
        $this->designation = $designation;
        $this->texte = $texte;

    }

    /**
     * @return mixed
     */
    public function getTn()
    {
        return $this->tn;
    }

    /**
     * @param mixed $lp
     */
    public function setTn($tn): void
    {
        $this->tn = $tn;
    }

    /**
     * @return mixed
     */
    public function getLp()
    {
        return $this->lp;
    }

    /**
     * @param mixed $lp
     */
    public function setLp($lp): void
    {
        $this->lp = $lp;
    }

    /**
     * @return mixed
     */
    public function getGa()
    {
        return $this->ga;
    }

    /**
     * @param mixed $ga
     */
    public function setGa($ga): void
    {
        $this->ga = $ga;
    }

    /**
     * @return mixed
     */
    public function getGp()
    {
        return $this->gp;
    }

    /**
     * @param mixed $gp
     */
    public function setGp($gp): void
    {
        $this->gp = $gp;
    }

    /**
     * @return String
     */
    public function getDesignation()
    {
        return $this->designation;
    }

    /**
     * @param mixed $designation
     */
    public function setdesignation($designation): void
    {
        $this->designation = $designation;
    }

    /**
     * @return mixed
     */
    public function getTexte()
    {
        return $this->texte;
    }

    /**
     * @param mixed $texte
     */
    public function setTexte($texte): void
    {
        $this->texte = $texte;
    }

    /**
     * @return mixed
     */
    public function getLien()
    {
        $t = new ToolModel();
        $designation = $t->urlFriendly($this->designation);
        return "http://www.jumo.fr/produits/fr/".$this->getNumProduit()."/".$designation.".html";
    }

        /**
     * @return mixed
     */
    public function getLienPrix()
    {
        $t = new ToolModel();
        $designation = $t->urlFriendly($this->designation);
        $tn = $t->urlFriendly($this->tn);
        return "http://www.jumo.fr/produits/contact/".$this->getNumProduit()."/feedback.html?articleNo=".$tn."&parentId=";
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        $imgPath = "upload/produits/" . $this->getLp() . $this->getGa() . $this->getGp() . '.png';
        if(file_exists($imgPath)){
            return self::IMAGE_DIR . $this->getLp() . $this->getGa() . $this->getGp() . '.png';;
        }
        else{
            return self::NO_IMAGE;
        }

    }

    /**
     * @return string
     */
    public function getImageSmallUrl()
    {
        $imgPath = "upload/produits/" . $this->getNumProduit() . '_s.png';
        if(file_exists($imgPath)){
            return self::IMAGE_DIR . $this->getNumProduit(). '_s.png';;
        }
        else{
            return self::NO_IMAGE_PRODUCT;
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $t = new ToolModel();
        $designation = $t->urlFriendly($this->getDesignation());
        return '/catalogue-stock/gp-'.$this->getTn().'-'.$this->getLp().'-'.$this->getGa().'-'.$this->getGp(). '/'.$designation;
    }


    public function getUrlStock()
    {
        $t = new ToolModel();
        $designation = $t->urlFriendly($this->getDesignation());
        return '/catalogue-stock/gp-'.$this->getTn().'-'.$this->getLp().'-'.$this->getGa().'-'.$this->getGp(). '/'.$designation;
    }

    /**
     * @return string
     */
    public function getNumProduit(){
        $lp = $this->getLp();
        $ga = $this->getGa();
        $gp = $this->getGp();
        if(strlen($lp) == 1){
            $lp = '0'.$lp;
        }
        if(strlen($ga) == 1){
            $ga = '0'.$ga;
        }
        if(strlen($gp) == 1){
            $gp = '0'.$gp;
        }
        return $lp.$ga.$gp;
    }

    public function getDescription1Decode()
    {
        return htmlspecialchars_decode($this->description1, ENT_HTML5);
    }

    public function getDescription2Decode()
    {
        return htmlspecialchars_decode($this->description2, ENT_HTML5);
    }

    public function toArray(){
        $res = [
            'tn' => $this->getTn(),
            'lp' => $this->getLp(),
            'ga' => $this->getGa(),
            'gp' => $this->getGp(),
            'designation' => $this->getDesignation(),
            'texte' => $this->getTexte()
        ];
        return $res;
    }


}