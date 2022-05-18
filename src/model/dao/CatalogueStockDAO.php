<?php

namespace src\model\dao;
use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\CatalogueStock;

class CatalogueStockDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Lp de la table MySQL
     */
    public const TABLE = "jf_catalogue_standard";
    public const TABLE2 = "produit";

    /**
     * @var
     */
    private static $instance;


    /**
     * CatalogueStandardDAO constructor.
     * @param DatabaseController $dbCtrl
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return CatalogueStandardDAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new CatalogueStockDAO($dbCtrl);
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
        $c = $this->getCorectInstance($object);

        $is = function ( bool $is){
            if($is){
                return "1";
            }
            else{
                return "0";
            }
        };

        $this->getDatabaseCtrl()->query(
            "INSERT INTO ".self::TABLE." (lp_id, lp, ga, gp, type, intitule, date_maj, fichier_pdf, fichier_magazine, actif_pdf, actif_magazine) VALUES (:lp_id, :lp, :ga, :gp, :type, :intitule, :date_maj, :fichier_pdf, :fichier_magazine, :actif_pdf, :actif_magazine);", array(
            "lp"       => $c->getLp(),
            "ga"   => $c->getGa(),
            "gp"   => $c->getGp(),
            "lp_id"    => $c->getLpId(),
            "type"    => $c->getType(),
            "intitule"    => $c->getIntitule(),
            "date_maj"    => $c->getDateMaj(),
            "fichier_pdf"     => $c->getFichierPdf(),
            "fichier_magazine" => $c->getFichierMag(),
            "actif_pdf"     => $is($c->isActifPdf()),
            "actif_magazine" => $is($c->isActifMag())
        ));

    }


    /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function update($object)
    {
        $c = $this->getCorectInstance($object);

        $is = function ( bool $is){
            if($is){
                return "1";
            }
            else{
                return "0";
            }
        };

        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET lp_id = :lp_id, lp = :lp, ga = :ga, gp = :gp, type = :type, intitule = :intitule, date_maj = :date_maj, fichier_pdf = :fichier_pdf, fichier_magazine = :fichier_magazine , actif_pdf = :actif_pdf, actif_magazine = :actif_magazine WHERE id = :id;", array(
            "id"       => $c->getId(),
            "lp"       => $c->getLp(),
            "ga"   => $c->getGa(),
            "gp"   => $c->getGp(),
            "lp_id"    => $c->getLpId(),
            "type"    => $c->getType(),
            "intitule"    => $c->getIntitule(),
            "date_maj"    => $c->getDateMaj(),
            "fichier_pdf"     => $c->getFichierPdf(),
            "fichier_magazine" => $c->getFichierMag(),
            "actif_pdf"     => $is($c->isActifPdf()),
            "actif_magazine" => $is($c->isActifMag())
        ));
    }

    /**
     * @return mixed
     */
    public function delete($object)
    {
        $c = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM ".self::TABLE." WHERE id = :id ",
            ["id"=> $c->getId()]
        );
    }

    /**
     * @param $pk : primary key
     * @return mixed
     */
    public function find($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE id = :id",
            ['id'=> $pk]
        );
        if(!$ret){
            $result = $ret;
        }
        else{
            $result = new CatalogueStock($ret['id'],$ret['lp'],$ret['designation'], $ret['date_ajout'],$ret['texte']);
        }
        return $result;
    }

    /**
     * @param $pk : primary key
     * @return mixed
     */
    public function findCatalogueStockEntier()
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE2.""
        );
        if(!$ret){
            $result = $ret;
        }
        else{
            $result = new CatalogueStock($ret['tn'],$ret['lp'],$ret['designation'], $ret['date_ajout'],$ret['texte']);
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
            $result[]= new CatalogueStock($v['id'], $v['lp_id'], $v['lp'], $v['ga'], $v['gp'], $v['type'], $v['intitule'],$v['date_maj'], $v['fichier_pdf'], $v['fichier_magazine'], $v['actif_pdf'], $v['actif_magazine']);
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function findAllByType($type)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE type = :type", ['type' => $type]);
        $result = array();
        foreach( $ret as $v){
            $result[]= new CatalogueStock($v['id'], $v['lp_id'], $v['lp'], $v['ga'], $v['gp'], $v['type'], $v['intitule'],$v['date_maj'], $v['fichier_pdf'], $v['fichier_magazine'], $v['actif_pdf'], $v['actif_magazine']);

        }
        return $result;
    }

    /**
     * Fonction retournant le catalogue standard d'une un groupe produit
     * identifie par un numero de lp, ga , gp.
     * @param Integer $lp numero de ligne de produit
     * @param Integer $ga numero de groupe article
     * @param Integer $gp numero de groupe produit
     * @return CatalogueStandard|null
     */
    public function findByGp($lp, $ga, $gp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch("SELECT * FROM ".self::TABLE." WHERE lp = :lp  AND ga = :ga AND gp = :gp ORDER BY date_maj DESC LIMIT 1",
            [
                'lp' => $lp,
                'ga' => $ga,
                'gp'=> $gp
            ]);
        if(!$ret){
            $result = null;
        }
        else{
            $result = new CatalogueStock($ret['id'],$ret['lp'],$ret['designation'], $ret['date_ajout'],$ret['texte']);
        }
        return $result;
    }
    /**
     * Fonction retournant le catalogue standard d'une une ligne de produit
     * identifie par un numero de lp.
     * @param Integer $lp numero de ligne de produit
     * @return CatalogueStandard|null
     */
    public function findByLp($lp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch("SELECT * FROM ".self::TABLE2." WHERE lp = :lp ORDER BY date_ajout DESC LIMIT 1", ['lp' => $lp]);
        if(!$ret){
            $result = $ret;
        }
        else{
            $result = new CatalogueStock($ret['tn'], $ret['lp'],$ret['designation'], $ret['date_ajout'], $ret['texte']);
        }
        return $result;
    }
    /**
     * Fonction retournant le catalogue standard d'une une ligne de produit
     * identifie par un identifiant de lp.
     * @param Integer $lpId identifiant de ligne de produit
     * @return CatalogueStandard|null
     */


    /**
     * Fonction retournant tous les catalogues standards appartenent a une ligne de produit
     * identifie par un numero de lp.
     * @param Integer $lp numero de ligne de produit
     * @return array
     */
    public function findAllWithLp($lp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll(
            "
                    SELECT cs.* FROM produit cs 
                    INNER JOIN( 
                        SELECT lp 
                        FROM produit 
                        GROUP BY lp
                    ) cs2 ON cs.lp = cs2.lp AND cs.lp = :lp
                ", ['lp' => $lp]);
        $result = array();
        foreach( $ret as $v){
            $result[]= new CatalogueStock($v['tn'], $v['lp'],$v['designation'],$v['date_ajout'], $v['texte']);
        }
        return $result;
    }
    /**
     * Fonction retournant tous les catalogues standards appartenent a une ligne de produit
     * identifie par un identifiant de lp.
     * @param Integer $lpId identifiant de ligne de produit
     * @return array
     */
    public function findAllWithLpId($lpId)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll(
            "
                    SELECT cs.* 
                    FROM jf_lp_perso_contient_gp lpp, jf_catalogue_standard cs
                    INNER JOIN (
                        SELECT lp, ga, gp, MAX(date_maj) as maxDate 
                        FROM jf_catalogue_standard
                        WHERE type = 'parGP' 
                        GROUP BY lp, ga, gp
                    ) cs2 ON cs2.lp = cs.lp AND cs2.ga = cs2.ga AND cs2.gp = cs.gp AND cs2.maxDate = cs.date_maj
                    WHERE cs.lp = lpp.lp AND cs.ga = lpp.ga AND cs.gp = lpp.gp AND lpp.id = :lpId
                ", ['lpId' => $lpId]);
        $result = array();
        foreach( $ret as $v){
            $result[]= new CatalogueStock($v['id'], $v['lp_id'], $v['lp'], $v['ga'], $v['gp'], $v['type'], $v['intitule'],$v['date_maj'], $v['fichier_pdf'], $v['fichier_magazine'], $v['actif_pdf'], $v['actif_magazine']);
        }
        return $result;
    }

    /**
     * @param $object
     * @return \src\model\metier\CatalogueStandard
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\CatalogueStandard {
        if($object instanceof \src\model\metier\CatalogueStandard){
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
            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE LOWER(intitule) LIKE :searchKey AND type = 'parGP'", [
                'searchKey' => strtolower('%'.$searchKey.'%')
            ]);
            foreach( $ret as $v){
                $result[] =new CatalogueStock($v['id'], $v['lp_id'], $v['lp'], $v['ga'], $v['gp'], $v['type'], $v['intitule'],$v['date_maj'], $v['fichier_pdf'], $v['fichier_magazine'], $v['actif_pdf'], $v['actif_magazine']);
            }
        }
        if(is_string($searchKey)){
            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." cs , jf_lp lp WHERE LOWER(lp.intitule) LIKE :searchKey AND lp.id = cs.lp_id AND cs.type = 'parLP'", [
                'searchKey' => strtolower('%'.$searchKey.'%')
            ]);
            foreach( $ret as $v){
               $result[] =new CatalogueStock($v['id'], $v['lp_id'], $v['lp'], $v['ga'], $v['gp'], $v['type'], $v['intitule'],$v['date_maj'], $v['fichier_pdf'], $v['fichier_magazine'], $v['actif_pdf'], $v['actif_magazine']);
            }
        }

        if( (is_numeric($searchKey)) && (strlen($searchKey) == 6) ){

            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE CONCAT(FORMAT(lp,'00','en-US'),FORMAT(ga,'00','en-US'),FORMAT(gp,'00','en-US')) = :searchKey",[
                'searchKey'=> $searchKey
            ]);
            foreach( $ret as $v){
                $result[] = new CatalogueStock($v['id'], $v['lp_id'], $v['lp'], $v['ga'], $v['gp'], $v['type'], $v['intitule'],$v['date_maj'], $v['fichier_pdf'], $v['fichier_magazine'], $v['actif_pdf'], $v['actif_magazine']);
            }
        }

        if( (is_numeric($searchKey)) && (strlen($searchKey) == 4) ){

            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE CONCAT(FORMAT(lp,'00','en-US'),FORMAT(ga,'00','en-US')) = :searchKey",[
                'searchKey'=> $searchKey
            ]);
            foreach( $ret as $v){
                $result[] = new CatalogueStock($v['id'], $v['lp_id'], $v['lp'], $v['ga'], $v['gp'], $v['type'], $v['intitule'],$v['date_maj'], $v['fichier_pdf'], $v['fichier_magazine'], $v['actif_pdf'], $v['actif_magazine']);
            }
        }
        if( (is_numeric($searchKey)) && (strlen($searchKey) == 2) ){

            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE CONCAT(FORMAT(lp,'00','en-US')) = :searchKey",[
                'searchKey'=> $searchKey
            ]);
            foreach( $ret as $v){
                $result[] = new CatalogueStock($v['id'], $v['lp_id'], $v['lp'], $v['ga'], $v['gp'], $v['type'], $v['intitule'],$v['date_maj'], $v['fichier_pdf'], $v['fichier_magazine'], $v['actif_pdf'], $v['actif_magazine']);
            }
        }

        return $result;
    }
}