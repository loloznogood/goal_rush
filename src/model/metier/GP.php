<?php


namespace src\model\metier;

use \src\model\ToolModel;

class GP
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
     * @var String $intitule intitule du groupe produit
     */
    private $intitule;

    /**
     * @var
     */
    private $description1;

    /**
     * @var
     */
    private $description2;
    /**
     * @var array
     */
    private $namescarac;

    /**
     * @var
     */
    private $carac;

    /**
     * @var
     */
    private $typelabel;

    /**
     * @var
     */
    private $automatique;

    /**
     * GP constructor.
     * @param $lp
     * @param $ga
     * @param $gp
     * @param $intitule
     * @param $description1
     * @param $description2
     * @param $namescarac
     * @param $carac
     * @param $typelabel
     * @param $automatique
     */
    public function __construct($lp, $ga, $gp, $intitule, $description1, $description2, $namescarac, $carac, $typelabel, $automatique)
    {
        $this->lp = $lp;
        $this->ga = $ga;
        $this->gp = $gp;
        $this->intitule = $intitule;
        $this->description1 = $description1;
        $this->description2 = $description2;
        $this->namescarac = $namescarac;
        $this->carac = $carac;
        $this->typelabel = $typelabel;
        $this->automatique = $automatique;
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
    public function getDescription1()
    {
        return $this->description1;
    }

    /**
     * @param mixed $description1
     */
    public function setDescription1($description1): void
    {
        $this->description1 = $description1;
    }

    /**
     * @return mixed
     */
    public function getDescription2()
    {
        return $this->description2;
    }

    /**
     * @param mixed $description2
     */
    public function setDescription2($description2): void
    {
        $this->description2 = $description2;
    }

    /**
     * @return array
     */
    public function getNamescarac()
    {
        return $this->namescarac;
    }

    /**
     * @param array $namescarac
     */
    public function setNamescarac($namescarac): void
    {
        $this->namescarac = $namescarac;
    }

    /**
     * @return mixed
     */
    public function getCarac()
    {
        return $this->carac;
    }

    /**
     * @param mixed $carac
     */
    public function setCarac($carac): void
    {
        $this->carac = $carac;
    }

    /**
     * @return mixed
     */
    public function getTypelabel()
    {
        return $this->typelabel;
    }

    /**
     * @param mixed $typelabel
     */
    public function setTypelabel($typelabel): void
    {
        $this->typelabel = $typelabel;
    }

    /**
     * @return mixed
     */
    public function getAutomatique()
    {
        return $this->automatique;
    }

    /**
     * @param mixed $automatique
     */
    public function setAutomatique($automatique): void
    {
        $this->automatique = $automatique;
    }

    /**
     * @return mixed
     */
    public function getLien()
    {
        $t = new ToolModel();
        $intitule = $t->urlFriendly($this->intitule);
        return "http://www.jumo.fr/produits/fr/" . $this->getNumProduit() . "/" . $intitule . ".html";
    }

    /**
     * @return string
     */
    public function getImageUrl()
    {
        $imgPath = "upload/produits/" . $this->getNumProduit() . '.png';
        if (file_exists($imgPath)) {
            return self::IMAGE_DIR . $this->getNumProduit() . '.png';
        } else {
            return self::NO_IMAGE;
        }
    }

    /**
     * @return string
     */
    public function getImageSmallUrl()
    {
        $imgPath = "upload/produits/" . $this->getNumProduit() . '_s.png';
        if (file_exists($imgPath)) {
            return self::IMAGE_DIR . $this->getNumProduit() . '_s.png';
        } else {
            return self::NO_IMAGE_PRODUCT;
        }
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        $t = new ToolModel();
        $intitule = $t->urlFriendly($this->getIntitule());
        return '/produits/gp-' . $this->getLp() . '-' . $this->getGa() . '-' . $this->getGp() . '/' . $intitule;
    }


    /**
     * @return string
     */
    public function getNumProduit()
    {
        $lp = $this->getLp();
        $ga = $this->getGa();
        $gp = $this->getGp();
        if (strlen($lp) == 1) {
            $lp = '0' . $lp;
        }
        if (strlen($ga) == 1) {
            $ga = '0' . $ga;
        }
        if (strlen($gp) == 1) {
            $gp = '0' . $gp;
        }
        return $lp . $ga . $gp;
    }

    public function getDescription1Decode()
    {
        return htmlspecialchars_decode($this->description1, ENT_HTML5);
    }

    public function getDescription2Decode()
    {
        return htmlspecialchars_decode($this->description2, ENT_HTML5);
    }

    public function toArray()
    {
        $res = [
            'lp' => $this->getLp(),
            'ga' => $this->getGa(),
            'gp' => $this->getGp(),
            'intitule' => $this->getIntitule(),
            'description1' => $this->getDescription1(),
            'description2' => $this->getDescription2(),
            'namescarac' => $this->getNamescarac(),
            'carac' => $this->getCarac(),
            'lien' => $this->getLien(),
            'typelabel' => $this->getTypelabel(),
            'automatique' => $this->getAutomatique()
        ];
        return $res;
    }
}
