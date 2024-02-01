<?php

$conn = mysqli_connect($sqlServer, $sqlUser, $sqlPassword, $sqlDatabase);
if ($conn->connect_error) {
    // die("Connection failed: " . $conn->connect_error);
    die("Connection failed");
}
$conn->set_charset("utf8");