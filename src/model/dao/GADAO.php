<?php

namespace src\model\dao;

use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\GA;

class GADAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_ga";

    /**
     * @var
     */
    private static $instance;

    /**
     * gaDAO constructor.
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return GADAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new GADAO($dbCtrl);
        }
        return self::$instance;
    }


    /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function create($object)
    {
        $ga = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "INSERT INTO ".self::TABLE."(lp, ga, intitule) VALUES (:lp, :ga, :intitule);", array(
            "lp"        => $ga->getLp(),
            "ga"        => $ga->getGa(),
            "intitule"  => $ga->getIntitule()
        ));

        $this->getDatabaseCtrl()->query(
            "INSERT INTO jf_ga_gp_edite (lp, ga) VALUES (:lp, :ga);", array(
            "lp"        => $ga->getLp(),
            "ga"        => $ga->getGa()
        ));
    }

    /**
     * @return mixed
     */
    public function update($object)
    {
        $ga = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET intitule = :intitule WHERE lp = :lp AND ga = :ga ", array(
            "lp"        => $ga->getLp(),
            "ga"        => $ga->getGa(),
            "intitule"  => $ga->getIntitule()
        ));

        $exists = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM jf_ga_gp_edite WHERE lp = :lp AND ga = :ga AND gp IS NULL ;",
            ["lp" => $ga->getLp(), "ga" => $ga->getGa()]
        );

        if(!$exists){
            $this->getDatabaseCtrl()->query(
                "INSERT INTO jf_ga_gp_edite (lp, ga) VALUES (:lp, :ga);",
                ["lp"=> $ga->getLp(), "ga" => $ga->getGa()]
            );
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function updateTotal($object, $newObject=null)
    {
        if(is_null($newObject)){
            $this->update($object);
        }
        else{
            $ga = $this->getCorectInstance($object);
            $gaNew = $this->getCorectInstance($newObject);

            $this->getDatabaseCtrl()->query(
                "UPDATE ".self::TABLE." SET lp = :lpNew, ga = :gaNew, intitule = :intitule WHERE lp = :lp AND ga = :ga ", array(
                "lp"        => $ga->getLp(),
                "ga"        => $ga->getGa(),
                "intitule"  => $gaNew->getIntitule(),
                "lpNew"        => $gaNew->getLp(),
                "gaNew"        => $gaNew->getGa()
            ));

            $exists = $this->getDatabaseCtrl()->queryFetch(
                "SELECT * FROM jf_ga_gp_edite WHERE lp = :lp AND ga = :ga AND gp IS NULL",
                ["lp" => $ga->getLp(), "ga" => $ga->getGa()]
            );

            if(!$exists){
                $this->getDatabaseCtrl()->query(
                    "INSERT INTO jf_ga_gp_edite (lp, ga) VALUES (:lp, :ga);",
                    ["lp"=> $ga->getLp(), "ga" => $ga->getGa()]
                );
            }
            else{
                $this->getDatabaseCtrl()->query(
                    "UPDATE jf_ga_gp_edite SET lp = :lpNew, ga = :gaNew WHERE lp = :lp AND ga = :ga AND gp IS NULL ", array(
                    "lp"        => $ga->getLp(),
                    "ga"        => $ga->getGa(),
                    "lpNew"        => $gaNew->getLp(),
                    "gaNew"        => $gaNew->getGa(),
                    "gpNew"        => $gaNew->getGp()
                ));
            }
        }
    }

    /**
     * @return mixed
     */
    public function delete($object)
    {
        $ga = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM ".self::TABLE." WHERE lp = :lp AND ga = :ga",
            ["lp" => $ga->getLp(), "ga" => $ga->getGa()]
        );

        $this->deleteSave($object);

    }

    /**
     * @param $object
     * @throws \Exception
     */
    public function deleteSave($object)
    {
        $gp = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM jf_ga_gp_edite WHERE lp = :lp AND ga = :ga AND gp IS NULL;",
            [
                "lp" => $gp->getLp(),
                "ga" => $gp->getGa()
            ]
        );

    }

    /**
     * @param $pk : primary key
     * @return mixed
     */
    public function find($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE lp = :lp AND ga = :ga",
            [
                'lp'=> $pk['lp'],
                'ga' => $pk['ga']
            ]
        );
        if(!$ret){
            $result =  null;
        }
        else {
            $result = new GA($ret['lp'], $ret['ga'], $ret['intitule']);
        }
        return $result;
    }

    /**
     * Fonction retournant tous les groupes articles appartenant a la ligne de produit
     * identifie par un numero de lp.
     * @param Integer $lp numero de ligne de produit
     * @return array
     */
    public function findByLp($lp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE lp = :lp", ['lp' => $lp]);
        $result = array();
        foreach( $ret as $v){
            $result[] = new GA($v['lp'], $v['ga'], $v['intitule']);
        }
        return $result;
    }
    /**
     * Fonction retournant tous les groupes articles appartenant a la ligne de produit
     * identifie par un identifiant de lp.
     * @param Integer $lpId identifiant de ligne de produit
     * @return array
     */
    public function findByLpId($lpId)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll(
            "
                  SELECT DISTINCT jf_ga.lp, jf_ga.ga, jf_ga.intitule FROM jf_ga, jf_lp, jf_lp_perso_contient_gp
                  WHERE jf_lp.id = :id 
                  AND jf_lp.id = jf_lp_perso_contient_gp.id
                  AND jf_lp_perso_contient_gp.lp = jf_ga.lp
                  AND jf_lp_perso_contient_gp.ga = jf_ga.ga
                ",
            ['id' => $lpId]);
        $result = array();
        foreach( $ret as $v){
            $result[] = new GA($v['lp'], $v['ga'], $v['intitule']);
        }
        return $result;
    }


    /**
     * @return mixed
     */
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE, null);
        $result = array();
        foreach( $ret as $v){
            $result[] = new GA($v['lp'], $v['ga'], $v['intitule']);
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function findAllEdited()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll(
            "SELECT * FROM ".self::TABLE." as A, jf_ga_gp_edite as B
                 WHERE B.lp = A.lp AND B.ga = A.ga AND B.gp IS NULL", null);
        $result = array();
        foreach( $ret as $v){
            $result[] = new GA($v['lp'], $v['ga'], $v['intitule']);
        }
        return $result;
    }

    /**
     * @param $object
     * @return \src\model\metier\GA
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\GA {
        if($object instanceof \src\model\metier\GA){
            return $object;
        }
        else{
            throw new \Exception("Instance incorrect.");
        }
    }

    /**
     * @param $searchKey
     * @return mixed
     */
    public function search($searchKey)
    {
        $result = array();
        if(is_string($searchKey)){
            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE LOWER(intitule) LIKE :searchKey ", [
                'searchKey' => strtolower('%'.$searchKey.'%')
            ]);
            foreach( $ret as $v){
                $result[] = new GA($v['lp'], $v['ga'], $v['intitule']);
            }
        }

        if( (is_numeric($searchKey)) && (strlen($searchKey) == 4) ){

            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE CONCAT(FORMAT(lp,'00','en-US'),FORMAT(ga,'00','en-US')) = :searchKey",[
                'searchKey'=> $searchKey
            ]);
            foreach( $ret as $v){
                $result[] = new GA($v['lp'], $v['ga'], $v['intitule']);
            }
        }
        return $result;
    }

    public function findGaStock($lp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll(
            "
            SELECT DISTINCT jf_catalogue_stock.ga, jf_catalogue_stock.lp, jf_ga.intitule FROM jf_ga, jf_catalogue_stock WHERE jf_ga.lp = :lp AND jf_catalogue_stock.lp = jf_ga.lp AND jf_catalogue_stock.ga = jf_ga.ga
                ", ['lp' => $lp]);
        $result = array();
        foreach( $ret as $v){
            $result[] = new GA($v['lp'], $v['ga'], $v['intitule']);
        }
        return $result;
    }
    

}