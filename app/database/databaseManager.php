<?php

//kizárólag másik php file hívhatja meg!

require_once(dirname(__DIR__, 2) . "/setup.php");
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

function GetboxId($boxUrl){
    global $conn;
    $query = "SELECT `boxes`.`id`, `boxes`.`name`, `boxes`.`webshop_url` FROM `boxes` WHERE `boxes`.`url` = '$boxUrl'";
    $response = $conn->query($query);
    if($response->num_rows == 1){
        $box = $response->fetch_assoc();
        $boxId = $box['id'];
        $query = "SELECT `images`.`url`, `images`.`title` FROM `images` INNER JOIN `box_image` ON `box_image`.`image_id` = `images`.`id` WHERE `box_image`.`box_id` = $boxId AND `images`.`type` = 'cover'";
        $response = $conn->query($query);
        if($response->num_rows == 1){
            $boxData = $response->fetch_assoc();
            $boxData['id'] = $boxId;
            $boxData['name'] = $box['name'];
            $boxData['webshop_url'] = $box['webshop_url'];
            return $boxData;
        } else{
            return "no-cover";
        }
    } else {
        return false;
    }
}

function UnlockBox($boxId, $password) {
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "SELECT * FROM `box_user` WHERE `box_user`.`user_id` = $userId AND `box_user`.`box_id` = '$boxId'";
    $response = $conn->query($query);
    if($response->num_rows == 0) {
        $query = "SELECT `boxes`.`id` FROM `boxes` WHERE `boxes`.`id` = '$boxId' AND BINARY `boxes`.`password` = BINARY '$password'";
        $response = $conn->query($query);
        if($response->num_rows > 0) {
            $query = "INSERT INTO `box_user` (`user_id`, `box_id`) VALUES ($userId, $boxId)";
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

function GetUserBoxes(){
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "SELECT `boxes`.`name` AS \"box_name\", `boxes`.`url` AS \"box_url\", `images`.`url` AS \"image_url\", `images`.`title` AS \"image_title\" FROM `boxes` INNER JOIN `box_user` ON `boxes`.`id` = `box_user`.`box_id` 
    INNER JOIN `box_image` ON `boxes`.`id` = `box_image`.`box_id`
    INNER JOIN `images` ON `box_image`.`image_id` = `images`.`id`
    WHERE `box_user`.`user_id` = $userId AND `images`.`type` = \"cover\";";
    $response = $conn->query($query);
    if($response->num_rows > 0){
        $unlockedBoxes = array();
        while($row = $response->fetch_assoc()){
            $unlockedBoxes[] = $row;
        }
        return $unlockedBoxes;
    } else {
        return false;
    }
}

function GetLockedBoxes(){
    global $conn;

    $userId = $_SESSION['userId'];

    // $query = "SELECT `boxes`.`name` AS 'box_name', `boxes`.`url` AS box_url, `images`.`url` AS 'image_url', `images`.`title` AS 'image_title' FROM `boxes` LEFT JOIN `box_user` ON `boxes`.`id` = `box_user`.`box_id` INNER JOIN `box_image`ON `box_image`.`box_id` = `boxes`.`id` INNER JOIN `images` ON `images`.`id` = `box_image`.`image_id` WHERE (`box_user`.`user_id` != $userId OR `box_user`.`user_id` IS NULL) AND `images`.`type` = 'cover'";
    $query = "SELECT `boxes`.`name` AS 'box_name', `boxes`.`url` AS box_url, `images`.`url` AS 'image_url', `images`.`title` AS 'image_title' FROM `boxes` LEFT JOIN `box_user` ON `boxes`.`id` = `box_user`.`box_id` INNER JOIN `box_image`ON `box_image`.`box_id` = `boxes`.`id` INNER JOIN `images` ON `images`.`id` = `box_image`.`image_id` WHERE `images`.`type` = 'cover' AND `box_user`.`box_id` NOT IN (SELECT `box_user`.`box_id` FROM `box_user` WHERE `box_user`.`user_id` = $userId)";
    $response = $conn->query($query);
    if($response->num_rows > 0){
        $lockedBoxes = array();
        while($row = $response->fetch_assoc()){
            $lockedBoxes[] = $row;
        }
        return $lockedBoxes;
    } else {
        return false;
    }
}