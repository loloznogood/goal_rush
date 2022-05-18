<?php
$bd = mysqli_connect($adresse_mysql, $base_id, $base_pw, $mabase);
if (!$bd) {
    die('Erreur de connexion mysql');
}
