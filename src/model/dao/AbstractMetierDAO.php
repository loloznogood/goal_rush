<?php


namespace src\model\dao;
use \src\controller\DatabaseController as DatabaseController;

/**
 * Class AbstractMetierDAO
 * @package src\model\dao
 */
abstract class AbstractMetierDAO
{

    /**
     * @var
     */
    private $databaseCtrl;


    /**
     * @return \DatabaseController
     */
    public function getDatabaseCtrl(): DatabaseController
    {
        return $this->databaseCtrl;
    }


    /**
     * @param DatabaseController $databaseCtrl
     */
    public function setDatabaseCtrl(DatabaseController $databaseCtrl): void
    {
        $this->databaseCtrl = $databaseCtrl;
    }








}