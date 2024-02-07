<?php
if (basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"])) {
    http_response_code(403);
    exit();
}

$conn = mysqli_connect($sqlServer, $sqlUser, $sqlPassword, $sqlDatabase);
if ($conn->connect_error) {
    // die("Connection failed: " . $conn->connect_error);
    die("Connection failed");
}
$conn->set_charset("utf8");