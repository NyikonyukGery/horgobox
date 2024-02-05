<?php
    require_once("setup.php");
    CheckSession();

    require_once(ROOT_PATH . "/app/database/databaseManager.php");

    $requiredPermissions = array("ACCESS_BOXES");
    CheckUserPermissions($requiredPermissions);

    $boxes = GetBoxes();
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
    <title>Tananyagok | Csipcsirip - Horgobox</title>
</head>

<body>
    <?php require(ROOT_PATH . "/app/includes/navigation.php") ?>

    <main>
        <div>
            <h1>Tananyagok</h1>
        </div>

        <div>
            <table>
                <thead>
                    <tr>
                        <td>#</td>
                        <td>Borító</td>
                        <td>Név</td>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
                        $counter = 1;
                        foreach($boxes as $box){
                            echo("
                                <tr onclick='location.href=" . BASE_URL . "box?id=" . $box['id'] . "'>
                                    <td>$counter</td>
                                    <td><img src='" . ASSETS_URL . "images" . $box['image_url'] . "' alt='" . $box['image_title'] . "'></td>
                                    <td>" . $box['name'] . "</td>
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