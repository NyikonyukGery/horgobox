<?php
    require_once("setup.php");
    CheckSession();

    require_once(ROOT_PATH . "/app/database/databaseManager.php");

    $requiredPermissions = array("ACCESS_USERS");
    CheckUserPermissions($requiredPermissions);

    $users = GetUsers(0, 50);
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
    <title>Felhasználók | Csipcsirip - Horgobox</title>
</head>

<body>
    <?php require(ROOT_PATH . "/app/includes/navigation.php") ?>

    <main>
        <div>
            <h1>Felhasználók</h1>
        </div>

        <div class="filters">
            <div>
                <input type="text" name="q" id="search" placeholder="Kereső">
                <button type="button" onclick="SearchUsers()"><i class="fa-solid fa-magnifying-glass"></i> Keresés</button>
            </div>
        </div>

        <div class="select-page">
            <button class="text-like" id="left-arrow"><i class="fa-solid fa-arrow-left"></i></button>
            <div class="pages" id="pages">
            </div>
            <button class="text-like" id="right-arrow"><i class="fa-solid fa-arrow-right"></i></button>
        </div>

        <div>
            <table class="users">
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Név</td>
                        <td>Felhasználónév</td>
                        <td>Email</td>
                        <td>Regisztráció</td>
                    </tr>
                </thead>
                <tbody id="users">
                </tbody>
            </table>
        </div>
    </main>

    <script src="<?php echo(BASE_URL); ?>/assets/scripts/users.js"></script>
</body>

</html>