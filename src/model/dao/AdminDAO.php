<?php

namespace src\model\dao;
use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\Admin;

class AdminDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_admin";

    /**
     * @var
     */
    private static $instance;


    /**
     * AdminDAO constructor.
     * @param DatabaseController $dbCtrl
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return AdminDAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new AdminDAO($dbCtrl);
        }
        return self::$instance;
    }

    /**
     * @return mixed
     */
    public function create($object)
    {
        $a = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "INSERT INTO ".self::TABLE." (login, pass, nom, droits) VALUES (:login, :pass, :nom, :droits);", array(
            "login"  => $a->getLogin(),
            "pass"   => $a->getSecurePass(),
            "nom"    => $a->getNom(),
            "droits" => $a->getDroits()
        ));

    }

    /**
     * @return mixed
     */
    public function update($object)
    {
        $a = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET login = :login, pass = :pass, nom = :nom, droits = :droits WHERE id = :id;", array(
            "id"            => $a->getId(),
            "login"         => $a->getLogin(),
            "pass"          => $a->getSecurePass(),
            "nom"           => $a->getNom(),
            "droits"        => $a->getDroits()
        ));
    }

    /**
     * @return mixed
     */
    public function delete($object)
    {
        $s = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM ".self::TABLE." WHERE id = :id ",
            ["id"=> $s->getId()]
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
        $result = new Admin($ret['id'], $ret['login'], $ret['pass'], $ret['nom'], $ret['droits']);
        return $result;
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." ORDER BY id ASC", null);
        $result = array();
        foreach( $ret as $v){
            $result[]= new Admin($v['id'], $v['login'], $v['pass'], $v['nom'], $v['droits']);
        }
        return $result;
    }

    public function connexion($login){
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE login = :login",
            ['login'=> $login]
        );

        if(!$ret){
            $result = $ret;
        }
        else{
            $result = new Admin($ret['id'], $ret['login'], $ret['pass'], $ret['nom'], $ret['droits']);
        }
        return $result;
    }

    /**
     * @param $object
     * @return \src\model\metier\Admin
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\Admin {
        if($object instanceof \src\model\metier\Admin){
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