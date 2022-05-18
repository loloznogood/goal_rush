<?php


namespace src\model\dao;
use \src\model\dao\DAOInterface;
use \src\controller\DatabaseController;
use \src\model\metier\Page;

class PageDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_page";
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
     * @return PageDAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new PageDAO($dbCtrl);
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
            "INSERT INTO ".self::TABLE." (id, titre, contenu) VALUES (:id, :titre, :contenu);", [
            "id"      => $p->getId(),
            "titre"   => $p->getTitre(),
            "contenu"       => $p->getContenu()
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
            "UPDATE ".self::TABLE." SET titre= :titre, contenu = :contenu WHERE id = :id;", [
            "id"      => $p->getId(),
            "titre"   => $p->getTitre(),
            "contenu"       => $p->getContenu()
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
     * @return mixed|Page
     */
    public function find($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE id = :id",
            ['id'=> $pk]
        );
        if($ret){
            return new Page($ret['id'], $ret['titre'], $ret['contenu']);
        }
        else{
            return null;
        }
    }


    /**
     * @return array|mixed
     */
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." ORDER BY id ASC", null);
        $result = array();
        foreach( $ret as $v){
            $result[]= new Page($v['id'], $v['titre'], $v['contenu']);
        }
        return $result;
    }

    /**
     * @param $object
     * @return \src\model\metier\Page
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\Page {
        if($object instanceof \src\model\metier\Page){
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