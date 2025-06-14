<?php
function getConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "halisaha";

    $con = mysqli_connect($servername, $username, $password, $database);

    if (!$con) {
        die("Bağlantı hatası: " . mysqli_connect_error());
    }


    mysqli_set_charset($con, "utf8");

    return $con;
}
?>