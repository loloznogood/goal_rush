<?php

namespace src\model\dao;

use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\GPstock;

class GPDAOstock extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_catalogue_stock";


    /**
     * @var
     */
    private static $instance;


    /**
     * GPDAOstock constructor.
     * @param DatabaseController $dbCtrl
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return GPDAOstock
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new GPDAOstock($dbCtrl);
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
        $gp = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "INSERT INTO ".self::TABLE." (tn,lp, ga, gp, designation, texte) VALUES (:tn, :lp, :ga, :gp, :designation, :texte);", array(
            "tn"        => $gp->getTn(),   
            "lp"        => $gp->getLp(),
            "ga"        => $gp->getGa(),
            "gp"        => $gp->getGp(),
            "designation"  => $gp->getdesignation(),
            "texte"  => $gp->gettexte()
        ));
/*
        $this->getDatabaseCtrl()->query(
            "INSERT INTO jf_ga_gp_edite (lp, ga, gp) VALUES (:lp, :ga, :gp);", array(
            "lp"        => $gp->getLp(),
            "ga"        => $gp->getGa(),
            "gp"        => $gp->getGp(),
        )); */
    }


    /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function update($object)
    {
        $gp = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET designation = :designation , texte = :texte WHERE tn = :tn ;", array(
            "tn"        => $gp->getTn(),     
            "lp"        => $gp->getLp(),
            "ga"        => $gp->getGa(),
            "gp"        => $gp->getGp(),
            "designation"  => $gp->getdesignation(),
            "texte"  => $gp->gettexte()
        ));
/*
        $exists = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM jf_ga_gp_edite WHERE lp = :lp AND ga = :ga AND gp = :gp",
            ["lp" => $gp->getLp(), "ga" => $gp->getGa(), "gp" => $gp->getGp()]
        );

        if(!$exists){
            $this->getDatabaseCtrl()->query(
                "INSERT INTO jf_ga_gp_edite (lp, ga, gp) VALUES (:lp, :ga, :gp);",
                array("lp"=> $gp->getLp(), "ga" => $gp->getGa(), "gp" => $gp->getGp())
            );
        }
        */
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
            $gp = $this->getCorectInstance($object);
            $gpNew = $this->getCorectInstance($newObject);

            $this->getDatabaseCtrl()->query(
                "UPDATE ".self::TABLE." SET lp = :lpNew, ga = :gaNew, gp = :gpNew, designation = :designation , texte = :texte WHERE lp = :lp AND ga = :ga AND gp = :gp;", array(
                "lp"        => $gp->getLp(),
                "ga"        => $gp->getGa(),
                "gp"        => $gp->getGp(),
                "designation"  => $gpNew->getdesignation(),
                "texte"  => $gpNew->gettexte(),
                "lpNew"        => $gpNew->getLp(),
                "gaNew"        => $gpNew->getGa(),
                "gpNew"        => $gpNew->getGp()
            ));
/*
            $exists = $this->getDatabaseCtrl()->queryFetch(
                "SELECT * FROM jf_ga_gp_edite WHERE lp = :lp AND ga = :ga AND gp = :gp",
                ["lp" => $gp->getLp(), "ga" => $gp->getGa(), "gp" => $gp->getGp()]
            );

            if(!$exists){
                $this->getDatabaseCtrl()->query(
                    "INSERT INTO jf_ga_gp_edite (lp, ga, gp) VALUES (:lp, :ga, :gp);",
                    array("lp"=> $gpNew->getLp(), "ga" => $gpNew->getGa(), "gp" => $gpNew->getGp())
                );
            }
            else{
                $this->getDatabaseCtrl()->query(
                    "UPDATE jf_ga_gp_edite SET lp = :lpNew, ga = :gaNew, gp = :gpNew WHERE lp = :lp AND ga = :ga AND gp = :gp;", array(
                    "lp"        => $gp->getLp(),
                    "ga"        => $gp->getGa(),
                    "gp"        => $gp->getGp(),
                    "lpNew"        => $gpNew->getLp(),
                    "gaNew"        => $gpNew->getGa(),
                    "gpNew"        => $gpNew->getGp()
                ));
            }
            */
        }
        
    }


    /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function delete($object)
    {
        $gp = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM ".self::TABLE." WHERE tn = :tn",
            [
                "tn" => $gp->getTn()
            ]
        );

      //  $this->deleteSave($object);
     //   $this->deleteFromPersonlizedLP($object);

    }

        /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function deleteFromPersonlizedLP($object)
    {
        $gp = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM jf_lp_perso_contient_gp WHERE lp = :lp AND ga = :ga AND gp = :gp",
            [
                "lp" => $gp->getLp(),
                "ga" => $gp->getGa(),
                "gp" => $gp->getGp()
            ]
        );
    }

    /**
     * @param $object
     * @throws \Exception
     */
    public function deleteSave($object)
    {
        $gp = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM jf_ga_gp_edite WHERE lp = :lp AND ga = :ga AND gp = :gp",
            [
                "lp" => $gp->getLp(),
                "ga" => $gp->getGa(),
                "gp" => $gp->getGp()
            ]
        );

    }


    /**
     * @param $pk
     * @return mixed|GPstock
     */
    public function find($pk)
    {
        
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE tn = :tn",
            ["tn" => $pk['tn']]
        );
        if(!$ret){
            $result = null;
        }
        else{
            $result = new GPstock($ret['tn'],$ret['lp'], $ret['ga'], $ret['gp'], $ret['designation'], nl2br($ret['texte']));
        }
        return $result;
    }

        /**
     * @param $pk
     * @return mixed|GP
     */
    public function find2($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE lp = :lp AND ga = :ga AND gp = :gp",
            [
                "lp" => $pk['lp'],
                "ga" => $pk['ga'],
                "gp" => $pk['gp']
            ]
        );
        if(!$ret){
            $result = null;
        }
        else{
            $result = new GPstock($ret['tn'],$ret['lp'], $ret['ga'], $ret['gp'], $ret['designation'], htmlspecialchars_decode($ret['texte'],ENT_HTML5));
        }
        return $result;
    }
   

    /**
     * @param $lp
     * @return array
     */
    public function findByLp($lp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE lp = :lp", array(
            "lp" => $lp
        ));
        $result = array();
        foreach( $ret as $v){
            $result[] = new GPstock($v['tn'],$v['lp'], $v['ga'], $v['gp'], $v['designation'], $v['texte']);
        }
        if(!$ret){
            $result = null;
        }
        else{
            $result = new GPstock($ret['tn'],$ret['lp'], $ret['ga'], $ret['gp'], $ret['designation'], htmlspecialchars_decode($ret['texte'],ENT_HTML5));
        }
        return $result;
    }

    public function findProduitconcern($lp, $ga, $gp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM jf_gp WHERE lp = :lp AND ga = :ga AND gp = :gp", ['lp' => $lp, 'ga' => $ga, 'gp' => $gp]);
        $result = array();
        return $result;
    }

    /**
     * @param $lp
     * @param $ga
     * @return array
     */
    public function findByGa($lp, $ga)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE lp = :lp AND ga = :ga", ['lp' => $lp, 'ga' => $ga]);
        $result = array();
        foreach( $ret as $v){
            $result[] = new GPstock($v['tn'],$v['lp'], $v['ga'], $v['gp'], $v['designation'], $v['texte']);
        }
        return $result;
    }

        /**
     * @param $tn
     * @return array
     */
    public function findByTn($tn)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE tn = :tn", array(
            "tn" => $tn
        ));
        $result = array();
        foreach( $ret as $v){
            $result[] = new GPstock($v['tn'],$v['lp'], $v['ga'], $v['gp'], $v['designation'], $v['texte']);
        }
        if(!$ret){
            $result = null;
        }
        else{
            $result = new GPstock($ret['tn'],$ret['lp'], $ret['ga'], $ret['gp'], $ret['designation'], htmlspecialchars_decode($ret['texte'],ENT_HTML5));
        }
        return $result;
    }



    /**
     * @return array|mixed
     */
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE, null);
        $result = array();
        foreach( $ret as $v){
            $result[] = new GPstock($v['tn'],$v['lp'], $v['ga'], $v['gp'], $v['designation'], $v['texte']);
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
                 WHERE B.lp = A.lp AND B.ga = A.ga AND B.gp = A.gp", null);
        $result = array();
        foreach( $ret as $v){
            $result[] = new GPstock($v['tn'],$v['lp'], $v['ga'], $v['gp'], $v['designation'], $v['texte']);
        }
        return $result;
    }


    /**
     * @param $object
     * @return GPstock
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\GPstock {
        if($object instanceof \src\model\metier\GPstock){
            return $object;
        }
        else{
            throw new \Exception("Instance incorrect.");
        }
    }

    /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function addToPersonnalizedLP($object, $lpId)
    {
        $gp = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "INSERT INTO jf_lp_perso_contient_gp (id, lp, ga, gp) VALUES (:id, :lp, :ga, :gp);", array(
            'id'        => $lpId,
            "lp"        => $gp->getLp(),
            "ga"        => $gp->getGa(),
            "gp"        => $gp->getGp()
        ));
    }

    /**
     * @param $searchKey
     * @return mixed
     */
    public function search($searchKey)
    {
        $result = array();
        if(is_string($searchKey)){
            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE LOWER(designation) LIKE :searchKey ", [
                'searchKey' => strtolower('%'.$searchKey.'%')
            ]);
            foreach( $ret as $v){
                $result[] =new GPstock($v['tn'],$v['lp'], $v['ga'], $v['gp'], $v['designation'], $v['texte']);
            }
        }

        if( (is_numeric($searchKey)) && (strlen($searchKey) == 6) ){

            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE CONCAT(FORMAT(lp,'00','en-US'),FORMAT(ga,'00','en-US'),FORMAT(gp,'00','en-US')) = :searchKey",[
                'searchKey'=> $searchKey
            ]);
            foreach( $ret as $v){
                $result[] = new GPstock($v['tn'],$v['lp'], $v['ga'], $v['gp'], $v['designation'], $v['texte']);
            }
        }
        return $result;
    }

    public function findAllTn()
    {
        
        $ret = $this->getDatabaseCtrl()->queryFetchAll(
            "SELECT tn FROM jf_catalogue_stock");

        return $ret;
    } 
/*
    public function codbc($tn)
    {
        $server = "Driver={iSeries Access ODBC Driver};SYSTEM=DEJUMO00;";
        $user = "DVPGLN";
        $pw = "NOTES";
        $queryODBC = "SELECT * FROM jqfrlib.tha0010d ";  // table et bibliotheque données par Ali
        $connexionODBC = odbc_connect($server,$user, $pw);

        $resultODBC = odbc_exec($connexionODBC, $queryODBC) or die('Echec de la requete ODBC');

        $rechTN = "SELECT  * FROM jqfrlib.tha0010d where tn = 00447837 "; 

    $resultTN = odbc_exec($connexionODBC, $rechTN) or die('Echec de la requete ODBC');
    odbc_result_all($resultTN);

    }

*/
}