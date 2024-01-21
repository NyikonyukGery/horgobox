<?php 

session_start();

define("ROOT_PATH", realpath(dirname(__FILE__)));
define("BASE_URL", "http://horgobox.local/");
// define("BASE_URL", "https://horgobox.csipcsiripp.hu");
// define("BASE_URL", "http://localhost/");

$sqlServer = "localhost";
$sqlUser = "app";
$sqlPassword = "application";
$sqlDatabase = "horgobox";

function Redirect($url = ""){
    header("location: " . BASE_URL .  $url);
    exit();
}

function CheckSession(){
    if(!isset($_SESSION['login']) || !$_SESSION['login']){
        Redirect("bejelentkezes");
    }
}