<?php

function GenerateForgotPassword($token, $userId){
    $message = '
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.5">
        <title>Elfelejtett jelszó</title>

        <style>
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                color: black;
            }
            table{
                border: 5px solid #f4c430;
                border-collapse: collapse;
                width: 100%;
                max-width: 650px;
            }
            td{
                padding: 0 10px;
            }
            .btn {
                background-color: #f4c430;
                padding: 10px 30px;
                border-radius: 7px;
                outline: solid 2px #f4c430;
                text-align: center;
                display: block;
                color: black !important;
                width: fit-content;
                margin: 15px 0;
                text-decoration: none;
            }
            .primary-background{
                background-color: #f4c430;
            }
            .logo{
                height: 80px;
                width: auto;
                padding: 5px 0;
            }
            .footer{
                padding: 10px 0;
                text-align: center;
            }
            .footer>td>h3{
                padding: 10px 0;
            }
        </style>
    </head>
    <body>
        <table align="center">
            <tr class="primary-background">
                <td><img src="' . BASE_URL . '/assets/images/static/csipcsiripp_logo.png" alt="csipcsiripp_logo" class="logo"></td>
            </tr>
            <tr>
                <td>
                <h1>Elfelejtett jelszó</h1>
                <p>Kérlek kattints az alábbi gombra új jelszavad megadásához.</p>
                </td>
            </tr>
            <tr align="center">
                <a href="' . BASE_URL .'jelszo-visszaallitas?token=' . $token . '&user=' . $userId . '" class="btn">Jelszó visszaállítása</a>
            </tr>
            <tr>
                <td>Amennyiben nem te kérted a helyreállítást, kérlek hagyd filgyelmen kívül az emailt.</td>
            </tr>
            <tr class="primary-background footer">
                <td><h3>Horgobox</h3></td>
            </tr>
        </table>
    </body>
    </html>
    ';
    
    return $message;
}

function GenerateForgotPasswordSuccess(){
    $message = '
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.5">
        <title>Email megerősítő kód</title>

        <style>
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                color: black;
            }
            table{
                border: 5px solid #f4c430;
                border-collapse: collapse;
                width: 100%;
                max-width: 650px;
            }
            td{
                padding: 0 10px;
            }
            h2{
                text-align: center;
                padding: 10px 0;
            }
            .primary-background{
                background-color: #f4c430;
            }
            .logo{
                height: 80px;
                width: auto;
                padding: 5px 0;
            }
            .footer{
                padding: 10px 0;
                text-align: center;
            }
            .footer>td>h3{
                padding: 10px 0;
            }
        </style>
    </head>
    <body>
        <table align="center">
            <tr class="primary-background">
                <td><img src="' . BASE_URL . '/assets/images/static/csipcsiripp_logo.png" alt="csipcsiripp_logo" class="logo"></td>
            </tr>
            <tr>
                <td><h1>Sikeres jelszóváltoztatás</h1></td>
            </tr>
            <tr><h2>Sikeresen frissítetted felhaszálód jelszavát</h2></tr>
            <tr class="primary-background footer">
                <td><h3>Horgobox</h3></td>
            </tr>
        </table>
    </body>
    </html>
    ';
    
    return $message;
}

function GenerateEmailVerification($code){
    $message = '
    <!DOCTYPE html>
    <html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.5">
        <title>Email megerősítő kód</title>

        <style>
            *{
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                color: black;
            }
            table{
                border: 5px solid #f4c430;
                border-collapse: collapse;
                width: 100%;
                max-width: 650px;
            }
            td{
                padding: 0 10px;
            }
            h2{
                text-align: center;
                padding: 10px 0;
            }
            .primary-background{
                background-color: #f4c430;
            }
            .logo{
                height: 80px;
                width: auto;
                padding: 5px 0;
            }
            .footer{
                padding: 10px 0;
                text-align: center;
            }
            .footer>td>h3{
                padding: 10px 0;
            }
        </style>
    </head>
    <body>
        <table align="center">
            <tr class="primary-background">
                <td><img src="' . BASE_URL . '/assets/images/static/csipcsiripp_logo.png" alt="csipcsiripp_logo" class="logo"></td>
            </tr>
            <tr>
                <td>
                <h1>Email megerősítő kód</h1>
                <p>A visszaigazoláshoz szükséges kód:</p>
                </td>
            </tr>
            <tr align="center">
                <h2>' . $code . '</h2>
            </tr>
            <tr></tr>
            <tr class="primary-background footer">
                <td><h3>Köszönöm a regisztrációd! - Bazsi</h3></td>
            </tr>
        </table>
    </body>
    </html>
    ';
    
    return $message;
}
?>