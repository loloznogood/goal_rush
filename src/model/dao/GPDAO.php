<?php

namespace src\model\dao;

use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\GP;

class GPDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "jf_gp";


    /**
     * @var
     */
    private static $instance;


    /**
     * GPDAO constructor.
     * @param DatabaseController $dbCtrl
     */
    private function __construct(DatabaseController $dbCtrl)
    {
        $this->setDatabaseCtrl($dbCtrl);
    }


    /**
     * @param DatabaseController $dbCtrl
     * @return GPDAO
     */
    public static function getInstance(DatabaseController $dbCtrl)
    {
        if (is_null(self::$instance)) {
            self::$instance = new GPDAO($dbCtrl);
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
            "INSERT INTO " . self::TABLE . " (lp, ga, gp, intitule, description1, description2, namescarac, carac, typelabel, jumofr) VALUES (:lp, :ga, :gp, :intitule, :description1, :description2, :namescarac, :carac, :typelabel, :jumofr);",
            array(
                "lp"        => $gp->getLp(),
                "ga"        => $gp->getGa(),
                "gp"        => $gp->getGp(),
                "intitule"  => $gp->getIntitule(),
                "description1"  => $gp->getDescription1(),
                "description2"  => $gp->getDescription2(),
                "namescarac" => $gp->getNamescarac(),
                "carac" => $gp->getCarac(),
                "typelabel" => $gp->getTypelabel(),
                "jumofr" => $gp->getAutomatique()
            )
        );

        $this->getDatabaseCtrl()->query(
            "INSERT INTO jf_ga_gp_edite (lp, ga, gp) VALUES (:lp, :ga, :gp);",
            array(
                "lp"        => $gp->getLp(),
                "ga"        => $gp->getGa(),
                "gp"        => $gp->getGp(),
            )
        );
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
            "UPDATE " . self::TABLE . " SET intitule = :intitule , description1 = :description1, description2 = :description2, namescarac = :namescarac, carac = :carac, typelabel = :typelabel, jumofr = :jumofr WHERE lp = :lp AND ga = :ga AND gp = :gp;",
            array(
                "lp"        => $gp->getLp(),
                "ga"        => $gp->getGa(),
                "gp"        => $gp->getGp(),
                "intitule"  => $gp->getIntitule(),
                "description1"  => $gp->getDescription1(),
                "description2"  => $gp->getDescription2(),
                "namescarac" => $gp->getNamescarac(),
                "carac" => $gp->getCarac(),
                "typelabel" => $gp->getTypelabel(),
                "jumofr" => $gp->getAutomatique()
            )
        );

        $exists = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM jf_ga_gp_edite WHERE lp = :lp AND ga = :ga AND gp = :gp",
            ["lp" => $gp->getLp(), "ga" => $gp->getGa(), "gp" => $gp->getGp()]
        );

        if (!$exists) {
            $this->getDatabaseCtrl()->query(
                "INSERT INTO jf_ga_gp_edite (lp, ga, gp) VALUES (:lp, :ga, :gp);",
                array("lp" => $gp->getLp(), "ga" => $gp->getGa(), "gp" => $gp->getGp())
            );
        }
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function updateTotal($object, $newObject = null)
    {
        if (is_null($newObject)) {
            $this->update($object);
        } else {
            $gp = $this->getCorectInstance($object);
            $gpNew = $this->getCorectInstance($newObject);

            $this->getDatabaseCtrl()->query(
                "UPDATE " . self::TABLE . " SET lp = :lpNew, ga = :gaNew, gp = :gpNew, intitule = :intitule , description1 = :description1, description2 = :description2, namescarac = :namescarac, carac = :carac, typelabel = :typelabel, jumofr = :jumofr WHERE lp = :lp AND ga = :ga AND gp = :gp;",
                array(
                    "lp"        => $gp->getLp(),
                    "ga"        => $gp->getGa(),
                    "gp"        => $gp->getGp(),
                    "intitule"  => $gp->getIntitule(),
                    "description1"  => $gp->getDescription1(),
                    "description2"  => $gp->getDescription2(),
                    "namescarac" => $gp->getNamescarac(),
                    "carac" => $gp->getCarac(),
                    "typelabel" => $gp->getTypelabel(),
                    "jumofr" => $gp->getAutomatique(),
                    "lpNew"        => $gpNew->getLp(),
                    "gaNew"        => $gpNew->getGa(),
                    "gpNew"        => $gpNew->getGp()
                )
            );

            $exists = $this->getDatabaseCtrl()->queryFetch(
                "SELECT * FROM jf_ga_gp_edite WHERE lp = :lp AND ga = :ga AND gp = :gp",
                ["lp" => $gp->getLp(), "ga" => $gp->getGa(), "gp" => $gp->getGp()]
            );

            if (!$exists) {
                $this->getDatabaseCtrl()->query(
                    "INSERT INTO jf_ga_gp_edite (lp, ga, gp) VALUES (:lp, :ga, :gp);",
                    array("lp" => $gpNew->getLp(), "ga" => $gpNew->getGa(), "gp" => $gpNew->getGp())
                );
            } else {
                $this->getDatabaseCtrl()->query(
                    "UPDATE jf_ga_gp_edite SET lp = :lpNew, ga = :gaNew, gp = :gpNew WHERE lp = :lp AND ga = :ga AND gp = :gp;",
                    array(
                        "lp"        => $gp->getLp(),
                        "ga"        => $gp->getGa(),
                        "gp"        => $gp->getGp(),
                        "lpNew"        => $gpNew->getLp(),
                        "gaNew"        => $gpNew->getGa(),
                        "gpNew"        => $gpNew->getGp()
                    )
                );
            }
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
            "DELETE FROM " . self::TABLE . " WHERE lp = :lp AND ga = :ga AND gp = :gp",
            [
                "lp" => $gp->getLp(),
                "ga" => $gp->getGa(),
                "gp" => $gp->getGp()
            ]
        );

        $this->deleteSave($object);
        $this->deleteFromPersonlizedLP($object);
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
     * @return mixed|GP
     */
    public function find($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM " . self::TABLE . " WHERE lp = :lp AND ga = :ga AND gp = :gp",
            [
                "lp" => $pk['lp'],
                "ga" => $pk['ga'],
                "gp" => $pk['gp']
            ]
        );
        $namecarac = array();
        $carac = array();
        $namecarac = explode("*", htmlspecialchars_decode($ret['namescarac']));
        $carac = explode("*", htmlspecialchars_decode($ret['carac']));
        if (!$ret) {
            $result = null;
        } else {
            $result = new GP($ret['lp'], $ret['ga'], $ret['gp'], $ret['intitule'], $ret['description1'], htmlspecialchars_decode($ret['description2'], ENT_HTML5), $namecarac, $carac, htmlspecialchars_decode($ret['typelabel']), $ret['jumofr']);
        }
        return $result;
    }


      /**
     * @param $pk
     * @return mixed|GP
     */
    public function findNoArray($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM " . self::TABLE . " WHERE lp = :lp AND ga = :ga AND gp = :gp",
            [
                "lp" => $pk['lp'],
                "ga" => $pk['ga'],
                "gp" => $pk['gp']
            ]
        );
        if (!$ret) {
            $result = null;
        } else {
            $result = new GP($ret['lp'], $ret['ga'], $ret['gp'], $ret['intitule'], htmlspecialchars_decode($ret['description1'], ENT_HTML5),  htmlspecialchars_decode($ret['description2'], ENT_HTML5), htmlspecialchars_decode($ret['namescarac'], ENT_HTML5), htmlspecialchars_decode($ret['carac'], ENT_HTML5), htmlspecialchars_decode($ret['typelabel'], ENT_HTML5),$ret['jumofr']);
        }
        return $result;
    }

    /**
     * @param $lp
     * @return array
     */
    public function findByLp($lp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM " . self::TABLE . " WHERE lp = :lp", array(
            "lp" => $lp
        ));
        $result = array();
        foreach ($ret as $v) {
            $result[] = new GP($v['lp'], $v['ga'], $v['gp'], $v['intitule'], $v['description1'], $v['description2'], $v['namescarac'], $v['carac'], $v['typelabel'],$v['jumofr']);
        }
        if (!$ret) {
            $result = null;
        } else {
            $result = new GP($ret['lp'], $ret['ga'], $ret['gp'], $ret['intitule'], htmlspecialchars_decode($ret['description1'], ENT_HTML5),  htmlspecialchars_decode($ret['description2'], ENT_HTML5), htmlspecialchars_decode($ret['namescarac'], ENT_HTML5), htmlspecialchars_decode($ret['carac'], ENT_HTML5), htmlspecialchars_decode($ret['typelabel'], ENT_HTML5),$ret['jumofr']);
        }
        return $result;
    }


    /**
     * @param $lp
     * @param $ga
     * @return array
     */
    public function findByGa($lp, $ga)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM " . self::TABLE . " WHERE lp = :lp AND ga = :ga", ['lp' => $lp, 'ga' => $ga]);
        $result = array();
        foreach ($ret as $v) {
            $result[] = new GP($v['lp'], $v['ga'], $v['gp'], $v['intitule'], $v['description1'], $v['description2'], $v['namescarac'], $v['carac'], $v['typelabel'],$v['jumofr']);
        }
        return $result;
    }

    public function findByLpEdit($id, $ga){
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM " . self::TABLE . ", jf_lp_perso_contient_gp" . " WHERE id = :id AND  jf_lp_perso_contient_gp.ga = :ga 
        AND jf_gp.lp = jf_lp_perso_contient_gp.lp AND jf_gp.ga = jf_lp_perso_contient_gp.ga AND jf_gp.gp = jf_lp_perso_contient_gp.gp", array(
            "id" => $id,
            "ga" => $ga
        ));
        $result = array();
        foreach ($ret as $v) {
            $result[] = new GP($v['lp'], $v['ga'], $v['gp'], $v['intitule'], $v['description1'], $v['description2'], $v['namescarac'], $v['carac'], $v['typelabel'],$v['jumofr']);
        }
        return $result;
    }

    public function findByTn($tn)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM " . self::TABLE . " WHERE tn = :tn ", ['tn' => $tn]);
        $result = array();
        foreach ($ret as $v) {
            $result[] = new GP($v['lp'], $v['ga'], $v['gp'], $v['intitule'], $v['description1'], $v['description2'], $v['namescarac'], $v['carac'], $v['typelabel'],$v['jumofr']);
        }
        return $result;
    }


    /**
     * @return array|mixed
     */
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM " . self::TABLE, null);
        $result = array();
        foreach ($ret as $v) {
            $result[] = new GP($v['lp'], $v['ga'], $v['gp'], $v['intitule'], $v['description1'], $v['description2'], $v['namescarac'], $v['carac'], $v['typelabel'],$v['jumofr']);
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function findAllEdited()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll(
            "SELECT * FROM " . self::TABLE . " as A, jf_ga_gp_edite as B
                 WHERE B.lp = A.lp AND B.ga = A.ga AND B.gp = A.gp",
            null
        );
        $result = array();
        foreach ($ret as $v) {
            $result[] = new GP($v['lp'], $v['ga'], $v['gp'], $v['intitule'], $v['description1'], $v['description2'], $v['namescarac'], $v['carac'], $v['typelabel'],$v['jumofr']);
        }
        return $result;
    }


    /**
     * @param $object
     * @return GP
     * @throws \Exception
     */
    private function getCorectInstance($object): \src\model\metier\GP
    {
        if ($object instanceof \src\model\metier\GP) {
            return $object;
        } else {
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
            "INSERT INTO jf_lp_perso_contient_gp (id, lp, ga, gp) VALUES (:id, :lp, :ga, :gp);",
            array(
                'id'        => $lpId,
                "lp"        => $gp->getLp(),
                "ga"        => $gp->getGa(),
                "gp"        => $gp->getGp()
            )
        );
    }

    /**
     * @param $searchKey
     * @return mixed
     */
    public function search($searchKey)
    {
        $result = array();
        if (is_string($searchKey)) {
            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM " . self::TABLE . " WHERE LOWER(intitule) LIKE :searchKey ", [
                'searchKey' => strtolower('%' . $searchKey . '%')
            ]);
            foreach ($ret as $v) {
                $result[] = new GP($v['lp'], $v['ga'], $v['gp'], $v['intitule'], $v['description1'], $v['description2'], $v['namescarac'], $v['carac'], $v['typelabel'],$v['jumofr']);
            }
        }

        if ((is_numeric($searchKey)) && (strlen($searchKey) == 6)) {

            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM " . self::TABLE . " WHERE CONCAT(FORMAT(lp,'00','en-US'),FORMAT(ga,'00','en-US'),FORMAT(gp,'00','en-US')) = :searchKey", [
                'searchKey' => $searchKey
            ]);
            foreach ($ret as $v) {
                $result[] = new GP($v['lp'], $v['ga'], $v['gp'], $v['intitule'], $v['description1'], $v['description2'], $v['namescarac'], $v['carac'], $v['typelabel'],$v['jumofr']);
            }
        }
        return $result;
    }

    public function findProduitconcern($lp, $ga, $gp)
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT intitule FROM `jf_catalogue_stock` p,jf_gp j WHERE lp = :lp AND ga = :ga AND gp = :gp AND p.lp=j.lp AND p.ga=j.ga AND p.gp=j.gp ", ['lp' => $lp, 'ga' => $ga, 'gp' => $gp]);
        $result = array();
        foreach ($ret as $v) {
            $result[] = new GP($v['lp'], $v['ga'], $v['gp'], $v['intitule'], $v['description1'], $v['description2'], $v['namescarac'], $v['carac'], $v['typelabel'],$v['jumofr']);
        }
        return $result;
    }
}
