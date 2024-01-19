<?php require_once("setup.php"); ?>

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
    <title>Regisztráció | Csipcsirip - Horgobox</title>
</head>

<body>
    <main class="center">
        <div class="login-container">
            <div class="logo-container">
                <img src="./assets/images/static/csipcsiripp_logo.png" alt="csipcsiripp logo">
            </div>
            <div class="inputs-container">
                <div class="text-container">
                    <h3>Regisztráció</h3>
                </div>
                <div>
                    <input type="text" name="familyName" id="familyName" placeholder="Vezetéknév">
                </div>
                <div>
                    <input type="text" name="firstName" id="firstName" placeholder="Keresztnév">
                </div>
                <div>
                    <input type="text" name="username" id="username" placeholder="Felhasználónév">
                </div>
                <div>
                    <input type="email" name="email" id="email" placeholder="Email cím">
                </div>
                <div>
                    <input type="password" name="password" id="password" placeholder="Jelszó">
                </div>
                <div class="checkbox">
                    <div>
                        <input type="checkbox" id="newsletterSub" checked>
                        <label for="newsletterSub">Feliratkozom a hírlevélre</label>
                    </div>
                    <div>
                        <input type="checkbox" id="acceptConditions">
                        <label for="acceptConditions">Elolvastam és elfogadom az <a href="./resources/aszf.pdf" target="blank">ÁSZF-ben</a> foglaltakat!</label>
                    </div>
                </div>
                <div class="error-container" id="error-container">
                    <p id="error-message">Hibás felhaszánlónév vagy jelszó!</p>
                </div>
            </div>
            <div class="otheroption">
                <p>Már van felhasználód?</p>
                <a href="<?php echo(BASE_URL . "bejelentkezes.php"); ?>">Bejelentkezés</a>
            </div>
            <div class="button-container">
                <button type="button" onclick="RegisterUser()">Regisztráció</button>
            </div>
        </div>
    </main>


    <script src="./assets/scripts/authorization.js"></script>
    <script>
        addEventListener("keyup", function(e){
            if(e.code === "Enter"){
                RegisterUser();
            }
        });
    </script>
</body>
</html>