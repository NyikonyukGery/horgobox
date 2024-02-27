<?php

//kizárólag másik php file hívhatja meg!

require_once(dirname(__DIR__, 2) . "/setup.php");
require_once(ROOT_PATH . "/app/database/connect.php");
require_once(ROOT_PATH . "/app/endpoints/mailer.php");
require_once(ROOT_PATH . "/app/includes/mailGenerator.php");


//fordítási tömbök
$logsTranslation = array(
    "sendForgotPasswordEmail" => "Elfelejtett jelszó email küldése",
    "updateProfile" => "Felhasználói profil frissítve",
    "registration" => "Regisztráció",
    "confirmEmail" => "Email megerősítése",
    "unlockBox" => "Tananyag feloldva",
    "updateForgotPassword" => "Elfelejtett jelszó frissítve",
    "adminUpdateProfile" => "Az adminisztrátor frissítette a felhasználó adatait",
    "adminSendForgotPasswordEmail" => "Az adminisztrátor új jelszó emailt küldött",
    "adminNewsletterUpdate" => "Az adminisztrátor frissítette a hírlevél állapotát",
    "adminValidateEmail" => "Az adminisztrátor jóváhagyta az email címet",
    "adminSendEmailCode" => "Az adminisztrátor új email megerősító kódot küldött"
);


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
    return true;
}


//Régebbi azonosítókódok törlése
function RemoveOlderValidationCodes(){
    global $conn;

    //törlés
    $query = "DELETE FROM `validation_codes` WHERE (`validation_codes`.`sentTime` < (NOW() - INTERVAL 3 DAY) AND `validation_codes`.`type` = 'email') OR (`validation_codes`.`sentTime` < (NOW() - INTERVAL 1 DAY) AND `validation_codes`.`type` = 'email')";
    $conn->query($query);
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


//jelszó visszaállítása - email küldése (admin)
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

//jelszó visszaállítása - email küldése (egyéb felhaszáló)
function ResetPasswordUser($userId){
    global $conn;

    $query = "SELECT `users`.`email` FROM `users` WHERE `users`.`id` = $userId";
    $response = $conn->query($query);
    
    if($response->num_rows == 1){
        $email = $response->fetch_assoc()['email'];
        $token = "admin" . bin2hex(random_bytes(16));
        
        $query = "INSERT INTO `validation_codes` (`user_id`, `validation_data`, `type`) VALUES ($userId, '$token', 'adminPasswordReset')";
        if($conn->query($query)){
            $query = "INSERT INTO `logs` (`user_id`, `type`, `ip`) VALUES ($userId, 'adminSendForgotPasswordEmail', '" . $_SERVER['REMOTE_ADDR'] . "')";
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

    $query = "SELECT `admins`.`familyName`, `admins`.`firstName`, `admins`.`username`, `admins`.`email` FROM `admins` WHERE `admins`.`id` = $userId";
    $response = $conn->query($query);
    if($response->num_rows == 1) {
        $record = $response->fetch_assoc();
        return json_encode(["response" => "success", "user" => ["familyName" => $record["familyName"], "firstName" => $record["firstName"], "username" => $record["username"], "email" => $record["email"]]]);
    } else {
        return json_encode(["response" => "error", "error_title" => "Azonosítási hiba", "error_description" => "Nincs bejelentkezett felhasználó!"]);
    }
}


//felhasználói profil frissítése (admin felhasználó)
function UpdateUserData($familyName, $firstName, $username, $password){
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "SELECT `admins`.`id`, `admins`.`password` FROM `admins` WHERE `admins`.`username` = '$username'";
    $response = $conn->query($query);
    $record = $response->fetch_assoc();
    if(($response->num_rows == 1 && $record["id"] == $userId) || ($response->num_rows == 0)){
        if($password == null){
            $password = $record['password'];
        }
        $query = "UPDATE `admins` SET `familyName` = '$familyName', `firstName` = '$firstName', `username` = '$username', `password` = '$password' WHERE `admins`.`id` = $userId";
        if($conn->query($query)){
            $query = "SELECT `admins`.`familyName`, `admins`.`firstName`, `admins`.`username`, `admins`.`email` FROM `admins` WHERE `admins`.`id` = $userId";
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


//felhasználói profil frissítése (egyéb felhasználó)
function UpdateUserDataByAdmin($familyName, $firstName, $username, $email, $id){
    global $conn;

    $userId = $_SESSION["userId"];

    $query = "SELECT `users`.`id` FROM `users` WHERE `users`.`email` = '$email' OR `users`.`username` = '$username'";
    $response = $conn->query($query);
    $record = $response->fetch_assoc();
    if(($response->num_rows == 1 && $record["id"] == $id) || ($response->num_rows == 0)){
        $query = "UPDATE `users` SET `familyName` = '$familyName', `firstName` = '$firstName', `username` = '$username', `email` = '$email' WHERE `users`.`id` = $id";
        if($conn->query($query)){
            $query = "SELECT `users`.`familyName`, `users`.`firstName`, `users`.`username`, `users`.`email` FROM `users` WHERE `users`.`id` = $id";
            $response = $conn->query($query);
            $record = $response->fetch_assoc();

            $query = "INSERT INTO `logs` (`user_id`, `type`, `ip`) VALUES ($userId, 'adminUpdateProfile', '" . $_SERVER['REMOTE_ADDR'] . "')";
            $conn->query($query);


            $body = GenerateAdminProfileUpdate();
            sendMail("no-reply@csipcsiripp.hu", $email, "Felhasználó frissítve", $body);

            return json_encode(["response" => "success",  "user" => ["familyName" => $record["familyName"], "firstName" => $record["firstName"], "username" => $record["username"], "email" => $record["email"]]]);
        } else {
            return json_encode(["response" => "error","error_title" => "Módosítási hiba!", "error_description" => "A felhasználót nem sikerült módosítani!"]);
        }
    } else {
        return json_encode(["response" => "error", "error_title" => "Létező adatok!", "error_description" => "A felhasználónév vagy email már foglalt!"]);
    }

    return json_encode(["response" => "error","error_title" => "Sikertelen módosítás!", "error_description" => "Sikertelen módosítás!"]);
}

//statisztikák lekérdezése
function GetStatistics(){
    global $conn;

    $statistics = array();
    
    //felhasználók száma
    $query = "SELECT COUNT(`users`.`id`) AS 'registeredUsers' FROM `users`";
    $response = $conn->query($query);
    $statistics['registeredUsers'] = $response->fetch_assoc()['registeredUsers'];

    //feloldott dobozok lekérése
    $query = "SELECT COUNT(`box_user`.`user_id`) AS 'unlockedBoxes' FROM `box_user`";
    $response = $conn->query($query);
    $statistics['unlockedPatterns'] = $response->fetch_assoc()['unlockedBoxes'];

    //hírlevél feliratkozások
    $query = "SELECT COUNT(`users`.`id`) AS 'newsletterSignUp' FROM `users` WHERE `users`.`newsletter_sub` = 1";
    $response = $conn->query($query);
    $statistics['newsletterSignUp'] = $response->fetch_assoc()['newsletterSignUp'];

    return $statistics;
}

//felhasználók lekérdezése
function GetUsers($from){
    global $conn;

    $users = array();

    //felhasználók lekérése
    $query = "SELECT `users`.`id`, `users`.`familyName`, `users`.`firstName`, `users`.`username`, `users`.`email`, `users`.`registrationDate` FROM `users` LIMIT 50 OFFSET $from";
    $response = $conn->query($query);

    while($record = $response->fetch_assoc()){
        $users[] = $record;
    }

    $query = "SELECT COUNT(`users`.`id`) AS 'registeredUsers' FROM `users`";
    $userCount = $conn->query($query)->fetch_assoc()['registeredUsers'];

    $returnArray = array();
    $returnArray['users'] = $users;
    $returnArray['userCount'] = $userCount;

    return $returnArray;
}


//keresés a felhasználók között
function SearchUsers($from, $phrase){
    global $conn;

    $users = array();

    //keresett felhaszálók lekérdezése
    $query = "SELECT `users`.`id`, `users`.`familyName`, `users`.`firstName`, `users`.`username`, `users`.`email`, `users`.`registrationDate` FROM `users` WHERE CONCAT_WS('', `users`.`familyName`, `users`.`firstName`, `users`.`username`, `users`.`email`) LIKE '%$phrase%' LIMIT 50 OFFSET $from";
    $response = $conn->query($query);

    while($record = $response->fetch_assoc()){
        $users[] = $record;
    }

    $query = "SELECT COUNT(`users`.`id`) AS 'foundUsers' FROM `users` WHERE CONCAT_WS('', `users`.`familyName`, `users`.`firstName`, `users`.`username`, `users`.`email`) LIKE '%$phrase%'";
    $userCount = $conn->query($query)->fetch_assoc()['foundUsers'];

    $returnArray = array();
    $returnArray['users'] = $users;
    $returnArray['userCount'] = $userCount;

    return $returnArray;
}


//adott felhasználó adatainak lekérdezése
function GetUser($id){
    global $conn, $logsTranslation;

    $user = array();

    //alapadatok lekérdezése
    $query = "SELECT `users`.`valid_email`, `users`.`familyName`, `users`.`firstName`, `users`.`username`, `users`.`email`, `users`.`registrationDate`, `users`.`newsletter_sub` FROM `users` WHERE `users`.`id` = $id";
    $response = $conn->query($query);

    if($response->num_rows != 0){
        $user['basic'] = $response->fetch_assoc();

        //feloldott tananyagok lekérdezése
        $query = "SELECT `boxes`.`name` FROM `boxes` INNER JOIN `box_user` ON `boxes`.`id` = `box_user`.`box_id` WHERE `box_user`.`user_id` = $id";
        $response = $conn->query($query);

        if($response->num_rows != 0){
            $boxes = array();
            while($record = $response->fetch_assoc()){
                $boxes[] = $record['name'];
            }
            $user['boxes'] = $boxes;
        } else {
            $user['boxes'] = false;
        }

        //naplóbejegyzések lekérdezése
        $query = "SELECT `logs`.`type`, `logs`.`ip`, `logs`.`timestamp` FROM `logs` WHERE `logs`.`user_id` = $id ORDER BY `logs`.`timestamp` DESC";
        $response = $conn->query($query);

        if($response->num_rows != 0){
            $logs = array();
            while($record = $response->fetch_assoc()){
                if(array_key_exists($record['type'], $logsTranslation)){
                    $record['type'] = $logsTranslation[$record['type']];
                }
                $logs[] = $record;
            }
            $user['logs'] = $logs;
        } else {
            $user['logs'] = false;
        }
        
        //felhasználó visszaküldése
        return $user;
    } else {
        return false;
    }
}


//felhasználó hírlevélstátuszának módosítása
function ToggleNewsletterSub($userId, $newStatus){
    global $conn;

    //frissítés sql
    $query = "UPDATE `users` SET `users`.`newsletter_sub` = $newStatus WHERE `users`.`id` = $userId";
    if($conn->query($query)){
        //log bejegyzés létrehozása
        $query = "INSERT INTO `logs` (`user_id`, `type`, `ip`) VALUES ($userId, 'adminNewsletterUpdate', '" . $_SERVER['REMOTE_ADDR'] . "')";
        $conn->query($query);

        $query = "SELECT `users`.`email` FROM `users` WHERE `users`.`id` = $userId";
        $email = $conn->query($query)->fetch_assoc()['email'];

        $body = GenerateToggleNewsletter($newStatus);
        sendMail("no-reply@csipcsiripp.hu", $email, "Hírlevélállapot változás", $body);
        return true;
    } else {
        return false;
    }
}

function GetUserLogs($userId){
    global $conn, $logsTranslation;

    $logs = array();

    //naplóbejegyzések lekérdezése
    $query = "SELECT `logs`.`type`, `logs`.`ip`, `logs`.`timestamp` FROM `logs` WHERE `logs`.`user_id` = $userId ORDER BY `logs`.`timestamp` DESC";
    $response = $conn->query($query);

    if($response->num_rows != 0){
        while($record = $response->fetch_assoc()){
            if(array_key_exists($record['type'], $logsTranslation)){
                $record['type'] = $logsTranslation[$record['type']];
            }
            $logs[] = $record;
        }
    } else {
        $logs = false;
    }
    
    //felhasználó visszaküldése
    return $logs;
}


//admin jóváhagyja a felhasználó emial címét
function ValidateUserEmail($userId){
    global $conn;

    //email validálása
    $query = "UPDATE `users` SET `users`.`valid_email` = true WHERE `users`.`id` = $userId";
    if($conn->query($query)){
        //naplóbejegyzés beírása
        $query = "INSERT INTO `logs` (`user_id`, `type`, `ip`) VALUES ($userId, 'adminValidateEmail', '" . $_SERVER["REMOTE_ADDR"] . "')";
        $conn->query($query);

        $query = "DELETE FROM `validation_codes` where (`validation_codes`.`user_id` = $userId AND `validation_codes`.`type` = 'email')";
        $conn->query($query);
        RemoveOlderValidationCodes();

        $query = "SELECT `users`.`email` FROM `users` WHERE `users`.`id` = $userId";
        $email = $conn->query($query)->fetch_assoc()['email'];

        $body = GenerateAdminValidateUserEmail();
        sendMail("no-reply@csipcsiripp.hu", $email, "Email megerősítve", $body);

        return true;
    } else{
        return false;
    }

}


//email ellenőrző kód újraküldése
function SendNewEmailCode($userId){
    global $conn;

    //naplóbejegyzés beírása
    $query = "INSERT INTO `logs` (`user_id`, `type`, `ip`) VALUES ($userId, 'adminSendEmailCode', '" . $_SERVER["REMOTE_ADDR"] . "')";
    $conn->query($query);

    $query = "DELETE FROM `validation_codes` where (`validation_codes`.`user_id` = $userId AND `validation_codes`.`type` = 'email')";
    $conn->query($query);
    RemoveOlderValidationCodes();

    //új kód beillesztése
    $code = str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);

    $query = "INSERT INTO `validation_codes` (`user_id`, `validation_data`, `type`) VALUES (" . $_SESSION['userId'] .", '$code', 'email')";
    $conn->query($query);
    
    //email kiválasztása
    $query = "SELECT `users`.`email` FROM `users` WHERE `users`.`id` = $userId";
    $email = $conn->query($query)->fetch_assoc()['email'];    
    
    $body = GenerateEmailVerification($code);
    sendMail("no-reply@csipcsiripp.hu", $email, "Email megerősító kód", $body);
    return true;
}


//dobozok adatainak lekérése
function GetBoxes(){
    global $conn;
    
    $boxes = array();

    //tananyagok lekérdezése
    $query = "SELECT `boxes`.`id`, `boxes`.`name`, `images`.`url` AS 'image_url', `images`.`title`AS 'image_title' FROM `boxes` INNER JOIN (`box_image` INNER JOIN `images` ON `images`.`id` = `box_image`.`image_id`) ON `box_image`.`box_id` = `boxes`.`id` WHERE `images`.`type` = 'cover';";
    $response = $conn->query($query);

    while($record = $response->fetch_assoc()){
        $boxes[] = $record;
    }
    
    return $boxes;
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