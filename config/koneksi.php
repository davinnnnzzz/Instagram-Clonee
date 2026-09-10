<?php

// Koneksi database untuk aplikasi Instagram Clone
$localhost = "localhost";
$username  = "root";
$password  = "";
$db        = "instagram_clone";

// Menggunakan mysqli_connect (ditambah huruf 'i')
/* koneksi ke database */
$koneksi = mysqli_connect($localhost, $username, $password, $db);

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, 'utf8mb4');

// echo "Koneksi Berhasil";

?>