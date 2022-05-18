<?php

namespace src\model\dao;

use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\LP;

/**
 * Class LPDAO
 * @package src\model\dao
 */
class LPDAO extends AbstractMetierDAO implements DAOInterface
{

    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_lp";

    /**
     * @var
     */
    private static $instance;

    /**
     * LPDAO constructor.
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }

    /**
     * @param $db
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new LPDAO($dbCtrl);
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
        $lp = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "INSERT INTO ".self::TABLE."(lp, intitule, ordre, image, fabfr) VALUES (:lp, :intitule, :ordre, :image, :fabfr);", array(
                "lp"        => $lp->getLp(),
                "intitule"  => $lp->getIntitule(),
                "ordre"     => $lp->getOrdre(),
                "image"     => $lp->getImage(),
                "fabfr"     => $lp->getFabfr()
        ));
    }

    /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function update($object)
    {
        $lp = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET lp = :lp, intitule = :intitule, ordre = :ordre, image = :image, fabfr = :fabfr WHERE id = :id ", array(
                "id"        => $lp->getId(),
                "lp"        => $lp->getLp(),
                "intitule"  => $lp->getIntitule(),
                "ordre"     => $lp->getOrdre(),
                "image"     => $lp->getImage(),
                "fabfr"     => $lp->getFabfr()
        ));
    }

    /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function delete($object)
    {
        $lp = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM ".self::TABLE." WHERE id = :id ",
            ["id"=> $lp->getId()]
        );

    }

    /**
     * @param $pk
     * @return mixed|void
     */
    public function find($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE id = :id",
            ['id'=> $pk]
        );
        if(!$ret){
            $result = null;
        }
        else{
            $result = new LP($ret['id'], $ret['lp'], $ret['intitule'], $ret['ordre'], $ret['image'], $ret['fabfr']);
        }
        return $result;
    }

    /**
     * @param $lp
     * @return mixed|void
     */
    public function findByLp($lp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE lp = :lp",
            ['lp'=> $lp]
        );
        if(!$ret){
            $result = null;
        }
        else{
            $result = new LP($ret['id'], $ret['lp'], $ret['intitule'], $ret['ordre'], $ret['image'], $ret['fabfr']);
        }
        return $result;
    }


    /**
     * @return mixed|void
     */
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." ORDER BY ordre ASC", null);
        $result = array();
        foreach( $ret as $v){
            $result[] = new LP($v['id'], $v['lp'], $v['intitule'], $v['ordre'], $v['image'], $v['fabfr']);
        }
        return $result;
    }

    /**
     * @return mixed|void
     */
    public function findAllOther($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE id <> :id ORDER BY ordre ASC",
            ['id' => $pk]);
        $result = array();
        foreach( $ret as $v){
            $result[] = new LP($v['id'], $v['lp'], $v['intitule'], $v['ordre'], $v['image'], $v['fabfr']);
        }
        return $result;
    }

    /**
     * @return mixed|void
     */
    public function findAllTrueLP()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE lp IS NOT NULL ORDER BY ordre ASC");
        $result = array();
        foreach( $ret as $v){
            $result[] = new LP($v['id'], $v['lp'], $v['intitule'], $v['ordre'], $v['image'], $v['fabfr']);
        }
        return $result;
    }


    /**
     * @return mixed|void
     */
    public function findAllPersonalizedLP()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE lp IS NULL ORDER BY ordre ASC");
        $result = array();
        foreach( $ret as $v){
            $result[] = new LP($v['id'], $v['lp'], $v['intitule'], $v['ordre'], $v['image'], $v['fabfr']);
        }
        return $result;
    }

    /**
     * @return mixed|void
     */
    public function findAllPersonalizedLPByGP($lp, $ga, $gp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT A.* FROM jf_lp A, jf_lp_perso_contient_gp B  WHERE B.lp = :lp AND  B.ga = :ga AND B.gp = :gp AND B.id = A.id ",
            ['lp'=>$lp, 'ga'=>$ga, 'gp'=>$gp]);
        $result = array();
        foreach( $ret as $v){
            $result[] = new LP($v['id'], $v['lp'], $v['intitule'], $v['ordre'], $v['image'], $v['fabfr']);
        }
        return $result;
    }


    /**
     * @return mixed|void
     */
    public function findAllConcernedByGa($lp,$ga)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll(
            " SELECT DISTINCT jf_lp.id, jf_lp.lp, jf_lp.intitule, jf_lp.ordre, jf_lp.image, jf_lp.fabfr
                    FROM jf_ga, jf_lp 
                    WHERE jf_lp.lp = :lp
                    AND jf_lp.lp = jf_ga.lp
                    AND ga = :ga
                    UNION
                    SELECT DISTINCT jf_lp.id, jf_lp.lp, jf_lp.intitule, jf_lp.ordre, jf_lp.image, jf_lp.fabfr
            FROM jf_lp, jf_lp_perso_contient_gp
            WHERE jf_lp.id = jf_lp_perso_contient_gp.id 
            AND jf_lp_perso_contient_gp.lp = :lp
            AND jf_lp_perso_contient_gp.ga = :ga
                ",
            [
                'ga' => $ga,
                'lp' => $lp,
            ]);
        $result = array();
        foreach( $ret as $v){
            $result[] = new LP($v['id'], $v['lp'], $v['intitule'], $v['ordre'], $v['image'], $v['fabfr']);
        }
        return $result;
    }

    /**
     * @return mixed|void
     */
    public function getOrdreMax()
    {
        $ret = $this->getDatabaseCtrl()->queryFetch("SELECT MAX(ordre) as ordre FROM ".self::TABLE);
        if(!$ret){
            $result = null;
        }
        else{
            $result = $ret['ordre'];
        }
        return $result;
    }


    /**
     * @param $object
     * @return \src\model\metier\LP
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\LP {
        if($object instanceof \src\model\metier\LP){
            return $object;
        }
        else{
            throw new \Exception("Instance incorrect.");
        }
    }


    /**
     * @param $searchKey
     * @return array
     */
    public function search($searchKey)
    {
        $result = array();
        if(is_string($searchKey)){
            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE LOWER(intitule) LIKE :searchKey ", [
                'searchKey' => strtolower('%'.$searchKey.'%')
            ]);
            foreach( $ret as $v){
                $result[] = new LP($v['id'], $v['lp'], $v['intitule'], $v['ordre'], $v['image'], $v['fabfr']);
            }
        }

        if( (is_numeric($searchKey)) && (strlen($searchKey) == 2) ){

            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE lp = :searchKey",[
                'searchKey'=> $searchKey
            ]);
            foreach( $ret as $v){
                $result[] = new LP($v['id'], $v['lp'], $v['intitule'], $v['ordre'], $v['image'], $v['fabfr']);
            }
        }
        return $result;
    }


     /**
     * @return mixed|void
     */   
    public function findLpStock()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll(
            "
            SELECT DISTINCT jf_catalogue_stock.lp, id, intitule, ordre, image FROM jf_catalogue_stock, jf_lp WHERE jf_lp.lp = jf_catalogue_stock.lp
            ORDER BY ordre ASC", null);
        $result = array();
        foreach( $ret as $v){
            $result[] = new LP($v['id'], $v['lp'], $v['intitule'], $v['ordre'], $v['image'], $v['fabfr']);
        }
        return $result;
    }

}