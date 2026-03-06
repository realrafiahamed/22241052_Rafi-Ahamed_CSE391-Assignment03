<?php
if ($_SERVER['SERVER_NAME'] == "localhost") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "workshop";
} else {
    $servername = "sql102.infinityfree.com";
    $username = "if0_41287606";
    $password = "4eJsK5fyivU3J6";
    $dbname = "if0_41287606_workshop";
}

$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
