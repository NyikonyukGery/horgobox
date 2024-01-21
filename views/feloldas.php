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

    <!-- style -->
    <link rel="stylesheet" href="<?php echo(BASE_URL); ?>/assets/css/style.css">
    <!-- title -->
    <title>Mintafeloldás | Csipcsirip - Horgobox</title>
</head>

<body class="full-page">
    <img src="<?php echo(BASE_URL); ?>/assets/images/unlock/csipcsipbox.jpg" alt="no-box-cover.jpg" class="background-image-unlock-page" id="box-cover">
    <main class="bottom-right">
        <div class="login-container">
            <div class="inputs-container">
                <div class="text-container">
                    <h3>Mintafeloldás</h3>
                    <p>CsipCsip a Csibe</p>
                </div>
                <div>
                    <input type="password" name="password" id="boxPassword" placeholder="Jelszó">
                </div>
            </div>
            <div class="button-container">
                <!-- <button type="button" onclick="UnlockPattern()">Minta feloldása</button> -->
                <button type="button" onclick="UnlockBox()">Minta feloldása</button>
                <a class="small" href="/">Vissza a főoldalra</a>
            </div>
        </div>
    </main>

    <div class="message-popup-container white" id="message-popup-container">
        
    </div>

    <script src="<?php echo(BASE_URL); ?>/assets/scripts/boxes.js"></script>
    <script>
        var boxName = "<?php echo(explode("/", $request)[2]) ?>";
        GetBox();
    </script>
</body>

</html>