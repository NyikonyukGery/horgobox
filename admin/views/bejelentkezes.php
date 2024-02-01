<?php require_once("setup.php"); 

// bejelentkezett felhaszálót automatikusan továbbirányítja a főoldalra
if(isset($_SESSION['adminLogin']) && $_SESSION['adminLogin']){
    header("location: " .BASE_URL);
}

if(isset($_SESSION['login'])){
    unset($_SESSION['login']);
    unset($_SESSION['userId']);
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
    <title>Bejelentkezés | Csipcsirip - Horgobox</title>
</head>

<body>
    <main class="center">
        <div class="login-container">
            <div class="logo-container">
                <img src="<?php echo(BASE_URL); ?>/assets/images/static/csipcsiripp_logo.png" alt="csipcsiripp logo">
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
                <div class="normal">
                    <a href="<?php echo(BASE_URL . "elfelejtett-jelszo"); ?>">Elfelejtett jelszó</a>
                </div>
            </div>
            <div class="button-container">
                <button type="button" onclick="LoginUser()">Bejelentkezés</button>
            </div>
        </div>
    </main>

    <script src="<?php echo(BASE_URL); ?>/assets/scripts/authorization.js"></script>
    <script>
        addEventListener("keyup", function(e){
            if(e.code === "Enter"){
                LoginUser();
            }
        });
    </script>
</body>

</html>