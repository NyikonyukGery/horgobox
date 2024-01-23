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
    } else if($jsonData->method == "register"){
        $error = false;

        if(!$jsonData->user->acceptTerms){
            $error = true;
            echo(json_encode(["response" => "error", "error" => "Az ÁSZF elfogadása kötelező!"]));
            return;
        }

        if(strlen($jsonData->user->familyName) < 2 || strlen($jsonData->user->familyName) > 25){
            $error = true;
            echo(json_encode(["response" => "error", "error" => "A vezetéknév min 2, max 25 betű lehet!"]));
            return;
        } else if(!preg_match("/^[a-zA-Z áéíóöőúüű ÁÉÍÓÖŐÚÜŰ]+$/", $jsonData->user->familyName)){
            $error = true;
            echo(json_encode(["response" => "error", "error" => "A vezetéknév kizárólag betűket tartalmazhat!"]));
            return;
        }

        if(strlen($jsonData->user->firstName) < 2 || strlen($jsonData->user->firstName) > 25){
            $error = true;
            echo(json_encode(["response" => "error", "error" => "A keresztnév min 2, max 25 betű lehet!"]));
            return;      
        } else if(!preg_match("/^[a-zA-Z áéíóöőúüű ÁÉÍÓÖŐÚÜŰ]+$/", $jsonData->user->firstName)){
            $error = true;
            echo(json_encode(["response" => "error", "error" => "A keresztnév kizárólag betűket tartalmazhat!"]));
            return;
        }

        if(strlen($jsonData->user->username) < 5 || strlen ($jsonData->user->username) > 25){
            $error = true;
            echo(json_encode(["response" => "error", "error" => "A felhasználónév min 5, max 25 betű lehet!"]));
            return;
        }

        if(!filter_var($jsonData->user->email, FILTER_VALIDATE_EMAIL)){
            $error = true;
            echo(json_encode(["response" => "error", "error" => "Az email formátuma nem megfelelő!"]));
            return;        
        }

        $password = $jsonData->user->password;
        if(strlen($jsonData->user->password) < 8 || strlen($jsonData->user->password) > 100 || (!preg_match('@[A-Z]@', $password) || !preg_match('@[a-z]@', $password) || !preg_match('@[0-9]@', $password) || !preg_match('@[^\w]@', $password))){
            $error = true;
            echo(json_encode(["response" => "error", "error" => "A jelszónak tartalmaznia kell számot, kis- és nagybetűt, különleges karakter!\nLegalább 8 karakternek kell lennie!"]));
            return;
        }

        if(!$error){
            require_once(ROOT_PATH . "/app/database/databaseManager.php");
            //adatok előkészítése
            $familyName = htmlspecialchars($jsonData->user->familyName);
            $firstName = htmlspecialchars($jsonData->user->firstName);
            $username = htmlspecialchars($jsonData->user->username);
            $email = htmlspecialchars($jsonData->user->email);
            $password = hash("sha256", htmlspecialchars($password));
            $newsletter = htmlspecialchars($jsonData->user->newsletter);
            if(!$newsletter){
                $newsletter = 0;
            }      
            echo(RegisterNewUser($familyName, $firstName, $username, $email, $password, $newsletter));
            return;
            
        } else {
            echo(json_encode(["response" => "error", "error" => "Ismeretlen hiba lépett fel!"]));
            return;
        }
    }
}