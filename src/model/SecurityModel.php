<?php
/**
 * Created by PhpStorm.
 * User: Westermann
 * Date: 02/07/2018
 * Time: 09:40
 */

namespace src\model;


use Couchbase\BooleanFieldSearchQuery;
use JsonSchema\Constraints\StringConstraint;

class SecurityModel
{

    const SECRET = 'JUMO-moreThanSensors+Automation-FRANCE';
    const DELIMITER = '|';

    public static function passwordHash($pw)
    {
        return password_hash($pw, PASSWORD_BCRYPT);
    }

    public static function passwordVerify($pw, $pwHashed){
        return password_verify($pw, $pwHashed);
    }

    public static function generateSalt($length = 10) : String
    {
        $charList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $salt = "";
        $i = 0;
        while($i < $length){
            $salt .= $charList{mt_rand(0, strlen($charList)-1)};
            $i = $i + 1;
        }
        return $salt;
    }

    public static function hash($value)
    {
        return hash('sha256', $value);
    }

    public static function makeSecureValue($value, $salt = NULL)
    {
        if(is_null($salt)){
            $salt = self::generateSalt();
        }
        $hash = self::hash($value . $salt . self::SECRET);
        return $value .self::DELIMITER. $hash . self::DELIMITER . $salt;
    }

    public static function checkSecureValue($secureValue) : bool
    {
        $valueToCheck = explode(self::DELIMITER, $secureValue)[0];
        $salt = explode(self::DELIMITER, $secureValue)[2];
        return $secureValue ==  self::makeSecureValue($valueToCheck, $salt);
    }

    public static function getValueFromSecureValue($secureValue)
    {
        if(self::checkSecureValue($secureValue)){
            return explode(self::DELIMITER, $secureValue)[0];
        }
        else{
            return null;
        }
    }









}