<?php

namespace src\model\dao;

use \src\model\dao\DAOInterface as DAOInterface;
use \src\controller\DatabaseController as DatabaseController;
use src\model\metier\Bet;

class BetDAO extends AbstractMetierDAO implements DAOInterface{

    public const TABLE = "bets";

    /**
     * @var
     */

     private static $instance;

     /**
      * constructo
      */
      private function __construct(DatabaseController $dbCtrl)
      {
          $this->setDatabaseCtrl($dbCtrl);          
      }

      public static function getInstance(DatabaseController $dbCtrl)
      {
          if(is_null(self::$instance)){
              self::$instance = new BetDAO($dbCtrl);
          }
          return self::$instance;
      }

      /**
     * @param $object
     * @return \src\model\metier\Bet
     * @throws \Exception
     */
    private function getCorectInstance($object) : \src\model\metier\Bet {
        if($object instanceof \src\model\metier\Bet){
            return $object;
        }
        else{
            throw new \Exception("Instance incorrect.");
        }
    }

      /**
     * @param $object
     * @return mixed|void
     * @throws \Exception
     */
    public function create($object)
    {
        $bet = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "INSERT INTO ".self::TABLE."(id, id_ticket, id_party, prono, potentiel, result) VALUES (:lp, :ga, :intitule);", array(
            "id" => $bet->getId(),
            "ticket_id" => $bet->getTicket_id(),
            "party_id" => $bet->getParty_id(),
            "pronostic" => $bet->getProno(),
            "potentielGain" => $bet->getPotentiel(),
            "result" => $bet->getResult()

        ));
    }

    /**
     * @return mixed
     */
    public function update($object)
    {
        $bet = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "UPDATE ".self::TABLE." SET intitule = :intitule WHERE lp = :lp AND ga = :ga ", array(
                "id" => $bet->getId(),
                "ticket_id" => $bet->getTicket_id(),
                "party_id" => $bet->getParty_id(),
                "pronostic" => $bet->getProno(),
                "potentielGain" => $bet->getPotentiel(),
                "result" => $bet->getResult()
        ));
    }

     /**
     * @return mixed
     */
    public function findAll()
    {
        $ret = $this->getDatabaseCtrl()->queryFetchAll("SELECT * FROM ".self::TABLE, null);
        $result = array();
        foreach( $ret as $v){
            $result[] = new Bet($v['id'], $v['ticket_id'], $v['party_id'], $v['pronostic'],$v['potentialGain'], $v['result']);
        }
        return $result;
    }

    public function find($pk)
    {
        $ret = $this->getDatabaseCtrl()->queryFetch(
            "SELECT * FROM ".self::TABLE." WHERE id = :id",
            [
                'id'=> $pk['id'],
            ]
        );
        if(!$ret){
            $result =  null;
        }
        else {
            $result = new Bet($ret['id'], $ret['ticket_id'], $ret['party_id'], $ret['prono'],$ret['potentiel'], $ret['result']);
        }
        return $result;
    }

    public function delete($object)
    {
        $bet = $this->getCorectInstance($object);
        $this->getDatabaseCtrl()->query(
            "DELETE FROM ".self::TABLE." WHERE id = :id",
            ["id" => $bet->getId()]
        );
    }

    public function search($searchKey)
    {
    }
}