<?php
    require_once("setup.php");
    CheckSession();

    require_once(ROOT_PATH . "/app/database/databaseManager.php");

    $requiredPermissions = array("ACCESS_ADMIN_DASHBOARD");
    CheckUserPermissions($requiredPermissions);

    $users = GetUsers();
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
            <h1>Vásárlók</h1>
        </div>

        <div>
            <table>
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Név</td>
                        <td>Felhasználónév</td>
                        <td>Email</td>
                        <td>Regisztráció</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $counter = 1;
                        foreach($users as $user){
                            echo("
                                <tr onclick='location.href=" . BASE_URL . "vasarlo?id=" . $user['id'] . "'>
                                    <td>$counter</td>
                                    <td>" . $user['familyName'] . " " . $user['firstName'] . "</td>
                                    <td>" . $user['username'] . "</td>
                                    <td>" . $user['email'] . "</td>
                                    <td>" . $user['registrationDate'] . "</td>
                                </tr>
                            ");
                            $counter++;
                        }
                    ?>
                </tbody>
            </table>
        </div>

        <div>
            
        </div>

        <div>

        </div>
    </main>
</body>

</html>