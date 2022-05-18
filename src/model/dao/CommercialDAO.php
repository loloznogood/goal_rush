<?php

namespace src\model\dao;
use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\Commercial;

class CommercialDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_commercial";

    /**
     * @var
     */
    private static $instance;


    /**
     * CommercialDAO constructor.
     * @param DatabaseController $dbCtrl
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return CommercialDAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new CommercialDAO($dbCtrl);
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


        $this->getDatabaseCtrl()->query(
            "INSERT INTO ".self::TABLE." (nom, prenom, num_repr, fonction, mail, tel, fax, image) VALUES (:nom, :prenom, :num_repr, :fonction, :mail, :tel, :fax, :image);", array(
            "nom"       => $c->getNom(),
            "prenom"   => $c->getPrenom(),
            "num_repr"    => $c->getNumRepr(),
            "fonction"    => $c->getFonction(),
            "mail"    => $c->getMail(),
            "tel"    => $c->getTel(),
            "fax"     => $c->getFax(),
            "image" => $c->getImage()
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

        $numRepr = NULL;
        if(!empty($c->getNumRepr())){
            $numRepr = $c->getNumRepr();
        }

        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET nom = :nom, prenom = :prenom, num_repr = :num_repr, fonction = :fonction, mail = :mail, tel = :tel, fax = :fax, image = :image WHERE id = :id;", array(
            "id"       => $c->getId(),
            "nom"       => $c->getNom(),
            "prenom"   => $c->getPrenom(),
            "num_repr"    => $numRepr,
            "fonction"    => $c->getFonction(),
            "mail"    => $c->getMail(),
            "tel"    => $c->getTel(),
            "fax"     => $c->getFax(),
            "image" => $c->getImage()
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
            $result = new Commercial($ret['id'], $ret['nom'], $ret['prenom'], $ret['num_repr'], $ret['fonction'], $ret['mail'], $ret['tel'], $ret['fax'], $ret['image']);
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
            $result[]= new Commercial($v['id'], $v['nom'], $v['prenom'], $v['num_repr'], $v['fonction'], $v['mail'],$v['tel'], $v['fax'], $v['image']);
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function findAllByFonction($fonction)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE fonction = :fonction", ['fonction' => $fonction]);
        $result = array();
        foreach( $ret as $v){
            $result[]= new Commercial($v['id'], $v['nom'], $v['prenom'], $v['num_repr'], $v['fonction'], $v['mail'],$v['tel'], $v['fax'], $v['image']);
        }
        return $result;
    }

    /**
     * @param $object
     * @return \src\model\metier\Commercial
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\Commercial {
        if($object instanceof \src\model\metier\Commercial){
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