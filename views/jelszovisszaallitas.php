<?php
    require_once("setup.php");
    require_once(ROOT_PATH . "/app/database/databaseManager.php");

    $userId = $_GET['user'];
    $token = $_GET['token'];

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

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo(BASE_URL); ?>/assets/images/static/favicon.ico">

    <!-- style -->
    <link rel="stylesheet" href="<?php echo(BASE_URL); ?>/assets/css/style.css">
    <!-- title -->
    <title>Jelszó visszaállítása | Csipcsirip - Horgobox</title>
</head>

<body>
    <main class="center">
        <div class="login-container">
            <div class="logo-container">
                <img src="<?php echo(BASE_URL); ?>/assets/images/static/csipcsiripp_logo.png" alt="csipcsiripp logo">
            </div>
            <div class="inputs-container">
                <div class="text-container">
                    <h3>Jelszó visszaállítása</h3>
                    <p>Kérjük adja meg új jelszavát!</p>
                </div>
                <div>
                    <input type="password" name="password" id="password" placeholder="Új jelszó">
                </div>
            </div>
            <div class="button-container">
                <button type="button" onclick="SendNewPassword()">Jelszó frissítése</button>
            </div>
        </div>
    </main>


    <div class="message-popup-container white" id="message-popup-container">
        
    </div>

    <script src="<?php echo(BASE_URL); ?>/assets/scripts/passwordReset.js"></script>
    <script src="<?php echo(BASE_URL); ?>/assets/scripts/popup.js"></script>
    <script>
        const user = "<?php echo($userId); ?>";
        const token = "<?php echo($token); ?>";
    </script>
</body>

</html>