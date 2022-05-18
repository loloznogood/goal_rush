<?php

namespace src\model\dao;
use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\Partie;
use src\model\metier\Service;

class PartieDAO extends AbstractMetierDAO implements DAOInterface
{
    /**
     * Nom de la table MySQL
     */
    public const TABLE = "parties";

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
            self::$instance = new PartieDao($dbCtrl);
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
            "INSERT INTO ".self::TABLE." (home_team, away_team, score_home_team, score_away_team, home_team_rating, away_team_rating, draft_rating, result, date) VALUES (:home_team, :away_team, :score_home_team, :score_away_team, :home_team_rating, :away_team_rating, :draft_rating, :result, :date);", array(
            "home_team"      => $s->getHomeTeam(),
            "away_team"   => $s->getAwayTeam(),
            "score_home_team"       => $s->getScoreHomeTeam(),
            "score_away_team"         => $s->getScoreAwayTeam(),
            "home_team_rating"         => $s->getHomeTeamRating(),
            "away_team_rating"         => $s->getAwayTeamRating(),
            "draft_rating"         => $s->getDraftRating(),
            "result"         => $s->getResult(),
            "date"         => $s->getDate()
        ));

    }

    /**
     * @return mixed
     */
    public function update($object)
    {
        $s = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET home_team = :home_team, away_team = :away_team, score_home_team = :score_home_team, score_away_team = :score_away_team, home_team_rating = :home_team_rating, away_team_rating = :away_team_rating, draft_rating = :draft_rating, result = :result, date = :date  WHERE id = :id;", array(
            "id"            => $s->getId(),
            "home_team"      => $s->getHomeTeam(),
            "away_team"   => $s->getAwayTeam(),
            "score_home_team"       => $s->getScoreHomeTeam(),
            "score_away_team"         => $s->getScoreAwayTeam(),
            "home_team_rating"         => $s->getHomeTeamRating(),
            "away_team_rating"         => $s->getAwayTeamRating(),
            "draft_rating"         => $s->getDraftRating(),
            "result"         => $s->getResult(),
            "date"         => $s->getDate()
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
            return new Partie($ret['id'], $ret['home_team'], $ret['away_team'], $ret['score_home_team'], $ret['score_away_team'], $ret['home_team_rating'], $ret['away_team_rating'], $ret['draft_rating'], $ret['result'], $ret['date']);
        }
    }

    /**
     * @return mixed
     */
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." ORDER BY id ASC", null);
        $result = array();
        foreach( $ret as $v){
            $result[]= new Partie($v['id'], $v['home_team'], $v['away_team'], $v['score_home_team'], $v['score_away_team'], $v['home_team_rating'], $v['away_team_rating'], $v['draft_rating'], $v['result'], $v['date']);
        }
        return $result;
    }

    /**
     * @param $object
     * @return \src\model\metier\Service
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\Partie {
        if($object instanceof \src\model\metier\Partie){
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
            $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE." WHERE LOWER(home_team) LIKE :searchKey OR LOWER(home_team) LIKE :searchKey", [
                'searchKey' => strtolower('%'.$searchKey.'%')
            ]);
            foreach( $ret as $v){
                $result[]= new Partie($v['id'], $v['home_team'], $v['away_team'], $v['score_home_team'], $v['score_away_team'], $v['home_team_rating'], $v['away_team_rating'], $v['draft_rating'], $v['result'], $v['date']);
            }
        }
        return $result;
    }
}