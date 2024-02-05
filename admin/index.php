<?php
$request = explode('.', $_SERVER['REQUEST_URI'])[0];

$sub = "/admin";

switch ($request) {
    case $sub . "/vezerlopult":
    case $sub . "/";
    case $sub . "/statisztikak";
        require __DIR__ . "/views/vezerlopult.php";
        break;
    case $sub . "/bejelentkezes":
        require __DIR__ . "/views/bejelentkezes.php";
        break;
    case $sub . "/profil":
        require __DIR__ . "/views/felhasznalo.php";
        break;
    case $sub . "/logout":
        require __DIR__ . "/logout.php";
        break;
    case $sub . "/vasarlok":
        require __DIR__ . "/views/vasarlok.php";
        break;
    case $sub . "/tananyagok":
        require __DIR__ . "/views/tananyagok.php";
        break;
    default:
        require __DIR__ . "/views/404.php";
        break;
}