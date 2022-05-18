<?php

namespace src\model\dao;
use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\Departement;

class DepartementDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_departement";

    /**
     * @var
     */
    private static $instance;


    /**
     * DepartementDAO constructor.
     * @param DatabaseController $dbCtrl
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return DepartementDAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new DepartementDAO($dbCtrl);
        }
        return self::$instance;
    }

    /**
     * @return mixed
     */
    public function create($object)
    {
        $d = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "INSERT INTO ".self::TABLE." (num, nom, secteur, id_dir, id_tcs, id_itc, id_ac) VALUES (:num, :nom, :secteur, :id_dir, :id_tcs, :id_itc, id_ac);", array(
            "num"       => $d->getNum(),
            "nom"       => $d->getNom(),
            "secteur"   => $d->getSecteur(),
            "id_dir"    => $d->getIdDir(),
            "id_tcs"    => $d->getIdTcs(),
            "id_itc"    => $d->getIdItc(),
            "id_ac"     => $d->getIdAc()
        ));

    }

    /**
     * @return mixed
     */
    public function update($object)
    {
        $d = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET nom = :nom, secteur = :secteur, id_dir = :id_dir, id_tcs = :id_tcs, id_itc = :id_itc, id_ac = :id_ac WHERE num = :num;", array(
            "num"       => $d->getNum(),
            "nom"       => $d->getNom(),
            "secteur"   => $d->getSecteur(),
            "id_dir"    => $d->getIdDir(),
            "id_tcs"    => $d->getIdTcs(),
            "id_itc"    => $d->getIdItc(),
            "id_ac"     => $d->getIdAc()
        ));
    }

    /**
     * @return mixed
     */
    public function delete($object)
    {
        $d = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM ".self::TABLE." WHERE num = :num ",
            ["num"=> $d->getNum()]
        );
    }

    /**
     * @param $pk : primary key
     * @return mixed
     */
    public function find($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE num = :num",
            ['num'=> $pk]
        );
        if(!$ret){
            $result = $ret;
        }
        else{
            $result = new Departement($ret['num'], $ret['nom'], $ret['secteur'], $ret['id_dir'], $ret['id_tcs'], $ret['id_itc'], $ret['id_ac']);
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
            $result[]= new  Departement($v['num'], $v['nom'], $v['secteur'], $v['id_dir'], $v['id_tcs'], $v['id_itc'], $v['id_ac']);
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function findAllByDir($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE id_dir = :id", ['id' => $pk]);
        $result = array();
        foreach( $ret as $v){
            $result[]= new  Departement($v['num'], $v['nom'], $v['secteur'], $v['id_dir'], $v['id_tcs'], $v['id_itc'], $v['id_ac']);
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function findAllByTcs($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE id_tcs = :id", ['id' => $pk]);
        $result = array();
        foreach( $ret as $v){
            $result[]= new  Departement($v['num'], $v['nom'], $v['secteur'], $v['id_dir'], $v['id_tcs'], $v['id_itc'], $v['id_ac']);
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function findAllByItc($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE id_itc = :id", ['id' => $pk]);
        $result = array();
        foreach( $ret as $v){
            $result[]= new  Departement($v['num'], $v['nom'], $v['secteur'], $v['id_dir'], $v['id_tcs'], $v['id_itc'], $v['id_ac']);
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function findAllByAc($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE id_ac = :id", ['id' => $pk]);
        $result = array();
        foreach( $ret as $v){
            $result[]= new  Departement($v['num'], $v['nom'], $v['secteur'], $v['id_dir'], $v['id_tcs'], $v['id_itc'], $v['id_ac']);
        }
        return $result;
    }


    /**
     * @param $object
     * @return \src\model\metier\Departement
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\Departement {
        if($object instanceof \src\model\metier\Departement){
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
        // TODO: Implement search() method.
    }
}