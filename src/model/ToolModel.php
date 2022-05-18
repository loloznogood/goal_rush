<?php

namespace src\model;

class ToolModel
{

    public function urlFriendly( String $s) : String {
        $result = $s;
        $result = trim($result);
        $result = mb_strtolower($result, 'UTF-8');
        $result = $this->removeAccent($result);
        $result = $this->makeSlug($result);
        return $result;
    }

    public function removeAccent(String $s) : String {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $s);
    }

    public function makeSlug(String $s) : String {
        return preg_replace(
            array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'),
            array('', '-', ''),
            $s
        );
    }

    /**
    * Cette function retourne la taille maximum d'un fichier qu'un utilisateur
    * peut uploade.
    * @return String taille max sous forme : valeur unite
    */
    public function getMaxUploadedFileSize(){
    function _GetMaxAllowedUploadSize(){
        $Sizes = array();
        $Sizes[] = ini_get('upload_max_filesize');
        $Sizes[] = ini_get('post_max_size');
        $Sizes[] = ini_get('memory_limit');
        for($x=0;$x<count($Sizes);$x++){
            $Last = strtolower($Sizes[$x][strlen($Sizes[$x])-1]);
            $Sizes[$x] = substr($Sizes[$x], 0, -1);
            if($Last == 'k'){
                $Sizes[$x] *= 1024;
            } elseif($Last == 'm'){
                $Sizes[$x] *= 1024;
                $Sizes[$x] *= 1024;
            } elseif($Last == 'g'){
                $Sizes[$x] *= 1024;
                $Sizes[$x] *= 1024;
                $Sizes[$x] *= 1024;
            } elseif($Last == 't'){
                $Sizes[$x] *= 1024;
                $Sizes[$x] *= 1024;
                $Sizes[$x] *= 1024;
                $Sizes[$x] *= 1024;
            }
            $Sizes[$x].$Last;
        }
        return min($Sizes);
    }
    function _Byte2Size($bytes,$RoundLength=1) {
        $kb = 1024;         // Kilobyte
        $mb = 1024 * $kb;   // Megabyte
        $gb = 1024 * $mb;   // Gigabyte
        $tb = 1024 * $gb;   // Terabyte

        if($bytes < $kb) {
            if(!$bytes){
                $bytes = '0';
            }
            return (($bytes + 1)-1).' B';
        } else if($bytes < $mb) {
            return round($bytes/$kb,$RoundLength).' KB';
        } else if($bytes < $gb) {
            return round($bytes/$mb,$RoundLength).' MB';
        } else if($bytes < $tb) {
            return round($bytes/$gb,$RoundLength).' GB';
        } else {
            return round($bytes/$tb,$RoundLength).' TB';
        }
    }
    return _Byte2Size(_GetMaxAllowedUploadSize());
    }

        /**
    * Cette function retourne la taille maximum d'un fichier qu'un utilisateur
    * peut uploade.
    * @return String taille max en bytes
    */
    public function getMaxUploadedFileSizeBytes(){
        function _GetMaxAllowedUploadSize2(){
            $Sizes = array();
            $Sizes[] = ini_get('upload_max_filesize');
            $Sizes[] = ini_get('post_max_size');
            $Sizes[] = ini_get('memory_limit');
            for($x=0;$x<count($Sizes);$x++){
                $Last = strtolower($Sizes[$x][strlen($Sizes[$x])-1]);
                $Sizes[$x] = substr($Sizes[$x], 0, -1);
                if($Last == 'k'){
                    $Sizes[$x] *= 1024;
                } elseif($Last == 'm'){
                    $Sizes[$x] *= 1024;
                    $Sizes[$x] *= 1024;
                } elseif($Last == 'g'){
                    $Sizes[$x] *= 1024;
                    $Sizes[$x] *= 1024;
                    $Sizes[$x] *= 1024;
                } elseif($Last == 't'){
                    $Sizes[$x] *= 1024;
                    $Sizes[$x] *= 1024;
                    $Sizes[$x] *= 1024;
                    $Sizes[$x] *= 1024;
                }
                $Sizes[$x].$Last;
            }
            return min($Sizes);
        }
        function _Byte($bytes) {
            return (($bytes + 1)-1);
        }
        return _Byte(_GetMaxAllowedUploadSize2());
        }
    

    /**
    * Fonction recursive permettant de supprimer un répertoire non vide ( ou vide).
    * @param $dir : le repertoire à supprimer
    * @return True si la suppression s'est bien déroul&eacute;, sinon False
    */
    public function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir"){
                catalogueStandard::rrmdir($dir."/".$object);
                }
                else {
                unlink($dir."/".$object);
                }
            }
            }
            reset($objects);
            return rmdir($dir);
        }
        else{
            return False;
        }
    }

    /**
    * Fonction retournant le chemin absolu de la racine du site public
    * @return chemin absolu
    */
    public function getSiteAbsolutePath(){
        return dirname(dirname(__DIR__)).'/public';
    }

    /**
    * Fonction retournant le chemin absolu de la racine du projet
    * @return chemin absolu
    */
    public function getProjectAbsolutePath(){
        return dirname(dirname(__DIR__));
    }


}