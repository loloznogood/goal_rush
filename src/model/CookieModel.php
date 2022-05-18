<?php

namespace src\model;
use src\model\SecurityModel;

class CookieModel
{

    /**
     * @var String $name nom du cookie
     */
    private $name;
    /**
     * @var String $value valeur du cookie
     */
    private $value;
    /**
     * @var String $secureValue valeur securise du cookie
     */
    private $secureValue;
    /**
     * @var String $expire valeur d'expiration du cookie
     */
    private $expire;

    /**
     * CookieModel constructor.
     * @param String $name
     * @param String $value
     * @param String $expire. Valeur par defaut = 86400 (nombre de seconde en une journee).
     */
    public function __construct(String $name, String $value, String $expire = null)
    {
        if(is_null($expire)){
            $expire = strval(time()+60*60*24);
        }
        $this->name = $name;
        $this->value = $value;
        $this->secureValue = SecurityModel::makeSecureValue($value);
        $this->expire = $expire;
    }

    /**
     * @return String
     */
    public function getName(): String
    {
        return $this->name;
    }

    /**
     * @param String $name
     */
    public function setName(String $name): void
    {
        $this->name = $name;
    }

    /**
     * @return String
     */
    public function getValue(): String
    {
        return $this->value;
    }

    /**
     * @param String $value
     */
    public function setValue(String $value): void
    {
        $this->value = $value;
    }

    /**
     * @return String
     */
    public function getExpire(): String
    {
        return $this->expire;
    }

    /**
     * @param String $expire
     */
    public function setExpire(String $expire): void
    {
        $this->expire = $expire;
    }

    public function isSecure() : bool
    {
        if(strstr($this->value, SecurityModel::DELIMITER)){
            $v = explode(SecurityModel::DELIMITER, $this->getValue())[0];
            return SecurityModel::checkSecureValue($v, $this->getValue());
        }
        else{
            return false;
        }
    }




}