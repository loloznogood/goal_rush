<?php

namespace src\model;


class SearchResultItem
{

    /**
     * @var string $name nom de l'item de recherche
     */
    private $name;
    /**
     * @var string $url lien redirigeant vers l'item
     */
    private $url;
    /**
     * @var string $type type de l'item
     */
    private $type;

    /**
     * SearchResultItem constructor.
     * @param string $name
     * @param string $url
     * @param string $type
     */
    public function __construct($name, $url, $type)
    {
        $this->name = $name;
        $this->url = $url;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }





}