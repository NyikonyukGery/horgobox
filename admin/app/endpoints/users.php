<?php

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
require_once("../../setup.php");
if(!IS_AJAX && !strpos($_SERVER['HTTP_REFERER'],getenv('HTTP_HOST'))) {
    Redirect();
} else {
    $jsonData = json_decode(file_get_contents('php://input'));
    if($jsonData->method == "getUsers"){
        require_once(ROOT_PATH . "/app/database/databaseManager.php");
        $usersDump = GetUsers(htmlspecialchars($jsonData->from));
        $numberOfUsers = $usersDump['userCount'];
        $users = $usersDump['users'];

        echo(json_encode(["response" => "success", "users" => $users, "userCount" => $numberOfUsers, "base_url" => BASE_URL]));
        return;

    } else if($jsonData->method == "searchUsers"){
        require_once(ROOT_PATH . "/app/database/databaseManager.php");
        $usersDump = SearchUsers(htmlspecialchars($jsonData->from), htmlspecialchars($jsonData->phrase));
        $numberOfUsers = $usersDump['userCount'];
        $users = $usersDump['users'];

        echo(json_encode(["response" => "success", "users" => $users, "userCount" => $numberOfUsers, "base_url" => BASE_URL]));
        return;        
    } else if($jsonData->method == "toggleNewsletterSub"){
        require_once(ROOT_PATH . "/app/database/databaseManager.php");
        $newsletterSub = ToggleNewsletterSub(htmlspecialchars($jsonData->userId), $jsonData->newStatus);
        if($newsletterSub){
            echo(json_encode(["response" => "success"]));
        } else {
            echo(json_encode(["response" => "error", "error_title" => "Sikertelen hírlevélmódosítás", "error_description" => "Hiba lépett fel a kísérlet során!"]));
        }
        return;
    } else if($jsonData->method == "getUserLogs"){
        require_once(ROOT_PATH . "/app/database/databaseManager.php");
        $userLogs = GetUserLogs(htmlspecialchars($jsonData->userId));
        if($userLogs != false){
            echo(json_encode(["response" => "success", "logs" => $userLogs]));
        } else {
            echo(json_encode(["response" => "error", "error_title" => "Sikertelen lekérdezés", "error_description" => "A tevékenységnaplót nem sikerült lekérdezni!"]));
        }
        return;
    } else if ($jsonData->method == "sendNewPasswordEmail"){
        require_once(ROOT_PATH . "/app/database/databaseManager.php");
        echo(ResetPasswordUser(htmlspecialchars($jsonData->userId)));
        return;
    }else if($jsonData->method == "updateUser") {
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

        if(!filter_var($jsonData->user->email, FILTER_VALIDATE_EMAIL)){
            $error = true;
            echo(json_encode(["response" => "error", "error" => "Az email formátuma nem megfelelő!"]));
            return;        
        }

        if(!$error){
            require_once(ROOT_PATH . "/app/database/databaseManager.php");
            //adatok előkészítése
            $familyName = htmlspecialchars($jsonData->user->familyName);
            $firstName = htmlspecialchars($jsonData->user->firstName);
            $username = htmlspecialchars($jsonData->user->username);
            $email = htmlspecialchars($jsonData->user->email);
            $userId = htmlspecialchars($jsonData->user->userId);
            echo(UpdateUserDataByAdmin($familyName, $firstName, $username, $email, $userId));
            return;
        } else {
            echo(json_encode(["response" => "error", "error_title" => "Ismeretlen hiba!", "error_description" => "Ismeretlen hiba lépett fel!"]));
            return;
        }
    } else if($jsonData->method == "confirmEmail"){
        require_once(ROOT_PATH . "/app/database/databaseManager.php");
        if(ValidateUserEmail(htmlspecialchars($jsonData->userId))){
            echo(json_encode(["response" => "success"]));
        } else {
            echo(json_encode(["response" => "error", "error_title" => "Ismeretlen hiba!", "error_description" => "Ismeretlen hiba lépett fel!"]));
        }
        return;
    } else if($jsonData->method == "sendNewEmailCode"){
        require_once(ROOT_PATH . "/app/database/databaseManager.php");
        if(SendNewEmailCode(htmlspecialchars($jsonData->userId))){
            echo(json_encode(["response" => "success"]));
        } else {
            echo(json_encode(["response" => "error", "error_title" => "Ismeretlen hiba!", "error_description" => "Ismeretlen hiba lépett fel!"]));
        }
        return;
    }
}