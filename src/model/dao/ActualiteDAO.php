<?php


namespace src\model\dao;
use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\Actualite;


class ActualiteDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_actualite";

    /**
     * @var
     */
    private static $instance;


    /**
     * ActualiteDAO constructor.
     * @param DatabaseController $dbCtrl
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return ActualiteDAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if (is_null(self::$instance)) {
            self::$instance = new ActualiteDAO($dbCtrl);
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
        $a = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "INSERT INTO " . self::TABLE . " (type, intitule, description, contenu, date_deb, date_fin, lien, image, carousel) VALUES (:type, :intitule, :description, :contenu, :date_deb, :date_fin, :lien, :image, :carousel);", array(
            "type" => $a->getType(),
            "intitule" => $a->getIntitule(),
            "description" => $a->getDescription(),
            "contenu" => $a->getContenu(),
            "date_deb" => $a->getDateDebut(),
            "date_fin" => $a->getDateFin(),
            "lien" => $a->getLien(),
            "image" => $a->getImage(),
            "carousel" => $a->isCarousel()
        ));

    }


    public function update($object)
    {
        $a = $this->getCorectInstance($object);

        $isCarousel = function ( bool $isCarousel){
            if($isCarousel){
                return "1";
            }
            else{
                return "0";
            }
        };

        $this->getDatabaseCtrl()->query(
            "UPDATE " . self::TABLE . " SET type = :type, intitule = :intitule, description = :description, contenu = :contenu, date_deb = :date_deb, date_fin = :date_fin, lien = :lien, image = :image, carousel = :carousel WHERE id = :id;", array(
            "id" => $a->getId(),
            "type" => $a->getType(),
            "intitule" => $a->getIntitule(),
            "description" => $a->getDescription(),
            "contenu" => $a->getContenu(),
            "date_deb" => $a->getDateDebut(),
            "date_fin" => $a->getDateFin(),
            "lien" => $a->getLien(),
            "image" => $a->getImage(),
            "carousel" => $isCarousel($a->isCarousel())
        ));
    }


    public function delete($object)
    {
        $a = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM " . self::TABLE . " WHERE id = :id ",
            ["id" => $a->getId()]
        );
    }

    /**
     * @param $pk : primary key
     * @return mixed
     */
    public function find($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM " . self::TABLE . " WHERE id = :id",
            ['id' => $pk]
        );
        $result = new Actualite($ret['id'], $ret['type'], $ret['intitule'], $ret['description'], $ret['contenu'], $ret['date_deb'], $ret['date_fin'], $ret['lien'], $ret['image'], $ret['carousel']);
        return $result;
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM " . self::TABLE . " ORDER BY date_deb DESC", null);
        $result = array();
        foreach ($ret as $v) {
            $result[] = new Actualite($v['id'], $v['type'], $v['intitule'], $v['description'], $v['contenu'], $v['date_deb'], $v['date_fin'], $v['lien'], $v['image'], $v['carousel']);
        }
        return $result;

    }

    /**
     * @return mixed
     */
    public function findAllForCarousel()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM " . self::TABLE . " WHERE carousel=1 AND ( (date_fin>=CURDATE()) OR (date_fin is NULL)) ORDER BY date_deb DESC", null);
        $result = array();
        foreach ($ret as $v) {
            $result[] = new Actualite($v['id'], $v['type'], $v['intitule'], $v['description'], $v['contenu'], $v['date_deb'], $v['date_fin'], $v['lien'], $v['image'], $v['carousel']);
        }
        return $result;

    }

    /**
     * @param $object
     * @return \src\model\metier\Actualite
     * @throws \Exception
     */
    private function getCorectInstance($object): \src\model\metier\Actualite
    {
        if ($object instanceof \src\model\metier\Actualite) {
            return $object;
        } else {
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
            $ret = $this->getDatabaseCtrl()->queryFetchAll(
                "SELECT * FROM ".self::TABLE." 
                      WHERE LOWER(intitule) LIKE :searchKey 
                      OR LOWER(description) LIKE :searchKey", [
                'searchKey' => strtolower('%'.$searchKey.'%')
            ]);
            foreach( $ret as $v){
                $result[] = new Actualite($v['id'], $v['type'], $v['intitule'], $v['description'], $v['contenu'], $v['date_deb'], $v['date_fin'], $v['lien'], $v['image'], $v['carousel']);
            }
        }
        return $result;
    }
}