<?php


namespace src\model\metier;

use \src\model\ToolModel;


class Page
{
    /**
     * @var String $id identifiant de la page
     */
    private $id;
    /**
     * @var String $titre titre de la page
     */
    private $titre;
    /**
     * @var String $contenu contenu de la page.
     */
    private $contenu;

    /**
     * Page constructor.
     * @param String $id
     * @param String $titre
     * @param String $contenu
     */
    public function __construct(String $id, String $titre, String $contenu)
    {
        $this->id = $id;
        $this->titre = $titre;
        $this->contenu = $contenu;
    }

    /**
     * @return String
     */
    public function getId(): String
    {
        return $this->id;
    }

    /**
     * @param String $id
     */
    public function setId(String $id): void
    {
        $this->id = $id;
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
    public function getContenu(): String
    {
        return $this->contenu;
    }

    /**
     * @return string
     */
    public function getContenuDecode()
    {
        return htmlspecialchars_decode($this->contenu, ENT_HTML5);
    }

    /**
     * @param String $contenu
     */
    public function setContenu(String $contenu): void
    {
        $this->contenu = $contenu;
    }

    public function getUrl(){
        $t = new ToolModel();
        $intitule = $t->urlFriendly($this->intitule);
        return '/'.$intitule;
    }

}