<?php


namespace src\model\metier;
use \src\model\ToolModel;

class Bet{

    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $ticket_id;

    /**
     * @var
     */
     private $party_id;

     /**
      * @var
      */
      private $prono;

      /**
       * @var
       */
      private $potentiel;

      /**
       * @var
       */
      private $result;

      /**
       * Bet constructor.
       * @param $id
       * @param $ticket_id
       * @param $party_id
       * @param $prono
       * @param $potentiel
       * @param $result
       */

       public function __construct($id, $ticket_id, $party_id, $prono, $potentiel, $result)
       {
           $this->id = $id;
           $this->ticket_id = $ticket_id;
           $this->party_id = $party_id;
           $this->prono = $prono;
           $this->potentiel = $potentiel;
           $this->result = $result;
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
        public function getTicket_id(){
            return $this->ticket_id;
        }

        /**
         * @param mixed
         */
        public function setTicket_id($ticket_id){
            $this->ticket_id = $ticket_id;
        }

        /**
         * @return mixed
         */
        public function getParty_id(){
            return $this->party_id;
        }

        /**
         * @param mixed
         */
         public function setParty_id($party_id){
             $this->party_id = $party_id;
         }

         /**
          * @return mixed
          */
          public function getProno(){
              return $this->prono;
          }

          /**
           * @param mixed
           */
          public function setProno($prono){
              $this->prono = $prono;
          }

          /**
           * @return mixed
           */
          public function getPotentiel(){
              return $this->potentiel;
          }

          /**
           * @param mixed
           */
          public function setPotentiel($potentiel){
              $this->potentiel = $potentiel;
          }

          /**
           * @return mixed
           */
          public function getResult(){
              return $this->result;
          }

          /**
           * @param mixed
           */
           public function setResult($result){
               $this->result = $result;
           }

}

?>