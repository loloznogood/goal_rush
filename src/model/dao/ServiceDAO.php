<?php

namespace src\model\dao;
use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\Service;

class ServiceDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_service";

    /**
     * @var
     */
    private static $instance;


    /**
     * ServiceDAO constructor.
     * @param DatabaseController $dbCtrl
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return ServiceDAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new ServiceDAO($dbCtrl);
        }
        return self::$instance;
    }

    /**
     * @return mixed
     */
    public function create($object)
    {
        $s = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "INSERT INTO ".self::TABLE." (intitule, description, contenu, image, ordre) VALUES (:intitule, :description, :contenu, :image, :ordre);", array(
            "intitule"      => $s->getIntitule(),
            "description"   => $s->getDescription(),
            "contenu"       => $s->getContenu(),
            "image"         => $s->getImage(),
            "ordre"         => $s->getOrdre()
        ));

    }

    /**
     * @return mixed
     */
    public function update($object)
    {
        $s = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET intitule = :intitule, description = :description, contenu = :contenu, image = :image, ordre = :ordre WHERE id = :id;", array(
            "id"            => $s->getId(),
            "intitule"      => $s->getIntitule(),
            "description"   => $s->getDescription(),
            "contenu"       => $s->getContenu(),
            "image"         => $s->getImage(),
            "ordre"         => $s->getOrdre()
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
        if(!$ret){
            return null;
        }
        else{
            return new Service($ret['id'], $ret['intitule'], $ret['description'], $ret['contenu'], $ret['image'], $ret['ordre']);
        }
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." ORDER BY ordre ASC", null);
        $result = array();
        foreach( $ret as $v){
            $result[]= new Service($v['id'], $v['intitule'], $v['description'], $v['contenu'], $v['image'], $v['ordre']);
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
     * @return \src\model\metier\Service
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\Service {
        if($object instanceof \src\model\metier\Service){
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
            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE LOWER(intitule) LIKE :searchKey OR LOWER(description) LIKE :searchKey", [
                'searchKey' => strtolower('%'.$searchKey.'%')
            ]);
            foreach( $ret as $v){
                $result[] = new Service($v['id'], $v['intitule'], $v['description'], $v['contenu'], $v['image'], $v['ordre']);
            }
        }
        return $result;
    }
}