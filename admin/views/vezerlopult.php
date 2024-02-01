<?php
    require_once("setup.php");
    CheckSession();

    require_once(ROOT_PATH . "/app/database/databaseManager.php");

    $requiredPermissions = array("ACCESS_ADMIN_DASHBOARD");
    CheckUserPermissions($requiredPermissions);

    $statistics = GetStatistics();
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
    <title>Vezérlőpult | Csipcsirip - Horgobox</title>
</head>

<body>
    <?php require(ROOT_PATH . "/app/includes/navigation.php") ?>

    <main>
        <div>
            <h1>Statisztikák</h1>
        </div>

        <div>
            <p>Regisztrált felhasználók: <?php echo($statistics['registeredUsers']); ?></p>
            <p>Feloldott tananyagok: <?php echo($statistics['unlockedPatterns']); ?></p>
            <p>Hírlevél feliratkozások: <?php echo($statistics['newsletterSignUp']); ?></p>
        </div>

        <div>
            
        </div>

        <div>

        </div>
    </main>
</body>

</html>