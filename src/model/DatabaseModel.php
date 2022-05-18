<?php
namespace src\model;

use \PDO;

class DatabaseModel{

    /**
     * Nom de la base de donnees MySQL
     */
    private $dbName;

    /**
     * Utilisateur de la BD MySQL
     */
    private $user;

    /**
     * Mot de pass de l'utilisateur
     */
    private $pass;

    /**
     * Host
     */
    private $host;

    /**
     * Port
     */
    private $port;

    /**
     * Instance de la classe PDO
     */
    private $pdo;

    /**
     * Instance de la classe DataBase (pour pattern Singleton)
     */
    private static $instance = null;

    /**
     * Constructeur prive (pattern Singleton)
     */
    private function __construct(array $connectionData){

        $this->host = $connectionData['host'];
        $this->port = $connectionData['port'];
        $this->dbName = $connectionData['dbname'];
        $this->user = $connectionData['user'];
        $this->pass = $connectionData['pass'];

        $this->pdo = new PDO('mysql:host='.$this->host.';port='.$this->port.';dbname='.$this->dbName.';charset=UTF8MB4', $this->user, $this->pass);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * @return instance qui est l'instance de la classe Database
     */
    public static function getInstance(array $connectionData){
        if(is_null(self::$instance)){
            self::$instance = new DatabaseModel($connectionData);
        }
        return self::$instance;
    }

    /**
     * @return mixed
     */
    public function getPdo()
    {
        return $this->pdo;
    }



}
?>