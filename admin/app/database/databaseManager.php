<?php

//kizárólag másik php file hívhatja meg!

require_once(dirname(__DIR__, 2) . "/setup.php");
require_once(ROOT_PATH . "/app/database/connect.php");
// require_once(ROOT_PATH . "/app/endpoints/mailer.php");
// require_once(ROOT_PATH . "/app/includes/mailGenerator.php");


//felhasználói jogosultságok ellenőrzése
function CheckUserPermissions($permissions) {
    global $conn;

    if (!isset($_SESSION["userId"])){
        Redirect("bejelentkezes");
    } else {
        $query = "SELECT `role_id` FROM `admins` WHERE id = '" . $_SESSION["userId"] . "'";
        $resp = $conn->query($query);
        $rowCount = $resp->num_rows;
        if ($rowCount == 0) {
            Redirect("bejelentkezes");
        } else {
            $roleId = $resp->fetch_assoc()["role_id"];
            $query = "SELECT `permissions` FROM `roles` WHERE id = '" . $roleId . "'";
            $response = $conn->query($query);

            if($response->num_rows != 0){
                $permissionsJson = json_decode($response->fetch_assoc()["permissions"]);
                $permissionsArray = $permissionsJson->permissions;
                
                if($permissionsArray[0] != "ALL") {
                    foreach ($permissions as $value) {
                        if (!in_array($value, $permissionsArray)) {
                            Redirect();
                        }
                    }
                }
            } else {
                Redirect();
            }
            
        }
    } 
}


//felhaszáló ellenőrzése és bejelentkeztetése
function CheckLoginCredentials($email, $password){
    global $conn;

    if(isset($_SESSION['userId'])){
        return json_encode(["response" => "success", "route" => BASE_URL]);
    }

    $query = "SELECT `admins`.`id` FROM `admins` WHERE BINARY `admins`.`email` = BINARY '" . $email . "' AND BINARY `admins`.`password` = BINARY '" . $password . "'";
    $response = $conn->query($query);
    if($response->num_rows == 1){
        $record = $response->fetch_assoc();
        $_SESSION["userId"] = $record['id'];
        $_SESSION['adminLogin'] = true;
        return json_encode(["response" => "success", "route" => BASE_URL]);
    } else{
        return json_encode(["response" => "error"]);
    }
}


//jelszó visszaállítása - email küldése
function ResetPassword($email){
    global $conn;

    $query = "SELECT `admins`.`id` FROM `admins` WHERE `admins`.`email` = '$email'";
    $response = $conn->query($query);
    
    if($response->num_rows == 1){
        $userId = $response->fetch_assoc()['id'];
        $token = bin2hex(random_bytes(16));
        
        $query = "INSERT INTO `validation_codes` (`user_id`, `validation_data`, `type`) VALUES ($userId, '$token', 'adminPasswordReset')";
        if($conn->query($query)){
            $query = "INSERT INTO `logs` (`user_id`, `type`, `ip`) VALUES ($userId, 'sendAdminForgotPasswordEmail', '" . $_SERVER['REMOTE_ADDR'] . "')";
            $conn->query($query);

            $body = GenerateForgotPassword($token, $userId);
            sendMail("no-reply@csipcsiripp.hu", $email, "Elfelejtett jelszó", $body);
            return json_encode(["response" => "success"]);
        }  else {
            return json_encode(["response" => "error", "error_title" => "Generálási hiba!", "error_description" => "Sikertelen link létrehozás!"]);
        }
    } else {
        return json_encode(["response" => "error", "error_title" => "Hibás email!", "error_description" => "A megadott email címmel nem regisztráltak felhasználót!"]);
    }
}


//jelszó visszaállítása - frissítés
function UpdatePassword($password, $token, $userId){
    global $conn;

    $query = "SELECT `validation_codes`.`sentTime` FROM `validation_codes` WHERE `validation_codes`.`user_id` = $userId AND `validation_codes`.`validation_data` = '$token' AND `validation_codes`.`type` = 'passwordReset'";
    $response = $conn->query($query);
    
    if($response->num_rows == 1){
        $query = "UPDATE `admins` SET `admins`.`password` = '$password' WHERE `admins`.`id` = $userId";
        if($conn->query($query)){
            $query = "DELETE FROM `validation_codes` WHERE `validation_codes`.`user_id` = $userId AND `validation_codes`.`validation_data` = '$token'";
            $conn->query($query);

            $query = "INSERT INTO `logs` (`user_id`, `type`, `ip`) VALUES ($userId, 'updateForgotPassword', '" . $_SERVER['REMOTE_ADDR'] . "')";
            $conn->query($query);

            $query = "SELECT `admins`.`email` FROM `admins` WHERE `admins`.`id` = $userId";
            $email = $conn->query($query)->fetch_assoc()['email'];
            $body = GenerateForgotPasswordSuccess();
            sendMail("no-reply@csipcsiripp.hu", $email, "Sikeres jelszóváltoztatás", $body);
            return json_encode(["response" => "success", "route" => BASE_URL . "bejelentkezes"]);
        } else{
            return json_encode(["response" => "error", "error_title" => "Sikertelen művelet!", "error_description" => "Nem sikerült frissíteni a jelszót!"]);
        }
    } else{
        return json_encode(["response" => "error", "error_title" => "Érvénytelen link!", "error_description" => "A visszaállításhoz használt link érvénytelen!"]);
    }
}


//felhasználói adatok kinyerése
function GetUserData() {
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "SELECT `admins`.`familyName`, `admins`.`firstName`, `admins`.`username`, `admins`.`email`, `admins`.`newsletter_sub` FROM `admins` WHERE `admins`.`id` = $userId";
    $response = $conn->query($query);
    if($response->num_rows == 1) {
        $record = $response->fetch_assoc();
        return json_encode(["response" => "success", "user" => ["familyName" => $record["familyName"], "firstName" => $record["firstName"], "username" => $record["username"], "email" => $record["email"], "newsletter" => $record["newsletter_sub"]]]);
    } else {
        return json_encode(["response" => "error", "error_title" => "Azonosítási hiba", "error_description" => "Nincs bejelentkezett felhasználó!"]);
    }
}


//felhasználói profil frissítése
function UpdateUserData($familyName, $firstName, $username, $password){
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "SELECT `admins`.`id` FROM `admins` WHERE `admins`.`username` = '$username'";
    $response = $conn->query($query);
    $record = $response->fetch_assoc();
    if(($response->num_rows == 1 && $record["id"] == $userId) || ($response->num_rows == 0)){
        $query = "UPDATE `admins` SET `familyName` = '$familyName', `firstName` = '$firstName', `username` = '$username', `password` = '$password' WHERE `admins`.`id` = $userId";
        if($conn->query($query)){
            $query = "SELECT `admins`.`familyName`, `admins`.`firstName`, `admins`.`username`, `admins`.`email`, `admins`.`newsletter_sub` FROM `admins` WHERE `admins`.`id` = $userId";
            $response = $conn->query($query);
            $record = $response->fetch_assoc();

            $query = "INSERT INTO `logs` (`user_id`, `type`, `ip`) VALUES ($userId, 'updateProfile', '" . $_SERVER['REMOTE_ADDR'] . "')";
            $conn->query($query);

            return json_encode(["response" => "success",  "user" => ["familyName" => $record["familyName"], "firstName" => $record["firstName"], "username" => $record["username"]]]);
        } else {
            return json_encode(["response" => "error","error_title" => "Módosítási hiba!", "error_description" => "A felhasználót nem sikerült módosítani!"]);
        }
    } else {
        return json_encode(["response" => "error", "error_title" => "Létező felhasználónév!", "error_description" => "Ez a felhasználónév már foglalt!"]);
    }

    return json_encode(["response" => "error","error_title" => "Sikertelen módosítás!", "error_description" => "Sikertelen módosítás!"]);
}


//hírlevél feliratkozás
function NewsletterSignUp() {
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "UPDATE `admins` SET `admins`.`newsletter_sub` = true WHERE `admins`.`id` = $userId";
    if($conn->query($query)){
        return json_encode(["response" => "success"]);
    } else {
        return json_encode(["response" => "error","error_title" => "Sikertelen feliratkozás!", "error_description" => "A felhasználót nem tudtuk rögzítani!"]);
    }
}


//tananyag id lekérése név alapján
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


//tananyag feloldása
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
                $query = "INSERT INTO `logs` (`user_id`, `type`, `ip`) VALUES ($userId, 'unlockBox', '" . $_SERVER['REMOTE_ADDR'] . "')";
                $conn->query($query);
                return true;
            }
        } else {
            return false;
        }
    } else {
        return true;
    }
}


//felhasználó tananyagainak lekérése
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


//felhasználó által még nem feloldott dobozok lekérdezése
function GetLockedBoxes(){
    global $conn;

    $userId = $_SESSION['userId'];

    $query = "SELECT `boxes`.`name` AS 'box_name', `boxes`.`url` AS box_url, `images`.`url` AS 'image_url', `images`.`title` AS 'image_title' FROM `boxes` LEFT JOIN `box_user` ON `boxes`.`id` = `box_user`.`box_id` INNER JOIN `box_image`ON `box_image`.`box_id` = `boxes`.`id` INNER JOIN `images` ON `images`.`id` = `box_image`.`image_id` WHERE `images`.`type` = 'cover' AND `boxes`.`id` NOT IN (SELECT `box_user`.`box_id` FROM `box_user` WHERE `box_user`.`user_id` = $userId)";
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


// kiválasztott doboz betöltése
function LoadBox($boxName){
    global $conn;

    $userId = $_SESSION['userId'];

    $query = "SELECT `boxes`.`id` AS 'box_id', `boxes`.`name`, `boxes`.`description` FROM `box_user` INNER JOIN `boxes` ON `boxes`.`id` = `box_user`.`box_id` WHERE `boxes`.`url` = '/$boxName' AND `box_user`.`user_id` = $userId";
    $response = $conn->query($query);
    if($response->num_rows > 0){
        $boxData = array();
        $boxData["box"][] = $response->fetch_assoc();
        $boxId = $boxData["box"][0]['box_id'];

        $query = "SELECT `videos`.* FROM `videos` INNER JOIN `box_video` ON `box_video`.`video_id` = `videos`.`id` WHERE `box_video`.`box_id` = $boxId";
        $response = $conn->query($query);
        while($row = $response->fetch_assoc()){
            $boxData['videos'][] = $row;
        }

        $query = "SELECT `download_files`.* FROM `download_files` INNER JOIN `box_download` ON `box_download`.`download_file_id` = `download_files`.`id` WHERE `box_download`.`box_id` = $boxId";
        $response = $conn->query($query);
        while($row = $response->fetch_assoc()){
            $boxData['downloads'][] = $row;
        }

        return $boxData;
        
    } else {
        return false;
    }
}