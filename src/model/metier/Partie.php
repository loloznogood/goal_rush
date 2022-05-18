<?php


namespace src\model\metier;
use \src\model\ToolModel;

class Partie{

    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $home_team;

    /**
     * @var
     */
     private $away_team;

     /**
      * @var
      */
      private $score_home_team;

      /**
       * @var
       */
      private $score_away_team;

      /**
       * @var
       */
      private $home_team_rating;

    /**
     * @var
     */
    private $away_team_rating;

    /**
     * @var
     */
    private $draft_rating;

    /**
     * @var
     */
    private $result;

    /**
     * @var
     */
    private $date;

      /**
       * Bet constructor.
       * @param $id
       * @param $home_team
       * @param $away_team
       * @param $score_home_team
       * @param $score_away_team
       * @param $home_team_rating
       * @param $away_team_rating
       * @param $draft_rating
       * @param $result
       * @param $date
       */

       public function __construct($id, $home_team, $away_team, $score_home_team, $score_away_team, $home_team_rating, $away_team_rating, $draft_rating, $result, $date)
       {
           $this->id = $id;
           $this->home_team = $home_team;
           $this->away_team = $away_team;
           $this->score_home_team = $score_home_team;
           $this->score_away_team = $score_away_team;
           $this->home_team_rating = $home_team_rating;
           $this->away_team_rating = $away_team_rating;
           $this->draft_rating = $draft_rating;
           $this->result = $result;
           $this->date = $date;
       }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getHomeTeam()
    {
        return $this->home_team;
    }

    /**
     * @param mixed $home_team
     */
    public function setHomeTeam($home_team): void
    {
        $this->home_team = $home_team;
    }

    /**
     * @return mixed
     */
    public function getAwayTeam()
    {
        return $this->away_team;
    }

    /**
     * @param mixed $away_team
     */
    public function setAwayTeam($away_team): void
    {
        $this->away_team = $away_team;
    }

    /**
     * @return mixed
     */
    public function getScoreHomeTeam()
    {
        return $this->score_home_team;
    }

    /**
     * @param mixed $score_home_team
     */
    public function setScoreHomeTeam($score_home_team): void
    {
        $this->score_home_team = $score_home_team;
    }

    /**
     * @return mixed
     */
    public function getScoreAwayTeam()
    {
        return $this->score_away_team;
    }

    /**
     * @param mixed $score_away_team
     */
    public function setScoreAwayTeam($score_away_team): void
    {
        $this->score_away_team = $score_away_team;
    }

    /**
     * @return mixed
     */
    public function getHomeTeamRating()
    {
        return $this->home_team_rating;
    }

    /**
     * @param mixed $home_team_rating
     */
    public function setHomeTeamRating($home_team_rating): void
    {
        $this->home_team_rating = $home_team_rating;
    }

    /**
     * @return mixed
     */
    public function getAwayTeamRating()
    {
        return $this->away_team_rating;
    }

    /**
     * @param mixed $away_team_rating
     */
    public function setAwayTeamRating($away_team_rating): void
    {
        $this->away_team_rating = $away_team_rating;
    }

    /**
     * @return mixed
     */
    public function getDraftRating()
    {
        return $this->draft_rating;
    }

    /**
     * @param mixed $draft_rating
     */
    public function setDraftRating($draft_rating): void
    {
        $this->draft_rating = $draft_rating;
    }

    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result): void
    {
        $this->result = $result;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }



}

?>