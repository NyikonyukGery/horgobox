<?php
    require_once("./setup.php");
    CheckSession();

    require_once(ROOT_PATH . "/app/database/databaseManager.php");
    $unlockedBoxes = GetUserBoxes();
    $lockedBoxes= GetLockedBoxes();
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
    <title>Mintáim | Csipcsirip - Horgobox</title>
</head>

<body>
    <?php require(ROOT_PATH . "/app/includes/navigation.php") ?>

    <main>
        <div>
            <h1>Tananyagjaim</h1>
            <?php if($unlockedBoxes == false){
                echo('<p id="unlocked-boxes-message">Még nincs feloldott tananyagod!</p>');
            }
            ?>
        </div>

        <div id="unlocked-boxes" class="patterns-container">
            <?php 
                if($unlockedBoxes != false){
                    foreach($unlockedBoxes as $box){
                        echo('
                            <div class="pattern">
                                <div class="img-container">
                                    <img src="' . BASE_URL . 'assets/images' .$box['image_url'] . '" alt="' . $box['image_title'] . '">
                                </div>
                                <div class="description">
                                    <h2>' . $box['box_name'] . '</h2>
                                <a href="'. BASE_URL . 'boxok' . $box['box_url'] . '" class="btn">Megnézem</a>
                                </div>
                            </div>
                            ');
                    }
                }
            ?>
        </div>

        <div>
            <h1>Feloldatlan tananyagok</h1>
            <?php if($lockedBoxes == false){
                echo('<p id="futher-boxes-message">Az összes tananyagot feloldottad már! Hamarosan érkeznek újak!</p>');
            }
            ?>
        </div>

        <div class="patterns-container">

            <?php 
                if($lockedBoxes != false){
                    foreach($lockedBoxes as $box){
                        echo('
                            <div class="pattern">
                                <div class="img-container">
                                    <img src="' . BASE_URL . 'assets/images' .$box['image_url'] . '" alt="' . $box['image_title'] . '">
                                </div>
                                <div class="description">
                                    <h2>' . $box['box_name'] . '</h2>
                                <a href="'. BASE_URL . 'aktivalas' . $box['box_url'] . '" class="btn">Aktiválom</a>
                                </div>
                            </div>
                            ');
                    }
                }
            ?>
        </div>
    </main>
</body>

</html>