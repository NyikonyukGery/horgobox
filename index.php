<?php
$request = $_SERVER['REQUEST_URI'];


switch ($request) {
	case "/felhasznalo":
        require __DIR__ . '/views/felhasznalo.php';
        break;
    case "/logout":
        require __DIR__ . "/logout.php";
        break;
    case "/bejelentkezes":
        require __DIR__ . "/views/bejelentkezes.php";
        break;
    case "/regisztracio":
        require __DIR__ . "/views/regisztracio.php";
        break;
    case "/":
        require __DIR__ . "/views/fooldal.php";
        break;
    case (preg_match("/\/aktivalas\/.*/", $request) ? true : false):
        require __DIR__ . "/views/aktivalas.php";
        break;
    default:
        require __DIR__ . "/views/404.php";
        break;   
}