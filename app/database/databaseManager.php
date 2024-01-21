<?php

//kizárólag másik php file hívhatja meg!


require_once("../../setup.php");
require_once(ROOT_PATH . "/app/database/connect.php");


//felhaszáló ellenőrzése és bejelentkeztetése
function CheckLoginCredentials($email, $password){
    global $conn;

    $query = "SELECT `users`.`id` FROM `users` WHERE BINARY `users`.`email` = BINARY '" . $email . "' AND BINARY `users`.`password` = BINARY '" . $password . "'";
    $response = $conn->query($query);
    if($response->num_rows == 1){
        $record = $response->fetch_assoc();
        $_SESSION["userId"] = $record['id'];
        $_SESSION['login'] = true;
        return true;
    } else{
        return false;
    }
}

function RegisterNewUser($familyName, $firstName, $username, $email, $password, $newsletter = "false"){
    global $conn;

    $query = "SELECT `users`.`id`, `users`.`email`, `users`.`username` FROM `users` WHERE `users`.`username` = '$username' OR `users`.`email` = '$email'";
    $response = $conn->query($query);
    if($response->num_rows != 0){
        $record = $response->fetch_assoc();
        if($record["email"] == $email){
            return json_encode(["response" => "error", "error" => "Ezt az email címet már regisztrálták!"]);
        } else if ($record["username"] == $username){
            return json_encode(["response" => "error", "error" => "Ez a felhasználónév már foglalt!"]);
        }
    } else {
        $query = "INSERT INTO `users` (`role_id`, `email`, `familyName`, `firstName`, `username`, `password`, `newsletter_sub`) VALUES (1, '$email', '$familyName', '$firstName', '$username', '$password', $newsletter)";
        if($conn->query($query)){
            $_SESSION['userId'] = $conn->insert_id;
            $_SESSION['login'] = true;
            return json_encode(["response" => "success", "route" => BASE_URL]);
        } else {
            return json_encode(["response" => "error", "error" => "A felhasználót nem sikerült létrehozni!"]);
        }
    }

    return json_encode(["response" => "error", "error" => "Sikertelen regisztráció!"]);
}

function GetUserData() {
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "SELECT `users`.`familyName`, `users`.`firstName`, `users`.`username`, `users`.`email`, `users`.`newsletter_sub` FROM `users` WHERE `users`.`id` = $userId";
    $response = $conn->query($query);
    if($response->num_rows == 1) {
        $record = $response->fetch_assoc();
        return json_encode(["response" => "success", "user" => ["familyName" => $record["familyName"], "firstName" => $record["firstName"], "username" => $record["username"], "email" => $record["email"], "newsletter" => $record["newsletter_sub"]]]);
    } else {
        return json_encode(["response" => "error", "error_title" => "Azonosítási hiba", "error_description" => "Nincs bejelentkezett felhasználó!"]);
    }
}

function UpdateUserData($familyName, $firstName, $username, $password){
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "SELECT `users`.`id` FROM `users` WHERE `users`.`username` = '$username'";
    $response = $conn->query($query);
    $record = $response->fetch_assoc();
    if(($response->num_rows == 1 && $record["id"] == $userId) || ($response->num_rows == 0)){
        $query = "UPDATE `users` SET `familyName` = '$familyName', `firstName` = '$firstName', `username` = '$username', `password` = '$password' WHERE `users`.`id` = $userId";
        if($conn->query($query)){
            $query = "SELECT `users`.`familyName`, `users`.`firstName`, `users`.`username`, `users`.`email`, `users`.`newsletter_sub` FROM `users` WHERE `users`.`id` = $userId";
            $response = $conn->query($query);
            $record = $response->fetch_assoc();
            return json_encode(["response" => "success",  "user" => ["familyName" => $record["familyName"], "firstName" => $record["firstName"], "username" => $record["username"]]]);
        } else {
            return json_encode(["response" => "error","error_title" => "Módosítási hiba!", "error_description" => "A felhasználót nem sikerült módosítani!"]);
        }
    } else {
        return json_encode(["response" => "error", "error_title" => "Létező felhasználónév!", "error_description" => "Ez a felhasználónév már foglalt!"]);
    }

    return json_encode(["response" => "error","error_title" => "Sikertelen módosítás!", "error_description" => "Sikertelen módosítás!"]);
}

function NewsletterSignUp() {
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "UPDATE `users` SET `users`.`newsletter_sub` = true WHERE `users`.`id` = $userId";
    if($conn->query($query)){
        return json_encode(["response" => "success"]);
    } else {
        return json_encode(["response" => "error","error_title" => "Sikertelen feliratkozás!", "error_description" => "A felhasználót nem tudtuk rögzítani!"]);
    }
}

function GetPatternId($patternUrl){
    global $conn;
    $query = "SELECT `patterns`.`id` FROM `patterns` WHERE `patterns`.`unlock_url` = '$patternUrl'";
    $response = $conn->query($query);
    if($response->num_rows == 1){
        $patternId = $response->fetch_assoc()['id'];
        $query = "SELECT `images`.`url`, `images`.`title` FROM `images` INNER JOIN `pattern_image` ON `pattern_image`.`image_id` = `images`.`id` WHERE `pattern_image`.`pattern_id` = $patternId AND `images`.`type` = 'cover'";
        $response = $conn->query($query);
        if($response->num_rows == 1){
            return $response->fetch_assoc();
        } else{
            return "no-cover";
        }
    } else {
        return false;
    }  
}

function UnlockPattern($patternId, $password) {
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "SELECT * FROM `pattern_user` WHERE `pattern_user`.`user_id` = $userId AND `pattern_user`.`pattern_id` = '$patternId'";
    $response = $conn->query($query);
    if($response->num_rows == 0) {
        $query = "SELECT `patterns`.`id` FROM patterns WHERE `patterns`.`id` = '$patternId' AND BINARY `patterns`.`password` = BINARY '$password'";
        $response = $conn->query($query);
        if($response->num_rows > 0) {
            $query = "INSERT INTO `pattern_user` (`user_id`, `pattern_id`) VALUES ($userId, $patternId)";
            if($conn->query($query)){
                return true;
            }
        } else {
            return false;
        }
    } else {
        return true;
    } 
}