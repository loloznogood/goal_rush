<?php


namespace src\model\metier;


class Lien
{    /**
    * @var int $id identifiant du lien
    */
   private $id;
    /**
     * @var String $titre titre du lie
     */
    private $titre;
    /**
     * @var String $type du lien
     */
    private $tytre;
    /**
     * @var String $url lien url 
     */
    private $url;
    /**
     * @var int $idCategorie identifiant du la  categorie a laquelle appartient le lien s'il est de type sous-catégorie.
     */
    private $idCategorie;

    /**
     * Page constructor.
     *  @param int $id
     * @param String $type
     * @param String $titre
     * @param String $url
     *  @param int $idCategorie
     * 
     * 
     */
    public function __construct(int $id,String $type, String $titre, String $url, ?int $idCategorie )
    {   
        $this->id = $id;
        $this->type=$type;
        $this->titre = $titre;
        $this->url = $url;
        $this->idCategorie = $idCategorie;
        
    }
    /**
     * @return String
     */
    public function getTitre(): String
    {
        return $this->titre;
    }

    /**
     * @param String $titre
     */
    public function setTitre(String $titre): void
    {
        $this->titre = $titre;
    }

    /**
     * @return String
     */
    public function getUrl(): String
    {
        return $this->url;
    }

  
    /**
     * @param String $url
     */
    public function setUrl(String $url): void
    {
        $this->url = $url;
    }
 /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $url
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }


    /**
     * @return int
     */
    public function getIdCategorie(): ?int
    {
        return $this->idCategorie;
    }

    /**
     * @param int $url
     */
    public function setIdCategorie(int $idCategorie): void
    {
        $this->IdCategorie = $idCategorie;
    }
    
    /**
     * @return String
     */
    public function getType(): String
    {
        return $this->type;
    }

    /**
     * @param String $titre
     */
    public function setType(String $type): void
    {
        $this->type = $type;
    }


}