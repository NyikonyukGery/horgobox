<?php
    require_once("setup.php");
    CheckSession();

    require_once(ROOT_PATH . "/app/database/databaseManager.php");

    if(!isset($_GET['id']) || $_GET['id'] == null){
        Redirect("felhasznalok");
        exit();
    }

    $requiredPermissions = array("ACCESS_USER");
    CheckUserPermissions($requiredPermissions);

    $user = GetUser(htmlspecialchars($_GET['id']));
    if($user === false){
        Redirect("felhasznalok");
        exit();
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
    <title>Felhasználó | Csipcsirip - Horgobox</title>
</head>

<body>
    <?php require(ROOT_PATH . "/app/includes/navigation.php") ?>

    <main>
        <div>
            <h1>Felhasználó adatlapja</h1>
        </div>

        <div class="operations">
            <?php 
                $requiredPermissions = array("ALL");

                if(CheckUserPermissions($requiredPermissions) && !$user['basic']['valid_email']){
                    echo('<button type="button" onclick="ConfirmEmail()">Email megerősítése</button>');
                }

                if(!$user['basic']['valid_email']){
                    echo('<button type="button" onclick="SendNewEmailCode()">Email megerősító kód küldése</button>');
                }
            ?>
            <button type="button" onclick="SendNewPassword()">Új jelszó küldése</button>
            <button type="button" onclick="SaveChanges()">Mentés</button>
        </div>

        <div class="user-info">
            <h2>Alapadatok</h2>
            <div class="basic">
                <div class="row">
                    <div>
                        <p>Családnév</p>
                        <input type="text" value="<?php echo($user['basic']['familyName']); ?>" id="familyName">
                    </div>
                    <div>
                        <p>Keresztnév</p>
                        <input type="text" value="<?php echo($user['basic']['firstName']); ?>" id="firstName">
                    </div>
                </div>
                <div class="row">
                    <div>
                        <p>Felhasználónév</p>
                        <input type="text" value="<?php echo($user['basic']['username']); ?>" id="username">
                    </div>
                    <div>
                        <p>Email</p>
                        <input type="email" value="<?php echo($user['basic']['email']); ?>" id="email">
                    </div>
                </div>
                <div>
                    <p>Regisztráció ideje: <?php echo($user['basic']['registrationDate']); ?></p>
                    
                    <button type="button" onclick="ToggleNewsletterSub()" id="newsletterSub">
                        <?php
                            if($user['basic']['newsletter_sub']){
                                echo("Leiratkoztatás a hírlevélről");
                            } else{
                                echo("Feliratkoztatás a hírlevélre");
                            }
                        ?>
                    </button>
                </div>
            </div>


            <h2>Feloldott tananyagok</h2>
            <div class="unlocked-patterns">
                <?php
                    if($user['boxes'] != false){
                        foreach($user['boxes'] as $box){
                            echo("<p>$box</p>");
                        }
                    } else{
                        echo("<p>Nincs feloldott tananyag!</p>");
                    }
                ?>
            </div>


            <h2>Tevékenységnapló</h2>
            <div class="log-content"> 
                <?php
                    if($user['logs'] != false){
                        foreach($user['logs'] as $log){
                            echo("<p>" . $log['timestamp'] . " | IP: " . $log['ip'] . " | " . $log['type'] . "</p>");
                        }
                    } else {
                        echo("<p>Nincs tevékenységnapló bejegyzés!</p>");
                    }
                ?>
            </div>

            
        </div>
    </main>

    <script>
        originalUserData = <?php echo(json_encode($user['basic'])) ?>;
        userId = <?php echo(htmlspecialchars($_GET['id'])); ?>
    </script>
    <script src="<?php echo(BASE_URL); ?>/assets/scripts/user.js"></script>
    <script src="<?php echo(BASE_URL); ?>/assets/scripts/popup.js"></script>
</body>

</html>