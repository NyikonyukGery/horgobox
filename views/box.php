<?php
    require_once("./setup.php");
    CheckSession();

    require_once(ROOT_PATH . "/app/database/databaseManager.php");
    $request = $_SERVER['REQUEST_URI'];

    $boxName = explode("/", $request)[2];
    $boxData = LoadBox($boxName);
    if($boxData == false){
        Redirect();
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
    <title>tananyag | Csipcsirip - Horgobox</title>
</head>

<body>
    <?php require(ROOT_PATH . "/app/includes/navigation.php") ?>

    <main>
        <div class="title">
            <h1><?php echo($boxData["box"][0]["name"]); ?></h1>
            <p><?php echo($boxData["box"][0]["description"]); ?></p>
        </div>
        <div>
            <h1>Videók</h1>
        </div>
        <div class="videos-container">
            <?php
                if(isset($boxData["videos"])){
                    $videos = $boxData["videos"];
                    foreach($videos as $video){
                        echo('
                            <div class="video">
                                <h2>' . $video['title'] . '</h2>
                                <iframe src="' . $video['url'] . '" title="' . $video["title"] . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                        
                        ');
                    }
                } else {
                    echo('<p>Nem állnak rendelkezésre videók :(</p>');
                }
            ?>
        </div>

        <div>
            <h1>Letölthető anyagok</h1>
        </div>
        <div class="offline-resources">
            <?php
                if(isset($boxData["downloads"])){
                    $downloads = $boxData["downloads"];
                    foreach($downloads as $download){
                        echo('
                            <div class="offline-resource">
                                <a href="/resources' . $download["url"] . '" class="btn">' . $download["visible_name"] . '</a>
                            </div>
                        ');
                    }
                } else {
                    echo('<p>Nem állnak rendelkezésre letölthető anyagok :(</p>');
                }

            ?>
        </div>
    </main>
</body>

</html>