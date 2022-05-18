<?php
namespace src\controller;
use src\model\ConfigurationModel as ConfigurationModel;
/**
 * Cette classe permet de configurer les parametre de base du site
 */
class ConfigurationController{

    public function __construct(){}

    public function checkMode(string $mode){
        $m = trim($mode);
        if(($m != ConfigurationModel::MODE_DEV) && ($m != ConfigurationModel::MODE_PRODUCTION)){
            throw new Exception("Erreur de configuration.");
        }
        else{
            return new ConfigurationModel($mode);
        }
    }
}

?>