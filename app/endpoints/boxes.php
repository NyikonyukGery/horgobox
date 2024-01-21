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
    if($jsonData->method == "unlockBox"){
        if(strlen($jsonData->password) > 7){
            require_once(ROOT_PATH . "/app/database/databaseManager.php");
            if(UnlockPattern(htmlspecialchars($jsonData->boxId), htmlspecialchars($jsonData->password))){
                echo(json_encode(["response" => "success", "route" => BASE_URL]));
            } else {
                echo(json_encode(["response" => "error", "error_title" => "Hibás jelszó!", "error_description" => "A megadott jelszó nem található!"]));
            }
            return;
        } else {
            echo(json_encode(["response" => "error", "error_title" => "Hibás jelszó!", "error_description" => "A megadott jelszó nem található!"]));
            return;
        }
    } else if($jsonData->method == "getBox"){
        require_once(ROOT_PATH . "/app/database/databaseManager.php");
        $boxData = GetPatternId(htmlspecialchars($jsonData->boxName));
        if($boxData == false){
            echo(json_encode(["response" => "error", "error_title" => "Ismeretlen minta!", "error_description" => "Hamarosan átirányítunk!", "route" => BASE_URL]));
            return;
        } else{
            if($boxData != "no-cover"){
                $boxData['url'] = BASE_URL . "/assets/images" . $boxData['url'];
            }
            echo(json_encode(["response" => "success", "box_data" => $boxData]));
            return;
        }
    }
}