<?php
    require_once("./setup.php");
    CheckSession();

    $request = $_SERVER['REQUEST_URI'];
    echo($request);

    // switch($request) {
        
    // }
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <!-- meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />

    <!-- style -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- title -->
    <title>Mintafeloldás | Csipcsirip - Horgobox</title>
</head>

<body class="full-page">
    <img src="./assets/images/unlock/csipcsipbox.jpg" alt="csipcsipbox.jpg" class="background-image-unlock-page">
    <main class="bottom-right">
        <div class="login-container">
            <div class="inputs-container">
                <div class="text-container">
                    <h3>Mintafeloldás</h3>
                    <p>CsipCsip a Csibe</p>
                </div>
                <div>
                    <input type="password" name="password" id="patternPassword" placeholder="Jelszó">
                </div>
            </div>
            <div class="button-container">
                <button type="button" onclick="UnlockPattern()">Minta feloldása</button>
                <a class="small" href="index.html">Vissza a főoldalra</a>
            </div>
        </div>
    </main>

    <div class="message-popup-container white" id="message-popup-container">
        
    </div>

    <script src="./assets/scripts/boxes.js"></script>
</body>

</html>