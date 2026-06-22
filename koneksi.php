<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "tamz_bookstore";
$conn = mysqli_connect($host, $user, $pass, $db);
if(!$conn){ die("Koneksi gagal: ".mysqli_connect_error()); }
define('NAMA_TOKO', 'TAMz Bookstore');
session_start();
?>