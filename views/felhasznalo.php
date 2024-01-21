<?php
    require_once("./setup.php");
    CheckSession();
?>

<!DOCTYPE html>
<html lang="hu">

<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="" />

    <!-- style -->
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- title -->
    <title>Fiókom | Csipcsirip - Horgobox</title>
</head>

<body>
    <?php require(ROOT_PATH . "/app/includes/navigation.php") ?>

    <main>
        <h1>Fiókom</h1>

        <section>
            <h2>Általános adatok</h2>
            <div class="user-detail-container">
                <div>
                    <label for="familyName">Vezetéknév</label>
                    <input type="text" id="familyName">
                </div>
                <div>
                    <label for="firstName">Keresztnév</label>
                    <input type="text" id="firstName">
                </div>
                <div>
                    <label for="username">Felhasználónév</label>
                    <input type="text" id="username">
                </div>
                <div class="fix">
                    <label for="email">Email</label>
                    <p id="email">user@user.hu</p>
                </div>
                <div>
                    <label for="password">Jelszó</label>
                    <input type="password" id="password">
                </div>

                <div class="button-container">
                    <button type="button" id="update-button" onclick="UpdateUser()">Változtatások mentése</button>
                </div>
            </div>
        </section>
        
        <section>
            <div class="user-detail-container">
                <h2>Hírlevél</h2>
                <div id="newsletter-action-container" class="button-container">
                    <button onclick="NewsletterSignUp()">Feliratkozás a hírlevélre</button>
                </div>
            </div>
        </section>
    </main>

    <div class="message-popup-container" id="message-popup-container">
        
    </div>

    <script src="./assets/scripts/profile.js"></script>
    <script>
        loadUser();
    </script>
</body>

</html>