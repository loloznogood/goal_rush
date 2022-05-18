<?php
/**
 * Created by PhpStorm.
 * User: Westermann
 * Date: 21/06/2018
 * Time: 08:28
 */

namespace src\model\dao;

use \src\controller\DatabaseController;

class DAOFactory
{
    private $databaseCtrl;

    /**
     * DAOFactory constructor.
     * @param $database
     */
    public function __construct(DatabaseController $database)
    {
        $this->databaseCtrl = $database;
    }

    public function getLPDAO(){
        return \src\model\dao\LPDAO::getInstance($this->databaseCtrl);

    }

    public function getGADAO(){
        return \src\model\dao\GADAO::getInstance($this->databaseCtrl);

    }


    public function getGPDAO(){
        return \src\model\dao\GPDAO::getInstance($this->databaseCtrl);

    }

    public function getGPDAOstock(){
        return \src\model\dao\GPDAOstock::getInstance($this->databaseCtrl);

    }

    public function getServiceDAO(){
        return \src\model\dao\ServiceDAO::getInstance($this->databaseCtrl);

    }

    public function getActualiteDAO(){
        return \src\model\dao\ActualiteDAO::getInstance($this->databaseCtrl);

    }

    public function getDepartementDAO(){
        return \src\model\dao\DepartementDAO::getInstance($this->databaseCtrl);

    }

    public function getCommercialDAO(){
        return \src\model\dao\CommercialDAO::getInstance($this->databaseCtrl);

    }

    public function getAdminDAO(){
        return \src\model\dao\AdminDAO::getInstance($this->databaseCtrl);

    }

    public function getPageDAO(){
        return \src\model\dao\PageDAO::getInstance($this->databaseCtrl);

    }

    public function getMailDAO(){
        return \src\model\dao\MailDAO::getInstance($this->databaseCtrl);

    }
    public function getLienDAO(){
        return \src\model\dao\LienDAO::getInstance($this->databaseCtrl);

    }
    public function getCatalogueStandardDAO(){
        return \src\model\dao\CatalogueStandardDAO::getInstance($this->databaseCtrl);

    }

    public function getCatalogueStockDAO(){
        return \src\model\dao\CatalogueStockDAO::getInstance($this->databaseCtrl);

    }


}