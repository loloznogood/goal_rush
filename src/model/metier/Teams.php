<?php


namespace src\model\metier;
use \src\model\ToolModel;

class Teams{
    /**
     * @var 
     */
    private $id;

    /**
     * @var
     */
    private $name;

    /**
     * @param $id
     * @param $name
     */

     public function __construct($id, $name)
     {
         $this->id = $id;
         $this->name = $name;
     }

     /**
      * @return mixed
      */
      public function getId(){
          return $this->id;
      }

      /**
       * @param mixed
       */
      public function setId($id){
          $this->id = $id;
      }

      /**
       * @return mixed
       */
      public function getName(){
          return $this->name;
      }

      /**
       * @param mixed
       */
      public function setName($name){
          $this->name = $name;
      }
}