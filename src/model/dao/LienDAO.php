<?php


namespace src\model\dao;
use \src\model\dao\DAOInterface;
use \src\controller\DatabaseController;
use \src\model\metier\Lien;

class LienDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_liens_produits";
    /**
     * @var
     */
    private static $instance;


    /**
     * PageDAO constructor.
     * @param DatabaseController $dbCtrl
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return LienDAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new LienDAO($dbCtrl);
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
        $p = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "INSERT INTO ".self::TABLE." (id, titre,typeLien, url, idCategorie) VALUES (:id, :titre,:type, :url, :idCategorie);", [
            "id"      => $p->getId(),
            "titre"   => $p->getTitre(),
            "url"       => $p->getUrl(),
            "type"          => $p ->getType(),
            "idCategorie"          => $p ->getIdCategorie()

        ]);

    }


    /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function update($object)
    {
        $p = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET titre= :titre, url = :url , typeLien = :type, idCategorie=:idCategorie WHERE id = :id;", [
                "id"      => $p->getId(),
                "titre"   => $p->getTitre(),
                "url"       => $p->getUrl(),
                "type"          => $p ->getType(),
            "idCategorie"          => $p ->getIdCategorie()

        ]);
    }


    /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function delete($object)
    {
        $p = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM ".self::TABLE." WHERE id = :id ",
            ["id"=> $p->getId()]
        );
    }


    /**
     * @param $pk
     * @return mixed|Lien
     */
    public function find($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE id = :id",
            ['id'=> $pk]
        );
        if($ret){
            return new Lien($ret['id'],$ret['typeLien'], $ret['titre'], $ret['url'],$ret['idCategorie'] );
        }
        else{
            return null;
        }
    }


    /**
     * @return array|mixed
     */
    public function findAllCateg()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM jf_liens_produits WHERE typeLien = 'Categorie'");
        $result = array();
        foreach( $ret as $v){
            $result[]= new Lien($v['id'],$v['typeLien'], $v['titre'], $v['url'], $v['idCategorie']);
        }
        return $result;
    }
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." ORDER BY id ASC", null);
        $result = array();
        foreach( $ret as $v){
            $result[]= new Lien($v['id'],$v['typeLien'], $v['titre'], $v['url'], $v['idCategorie']);
        }
        return $result;
    }


       /**
     * @return array|mixed
     */
    public function findAllSousCateg()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM jf_liens_produits WHERE typeLien = 'Sous-categorie'");
        $result = array();
        foreach( $ret as $v){
            $result[]= new Lien($v['id'],$v['typeLien'], $v['titre'], $v['url'], $v['idCategorie']);
        }
        return $result;
    }
   

    /**
     * @param $object
     * @return \src\model\metier\Lien
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\Lien {
        if($object instanceof \src\model\metier\Lien){
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