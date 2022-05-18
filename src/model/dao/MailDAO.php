<?php


namespace src\model\dao;
use \src\model\dao\DAOInterface;
use \src\controller\DatabaseController;
use \src\model\metier\Mail;

class MailDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jfm_camp";
    /**
     * @var
     */
    private static $instance;


    /**
     * MailDAO constructor.
     * @param DatabaseController $dbCtrl
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return MailDAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if(is_null(self::$instance)){
            self::$instance = new MailDAO($dbCtrl);
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
            "INSERT INTO ".self::TABLE." (nom_camp,comp_camp,nb_camp) VALUES (:nom_camp, :comp_camp, :nb_camp);", [
            "nom_camp"      => $p->getNom_camp(),
            "comp_camp"   => $p->getComp_camp(),
            "nb_camp"       => $p->getNb_camp()
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
            "UPDATE ".self::TABLE." SET nom_camp = :nom_camp, comp_camp= :comp_camp, nb_camp = :nb_camp WHERE id = :id;", [
            "nom_camp"      => $p->getNom_camp(),
            "comp_camp"   => $p->getComp_camp(),
            "nb_camp"       => $p->getNb_camp(),
            "id"    => $p->getId()
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
     * @return mixed|Mail
     */
    public function find($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE id = :id",
            ['id'=> $pk]
        );
        if($ret){
            return new Mail($ret['id'], $ret['nom_camp'], $ret['comp_camp'], $ret['nb_camp']);
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
            $result[]= new Mail($v['id'], $v['nom_camp'], $v['comp_camp'], $v['nb_camp']);
        }
        return $result;
    }

    /**
     * @param $object
     * @return \src\model\metier\Mail
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\Mail {
        if($object instanceof \src\model\metier\Mail){
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