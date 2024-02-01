<?php
    require_once("setup.php");
    require_once(ROOT_PATH . "/app/database/databaseManager.php");

    if(isset($_SESSION['login']) && $_SESSION['login']){
        header("location: " .BASE_URL);
    }
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <!-- meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />

    <!-- style -->
    <link rel="stylesheet" href="<?php echo(BASE_URL); ?>/assets/css/style.css">
    <!-- title -->
    <title>Elfelejtett jelszó | Csipcsirip - Horgobox</title>
</head>

<body>
    <main class="center">
        <div class="login-container">
            <div class="logo-container">
                <img src="<?php echo(BASE_URL); ?>/assets/images/static/csipcsiripp_logo.png" alt="csipcsiripp logo">
            </div>
            <div class="inputs-container">
                <div class="text-container">
                    <h3>Elfelejtett jelszó</h3>
                    <p>Adja meg a felhasználójának email címet, hogy elküldhessük a visszaállítási linket.</p>
                </div>
                <div>
                    <input type="email" name="email" id="email" placeholder="Email cím">
                </div>
            </div>
            <div class="otheroption">
                <div class="normal">
                    <a href="<?php echo(BASE_URL); ?>">Vissza a főoldalra</a>
                </div>
            </div>
            <div class="button-container">
                <button type="button" onclick="SendPasswordReset()">Elküldés</button>
            </div>
        </div>
    </main>


    <div class="message-popup-container" id="message-popup-container">
        
    </div>

    <script src="<?php echo(BASE_URL); ?>/assets/scripts/passwordReset.js"></script>
    <script src="<?php echo(BASE_URL); ?>/assets/scripts/popup.js"></script>
</body>

</html>