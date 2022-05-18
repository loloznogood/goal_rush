<?php
namespace src\model;
/**
 * Cette classe permet de configurer les parametre de base du site
 */
class ConfigurationModel{

    /**
     * Mode developpement
     */
    public const MODE_DEV = "dev";
    /**
     * Mode de production
     */
    public const MODE_PRODUCTION = "production";

    /**
     * Tableau de parametres
     */
    private $settings;


    /**
     * Constructeur
     */
    public function __construct(string $mode){
        if($mode == "dev"){
            $this->settings = [
                'displayErrorDetails' => true,
                'addContentLengthHeader' => false,
                'determineRouteBeforeAppMiddleware' => true,
                'db' => [
                    'host' => 'localhost',
                    'user' => 'root',
                    'pass' => '',
                    'dbname' => 'goal_rush',
                    'port' => '3307'
                ]
            ];
        }

        else if($mode == "production"){
            $this->settings = [
                'displayErrorDetails' => false,
                'addContentLengthHeader' => false,
                'determineRouteBeforeAppMiddleware' => true,
                'db' => [
                    'host' => 'localhost',
                    'user' => 'jumo-france',
                    'pass' => 'wsHUhhu4yf!',
                    'dbname' => 'jumo-france'
                ]
            ];
        }
    }

    /**
     * @return $settings les configurations du site
     */
    public function getSettings():array{
        return $this->settings;
    }
}
?>