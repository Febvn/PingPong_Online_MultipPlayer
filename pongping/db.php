<?php
$host = "sql207.infinityfree.com";
$user = "if0_39980568"; // InfinityFree DB user
$pass = "80ar0BRAyJRu3";     // InfinityFree DB password
$db   = "if0_39980568_pingpong_pro";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
