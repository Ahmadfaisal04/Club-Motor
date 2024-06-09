<?php
$server = "localhost";
$user = "root";
$password = "";
$database = "users";

$db = mysqli_connect($server, $user, $password, $database);

if (!$db) {
    die("Gagal terhubung dengan database: " . mysqli_connect_error());
} else {
    echo "Koneksi berhasil";
}
?>