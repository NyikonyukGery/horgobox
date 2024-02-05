<?php
    require_once("./setup.php");
    CheckSession();

    $request = $_SERVER['REQUEST_URI'];
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
    <title>aktiválás | Csipcsirip - Horgobox</title>
</head>

<body class="full-page center">
    <div class="unlock-container">
        <div class="img-container">
            <img src="<?php echo(BASE_URL); ?>/assets/images/unlock/horgobox.png" alt="horgobox.jpg" class="background-image-unlock-page" id="box-cover">
        </div>

        <div class="login-container">
            <div class="inputs-container">
                <div class="text-container">
                    <h3>Mintafeloldás</h3>
                    <p id="box-name"></p>
                </div>
                <div>
                    <input type="text" name="password" id="boxPassword" placeholder="Jelszó"  style="text-transform:uppercase">
                </div>
            </div>
            <div class="button-container">
                <button type="button" onclick="UnlockBox()">Minta feloldása</button>
                <a class="small" href="/">Vissza a főoldalra</a>
            </div>
            <div class="otheroption">
                <div>
                    <p>Megvásárolnád?</p>
                    <a href="#" id="webshop-link">Irány a webshop!</a>
                </div>
            </div>
        </div>
    </div>
    <div class="message-popup-container white" id="message-popup-container">
    </div>
            
    <script src="<?php echo(BASE_URL); ?>/assets/scripts/boxes.js"></script>
    <script src="<?php echo(BASE_URL); ?>/assets/scripts/popup.js"></script>
    <script>
        var boxName = "/<?php echo(explode("/", $request)[2]) ?>";
        GetBox();
    </script>
</body>

</html>