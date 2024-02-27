<?php require_once("setup.php"); 

//bejelentkezett felhaszálót automatikusan továbbirányítja a főoldalra
if(isset($_SESSION['login']) && $_SESSION['login']){
    header("location: " .BASE_URL);
}

if(isset($_SESSION['adminLogin']) || isset($_SESSION['userId'])){
    session_unset();
    session_destroy();
    session_start();
}

?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <!-- meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo(BASE_URL); ?>/assets/images/static/favicon.ico">

    <!-- style -->
    <link rel="stylesheet" href="<?php echo(BASE_URL); ?>/assets/css/style.css">
    <!-- title -->
    <title>Bejelentkezés | Csipcsirip - Horgobox</title>
</head>

<body>
    <main class="center">
        <div class="login-container">
            <div class="logo-container">
                <img src="<?php echo(BASE_URL); ?>/assets/images/static/csipcsiripp_logo.png" alt="csipcsiripp logo">
            </div>
            <div class="inputs-container">
                <div class="text-container">
                    <h3>Bejelentkezés</h3>
                </div>
                <div>
                    <input type="text" name="email" id="email" placeholder="Email/felhasználónév">
                </div>
                <div>
                    <input type="password" name="password" id="password" placeholder="Jelszó">
                </div>
                <div class="error-container" id="error-container">
                    <p id="error-message">Hibás felhaszánlónév vagy jelszó!</p>
                </div>
            </div>
            <div class="otheroption">
                <div>
                    <p>Még nincs felhasználód?</p>
                    <a href="<?php echo(BASE_URL . "regisztracio"); ?>">Regisztráció</a>
                </div>
                <div class="normal">
                    <a href="<?php echo(BASE_URL . "elfelejtett-jelszo"); ?>">Elfelejtett jelszó</a>
                </div>
            </div>
            <div class="button-container">
                <button type="button" onclick="LoginUser()">Bejelentkezés</button>
            </div>
        </div>
    </main>

    <?php 
    // if(!isset($_COOKIE['HIDELOGINPOPUP'])){
    //     echo('
    //         <div class="popup-container" id="popup-container">
    //             <div class="warning-popup">
    //                 <h1>FIGYELEM!</h1>
    //                 <p>A belépéshez külön regisztráció szükséges! Kérjük amennyiben nem tette még, regisztráljon!<br>Köszönjük megértését!</p>
    //                 <button type="button" onclick="HidePopup()" class="top-right">X</button>
    //                 <button type="button" onclick="HidePopupForever()">Nem szeretném többet látni</button>
    //             </div>
    //         </div>
    //     ');
    // }
    ?>

    <script src="<?php echo(BASE_URL); ?>/assets/scripts/authorization.js"></script>
    <script>
        addEventListener("keyup", function(e){
            if(e.code === "Enter"){
                LoginUser();
            }
        });
    </script>
</body>

</html>