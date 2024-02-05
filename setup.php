<?php 
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
session_start();

if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    http_response_code(403);
}

define("ROOT_PATH", realpath(dirname(__FILE__)));
define("BASE_URL", "http://horgobox.local/");
// define("BASE_URL", "https://horgobox.csipcsiripp.hu");

$sqlServer = "localhost";
$sqlUser = "app";
$sqlPassword = "application";
$sqlDatabase = "horgobox";

// $sqlServer = "localhost";
// $sqlUser = "fotoment_csipcsirippHorgoboxDatabaseManager";
// $sqlPassword = "SP?~8[~cNNF+";
// $sqlDatabase = "fotoment_horgobox";


$noReplyEmail = "no-reply@csipcsiripp.hu";
$noReplyEmailPassword = "&c(!8VoD-]d+";

function Redirect($url = ""){
    header("location: " . BASE_URL .  $url);
    exit();
}

function CheckSession(){
    if(!isset($_SESSION['login']) || !$_SESSION['login']){
        Redirect("bejelentkezes");
    }
}