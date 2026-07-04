<?php
include 'koneksi.php';
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}
$id_user = $_SESSION['id'];
$id_buku = intval($_GET['id']);

// Cek udah ada di keranjang belum
$cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_user=$id_user AND id_buku=$id_buku");
if(mysqli_num_rows($cek) > 0){
    mysqli_query($conn, "UPDATE keranjang SET jumlah=jumlah+1 WHERE id_user=$id_user AND id_buku=$id_buku");
} else {
    mysqli_query($conn, "INSERT INTO keranjang (id_user, id_buku, jumlah) VALUES ($id_user, $id_buku, 1)");
}
header("Location: keranjang.php");
exit;
?>