<?php
    require_once("./setup.php");
    CheckSession();
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
    <title>Mintáim | Csipcsirip - Horgobox</title>
</head>

<body>
    <?php require(ROOT_PATH . "/app/includes/navigation.php") ?>

    <main>
        <h1>Mintáim</h1>

        <div class="patterns-container">
            <div class="pattern">
                <div class="img-container">
                    <img src="./assets/images/patterns/renszarvas.jpg" alt="horgolt rénszarvas">
                </div>
                <div class="description">
                    <h2>Rénszarvas</h2>
                    <a href="./assets/minták/renszarvas.html" class="btn">Megnézem</a>
                </div>
            </div>

            <div class="pattern">
                <div class="img-container">
                    <img src="./assets/images/patterns/stitch.jpg" alt="horgolt stitch">
                </div>
                <div class="description">
                    <h2>Sticth</h2>
                    <a href="./assets/minták/renszarvas.html" class="btn">Megnézem</a>
                </div>
            </div>

            <div class="pattern">
                <div class="img-container">
                    <img src="./assets/images/patterns/minipingvin.jpg" alt="horgolt mini pingvin">
                </div>
                <div class="description">
                    <h2>Mini pingvin</h2>
                    <a href="./assets/minták/renszarvas.html" class="btn">Megnézem</a>
                </div>
            </div>

            <div class="pattern">
                <div class="img-container">
                    <img src="./assets/images/patterns/pok.jpg" alt="horgolt pók">
                </div>
                <div class="description">
                    <h2>Pók</h2>
                    <a href="./assets/minták/renszarvas.html" class="btn">Megnézem</a>
                </div>
            </div>

            <div class="pattern">
                <div class="img-container">
                    <img src="./assets/images/patterns/csibe.jpg" alt="horgolt csibe">
                </div>
                <div class="description">
                    <h2>Csibe</h2>
                    <a href="./assets/minták/renszarvas.html" class="btn">Megnézem</a>
                </div>
            </div>

            <div class="pattern">
                <div class="img-container">
                    <img src="./assets/images/patterns/mehecske.jpg" alt="horgolt méhecske">
                </div>
                <div class="description">
                    <h2>Méhecske</h2>
                    <a href="./assets/minták/renszarvas.html" class="btn">Megnézem</a>
                </div>
            </div>

            <div class="pattern">
                <div class="img-container">
                    <img src="./assets/images/patterns/yoda.jpg" alt="horgolt yoda">
                </div>
                <div class="description">
                    <h2>Yoda</h2>
                    <a href="./assets/minták/renszarvas.html" class="btn">Megnézem</a>
                </div>
            </div>

            <div class="pattern">
                <div class="img-container">
                    <img src="./assets/images/patterns/denever.jpg" alt="horgolt denevér">
                </div>
                <div class="description">
                    <h2>Denevér</h2>
                    <a href="./assets/minták/renszarvas.html" class="btn">Megnézem</a>
                </div>
            </div>
        </div>
    </main>
</body>

</html>