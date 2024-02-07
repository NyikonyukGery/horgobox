<?php
    require_once("setup.php");
    require_once(ROOT_PATH . "/app/database/databaseManager.php");

    if(isset($_SESSION['login']) && $_SESSION['login'] && EmailConfirmed()){
        header("location: " .BASE_URL);
    } else if(!isset($_SESSION['userId'])){
        header("location: " . BASE_URL . "bejelentkezes");
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
    <title>Email megerősítése | Csipcsirip - Horgobox</title>
</head>

<body>
    <main class="center">
        <div class="login-container">
            <div class="logo-container">
                <img src="<?php echo(BASE_URL); ?>/assets/images/static/csipcsiripp_logo.png" alt="csipcsiripp logo">
            </div>
            <div class="inputs-container">
                <div class="text-container">
                    <h3>Email megerősítése</h3>
                    <p>Elküldtük a regisztrációkor megadott email címre a kódot! (ellenőrizd a spam mappát is)</p>
                </div>
                <div>
                    <input type="text" name="code" id="code" placeholder="Megerősítő kód">
                </div>
                <div class="error-container" id="error-container">
                    <p id="error-message">Hibás biztonsági kód! Elküldtük az újat!</p>
                </div>
            </div>
            <div class="otheroption">
                <div>
                    <p>Nem kaptad meg?</p>
                    <a href="#" onclick="ResendEmail()">Kód újraküldése</a>
                </div>
            </div>
            <div class="button-container">
                <button type="button" onclick="SubmitCode()">Megerősítés</button>
            </div>
        </div>
    </main>


    <div class="message-popup-container white" id="message-popup-container">
        
    </div>

    <script src="<?php echo(BASE_URL); ?>/assets/scripts/confirmEmail.js"></script>
    <script src="<?php echo(BASE_URL); ?>/assets/scripts/popup.js"></script>
</body>

</html>