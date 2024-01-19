<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
require_once("../../setup.php");
if(!IS_AJAX && !strpos($_SERVER['HTTP_REFERER'],getenv('HTTP_HOST'))) {
    Redirect();
} else {
    $jsonData = json_decode(file_get_contents('php://input'));
    if($jsonData->method == "unlockPattern"){
        if(strlen($jsonData->password) > 7){
            require_once(ROOT_PATH . "/app/database/databaseManager.php");
            if(UnlockPattern(1, $jsonData->password)){
                echo(json_encode(["response" => "success", "route" => BASE_URL]));
            } else {
                echo(json_encode(["response" => "error", "error_title" => "Hibás jelszó!", "error_description" => "A megadott jelszó nem található!"]));
            }
            return;
        } else {
            echo(json_encode(["response" => "error", "error_title" => "Hibás jelszó!", "error_description" => "A megadott jelszó nem található!"]));
            return;
        }
    }
}