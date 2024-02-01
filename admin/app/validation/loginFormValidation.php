<?php 

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
require_once("../../setup.php");
if(!IS_AJAX && !strpos($_SERVER['HTTP_REFERER'],getenv('HTTP_HOST'))) {
    Redirect();
} else {
    require_once("../../setup.php");
    $jsonData = json_decode(file_get_contents('php://input'));
    if($jsonData->method == "login"){
        if(!filter_var($jsonData->user->email, FILTER_VALIDATE_EMAIL) || strlen($jsonData->user->password) < 8){
            $error = ["response" => "error", "error" => "Hibás email cím vagy jelszó!"];
            echo(json_encode($error));
            return;
        } else {
            require_once(ROOT_PATH . "/app/database/databaseManager.php");
            
            $CheckLoginResponse = json_decode(CheckLoginCredentials(htmlspecialchars($jsonData->user->email), hash("sha256", htmlspecialchars($jsonData->user->password))));
            if($CheckLoginResponse->response == "success"){
                echo(json_encode($CheckLoginResponse));
                return;
            } else{
                echo(json_encode(["response" => "error", "error" => "Hibás email cím vagy jelszó!"]));
                return;
            }
        }
    }
}