<?php require_once("setup.php"); 

//bejelentkezett felhaszálót automatikusan továbbirányítja a főoldalra
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
    <link rel="stylesheet" href="./assets/css/style.css">
    <!-- title -->
    <title>Bejelentkezés | Csipcsirip - Horgobox</title>
</head>

<body>
    <main class="center">
        <div class="login-container">
            <div class="logo-container">
                <img src="./assets/images/static/csipcsiripp_logo.png" alt="csipcsiripp logo">
            </div>
            <div class="inputs-container">
                <div class="text-container">
                    <h3>Bejelentkezés</h3>
                </div>
                <div>
                    <input type="email" name="email" id="email" placeholder="Email cím">
                </div>
                <div>
                    <input type="password" name="password" id="password" placeholder="Jelszó">
                </div>
                <div class="error-container" id="error-container">
                    <p id="error-message">Hibás felhaszánlónév vagy jelszó!</p>
                </div>
            </div>
            <div class="otheroption">
                <p>Még nincs felhasználód?</p>
                <a href="<?php echo(BASE_URL . "regisztracio.php"); ?>">Regisztráció</a>
            </div>
            <div class="button-container">
                <button type="button" onclick="LoginUser()">Bejelentkezés</button>
            </div>
        </div>
    </main>

    <script src="./assets/scripts/authorization.js"></script>
    <script>
        addEventListener("keyup", function(e){
            if(e.code === "Enter"){
                LoginUser();
            }
        });
    </script>
</body>

</html>