<?php


namespace src\model\metier;
use \src\model\ToolModel;

class GA
{
    const IMAGE_DIR = '/upload/ga/';

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
    private $intitule;


    /**
     * GA constructor.
     * @param $lp
     * @param $ga
     * @param $intitule
     * @param $image
     */
    public function __construct($lp, $ga, $intitule)
    {
        $this->lp = $lp;
        $this->ga = $ga;
        $this->intitule = $intitule;
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


    public function getImageUrl()
    {
        return self::IMAGE_DIR . $this->getImageUrl();
    }

    public function getUrl()
    {
        $t = new ToolModel();
        $intitule = $t->urlFriendly($this->intitule);
        return '/produits/ga-'.$this->getLp().'-'.$this->getGa().'/'.$intitule;
    }

    public function getUrlStock()
    {
        $t = new ToolModel();
        $intitule = $t->urlFriendly($this->intitule);
        return '/catalogue-stock/ga-'.$this->getLp().'-'.$this->getGa().'/'.$intitule;
    }

    public function toArray(){
        $res = [
            'lp' => $this->getLp(),
            'ga' => $this->getGa(),
            'intitule' => $this->getIntitule(),
        ];
        return $res;
    }


}