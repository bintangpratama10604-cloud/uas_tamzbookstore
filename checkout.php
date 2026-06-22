<?php 
include 'koneksi.php';
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}
$id_user = $_SESSION['id'];

$keranjang = mysqli_query($conn, "SELECT k.*, b.judul, b.harga, b.stok FROM keranjang k JOIN buku b ON k.id_buku=b.id WHERE k.id_user=$id_user");
if(mysqli_num_rows($keranjang) == 0){
    header("Location: keranjang.php");
    exit;
}

$total = 0;
$items = [];
while($k = mysqli_fetch_assoc($keranjang)){
    $total += $k['harga'] * $k['jumlah'];
    $items[] = $k;
}

if(isset($_POST['bayar'])){
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $metode = $_POST['metode_bayar'];
    $kode_trx = 'TMZ'.date('YmdHis').rand(100,999);
    
    foreach($items as $item){
        if($item['jumlah'] > $item['stok']){
            die("Stok {$item['judul']} tidak cukup! Sisa: {$item['stok']}");
        }
    }
    
    mysqli_query($conn, "INSERT INTO transaksi (kode_trx, id_user, total_harga, nama_penerima, alamat, no_hp, metode_bayar) VALUES ('$kode_trx', $id_user, $total, '$nama', '$alamat', '$no_hp', '$metode')");
    $id_trx = mysqli_insert_id($conn);
    
    foreach($items as $item){
        mysqli_query($conn, "INSERT INTO detail_transaksi (id_transaksi, id_buku, jumlah, harga) VALUES ($id_trx, {$item['id_buku']}, {$item['jumlah']}, {$item['harga']})");
        mysqli_query($conn, "UPDATE buku SET stok=stok-{$item['jumlah']} WHERE id={$item['id_buku']}");
    }
    
    mysqli_query($conn, "DELETE FROM keranjang WHERE id_user=$id_user");
    header("Location: riwayat.php?sukses=$kode_trx");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Checkout - <?= NAMA_TOKO ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --beige:#F5F1E6; --navy:#1E2A5E; --orange:#FF6B35; --white:#FFFFFF; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:var(--beige); color:var(--navy); }
.container { max-width:1000px; margin:40px auto; padding:0 20px; display:grid; grid-template-columns:2fr 1fr; gap:30px; }
.btn-orange { background:var(--orange); color:var(--white); border:none; padding:12px 25px; border-radius:25px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; width:100%; font-size:16px; }
.card { background:var(--white); border-radius:15px; padding:30px; box-shadow:0 5px 15px rgba(0,0,0,0.08); }
h2 { margin-bottom:20px; }
.form-group { margin-bottom:15px; }
.form-group label { display:block; margin-bottom:5px; font-weight:500; }
.form-group input, .form-group textarea, .form-group select { width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; }
.radio-group { display:flex; flex-direction:column; gap:10px; }
.radio-item { border:2px solid #ddd; border-radius:10px; padding:15px; cursor:pointer; display:flex; align-items:center; gap:10px; }
.radio-item input { width:auto; }
.radio-item:has(input:checked) { border-color:var(--orange); background:#fff5f2; }
.item { display:flex; justify-content:space-between; margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #eee; }
.total { font-size:22px; font-weight:700; margin-top:15px; padding-top:15px; border-top:2px solid var(--navy); }
.info-bayar { background:#fff3cd; color:#856404; padding:15px; border-radius:8px; font-size:13px; margin-top:10px; }
@media (max-width:768px){ .container{ grid-template-columns:1fr; } }
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <h2>1. Alamat Pengiriman</h2>
        <form method="POST">
            <div class="form-group">
                <label>Nama Penerima</label>
                <input type="text" name="nama" value="<?= $_SESSION['nama'] ?>" required>
            </div>
            <div class="form-group">
                <label>No. HP / WhatsApp</label>
                <input type="text" name="no_hp" placeholder="08123456789" required>
            </div>
            <div class="form-group">
                <label>Alamat Lengkap</label>
                <textarea name="alamat" rows="4" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos" required></textarea>
            </div>
            
            <h2 style="margin-top:30px;">2. Metode Pembayaran</h2>
            <div class="radio-group">
                <label class="radio-item">
                    <input type="radio" name="metode_bayar" value="COD" checked>
                    <div><b>💵 COD - Bayar di Tempat</b><br><small>Bayar saat kurir sampai</small></div>
                </label>
                <label class="radio-item">
                    <input type="radio" name="metode_bayar" value="Transfer Bank">
                    <div><b>🏦 Transfer Bank BCA</b><br><small>1234567890 a.n TAMz Bookstore</small></div>
                </label>
                <label class="radio-item">
                    <input type="radio" name="metode_bayar" value="QRIS">
                    <div><b>📱 QRIS</b><br><small>Scan QR code setelah checkout</small></div>
                </label>
                <label class="radio-item">
                    <input type="radio" name="metode_bayar" value="E-Wallet">
                    <div><b>💳 E-Wallet DANA/OVO/Gopay</b><br><small>08123456789 a.n TAMz</small></div>
                </label>
            </div>
            <div class="info-bayar">
                <b>Note:</b> Untuk Transfer/QRIS/E-Wallet, silakan bayar setelah checkout. Admin akan konfirmasi via WA. Pesanan COD langsung diproses.
            </div>
            <br>
            <button type="submit" name="bayar" class="btn-orange">Buat Pesanan</button>
        </form>
    </div>
    
    <div class="card">
        <h2>Ringkasan Pesanan</h2>
        <?php foreach($items as $item): ?>
        <div class="item">
            <div>
                <b><?= $item['judul'] ?></b><br>
                <small><?= $item['jumlah'] ?> x Rp<?= number_format($item['harga']) ?></small>
            </div>
            <b>Rp<?= number_format($item['harga'] * $item['jumlah']) ?></b>
        </div>
        <?php endforeach; ?>
        <div class="total">Total: Rp<?= number_format($total) ?></div>
    </div>
</body>
</html>