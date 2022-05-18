<?php

namespace src\controller;
use src\model\DatabaseModel as DatabaseModel;

/**
 * Class DatabaseController
 * @package src\controller
 */
class DatabaseController{


    /**
     * @var
     */
    private static $instance;

    /**
     * @var \src\model\instance
     */
    private $database;

    /**
     * DatabaseController constructor.
     * @param $connectionData
     */
    private function __construct($connectionData){
        try {
            $this->database = DatabaseModel::getInstance($connectionData);
        } catch (Exception $e) {
            echo "Erreur connexion à la base de données : ".$e->getMessage();
        }
    }

    /**
     * @return mixed
     */
    public static function getInstance(){
        return self::$instance;
    }

    /**
     * @param $connectionData
     * @return DatabaseController
     */
    public static function connexion($connectionData){

        if(is_null(self::$instance)){
            self::$instance = new DatabaseController($connectionData);
        }
        return self::$instance;
    }


    /**
     * Realiser une requete sql
     * @return resultat
     */
    public function query($sql, $bind=null){
        try {
            $requete = $this->database->getPdo()->prepare($sql);
            if(is_null($bind)){
                $requete->execute();
            }
            else{
                $requete->execute($bind);
            }

        }catch(\Exception $e){
            //echo 'Erreur SQL: ' . $e->getMessage();
            throw new \Exception("Erreur SQL: ".$e->getMessage());
        }
    }

    public function queryFetch($sql, $bind=null){
        try {
            $requete = $this->database->getPdo()->prepare($sql);
            if(is_null($bind)){
                $requete->execute();
            }
            else{
                $requete->execute($bind);
            }
            $result =  $requete->fetch();
            return $result;
        }catch(\Exception $e){
            echo 'Erreur SQL: ' . $e->getMessage();
        }
    }

    public function queryFetchAll($sql, $bind=null ){
        try {
            $requete = $this->database->getPdo()->prepare($sql);
            if(is_null($bind)){
                $requete->execute();
            }
            else{
                $requete->execute($bind);
            }
            $result =  $requete->fetchAll();
            return $result;
        }catch(\Exception $e){
            echo 'Erreur SQL: ' . $e->getMessage();
        }
    }

    public function queryRowCount($sql, $bind=null){
        try {
            $requete = $this->database->getPdo()->prepare($sql);
            if(is_null($bind)){
                $requete->execute();
            }
            else{
                $requete->execute($bind);
            }
            return $requete->rowCount();
        }catch(\Exception $e){
            echo 'Erreur SQL: ' . $e->getMessage();
        }
    }

}

?>