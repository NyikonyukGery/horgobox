<?php


define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
require_once("../../setup.php");
if(!IS_AJAX && !strpos($_SERVER['HTTP_REFERER'],getenv('HTTP_HOST'))) {
    Redirect();
} else {
    require_once("../../setup.php");
    $jsonData = json_decode(file_get_contents('php://input'));
    if($jsonData->method == "getUserData") {
        if(isset($_SESSION["userId"])) {
            require_once(ROOT_PATH . "/app/database/databaseManager.php");
            echo GetUserData();
            return;
        } else {
            echo(json_encode(["response" => "error", "error" => "Nincs bejelentkezett felhasználó!"]));
            return;
        }
    } else if($jsonData->method == "updateUser") {
        $error = false;

        if(strlen($jsonData->user->familyName) < 2 || strlen($jsonData->user->familyName) > 25){
            $error = true;
            echo(json_encode(["response" => "error", "error_title" => "Hibás vezetéknév!", "error_description" => "Min 2, max 25 betű lehet!"]));
            return;
        } else if(!preg_match("/^[a-zA-Z áéíóöőúüű ÁÉÍÓÖŐÚÜŰ]+$/", $jsonData->user->familyName)){
            $error = true;
            echo(json_encode(["response" => "error", "error_title" => "Hibás vezetéknév!", "error_description" => "A kizárólag betűket tartalmazhat!"]));
            return;
        }

        if(strlen($jsonData->user->firstName) < 2 || strlen($jsonData->user->firstName) > 25){
            $error = true;
            echo(json_encode(["response" => "error", "error_title" => "Hibás keresztnév!", "error_description" => "Min 2, max 25 betű lehet!"]));
            return;      
        } else if(!preg_match("/^[a-zA-Z áéíóöőúüű ÁÉÍÓÖŐÚÜŰ]+$/", $jsonData->user->firstName)){
            $error = true;
            echo(json_encode(["response" => "error", "error_title" => "Hibás keresztnév!", "error_description" => "A kizárólag betűket tartalmazhat!"]));
            return;
        }

        if(strlen($jsonData->user->username) < 5 || strlen ($jsonData->user->username) > 25){
            $error = true;
            echo(json_encode(["response" => "error", "error_title" => "Hibás felhasználónév!", "error_description" => "Min 5, max 25 betű lehet!"]));
            return;
        }

        $password = $jsonData->user->password;
        if(strlen($jsonData->user->password) != 0 && (strlen($jsonData->user->password) < 8 || strlen($jsonData->user->password) > 100 || (!preg_match('@[A-Z]@', $password) || !preg_match('@[a-z]@', $password) || !preg_match('@[0-9]@', $password) || !preg_match('@[^\w]@', $password)))){
            $error = true;
            echo(json_encode(["response" => "error", "error_title" => "Hibás jelszó!", "error_description" => "Tartalmaznia kell számot, kis- és nagybetűt, különleges karakter!\nLegalább 8 karakternek kell lennie!"]));
            return;
        }

        if(!$error){
            require_once(ROOT_PATH . "/app/database/databaseManager.php");
            //adatok előkészítése
            $familyName = htmlspecialchars($jsonData->user->familyName);
            $firstName = htmlspecialchars($jsonData->user->firstName);
            $username = htmlspecialchars($jsonData->user->username);
            $password = hash("sha256", htmlspecialchars($password));     
            echo(UpdateUserData($familyName, $firstName, $username, $password));
            return;
        } else {
            echo(json_encode(["response" => "error", "error_title" => "Ismeretlen hiba!", "error_description" => "Ismeretlen hiba lépett fel!"]));
            return;
        }
    } else if($jsonData->method == "newsletterSignUp"){
        require_once(ROOT_PATH . "/app/database/databaseManager.php");
        echo(NewsletterSignUp());
        return;
    }
}