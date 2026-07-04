<?php 
include 'koneksi.php';
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}
$id_user = $_SESSION['id'];
$trx = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_user=$id_user ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Riwayat Transaksi - <?= NAMA_TOKO ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --beige:#F5F1E6; --navy:#1E2A5E; --orange:#FF6B35; --white:#FFFFFF; --gray:#666666; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:var(--beige); color:var(--navy); }
.container { max-width:1000px; margin:40px auto; padding:0 20px; }
.btn-orange { background:var(--orange); color:var(--white); border:none; padding:10px 25px; border-radius:25px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; }
.btn-orange:hover { background:#e85a28; }
.card { background:var(--white); border-radius:15px; padding:30px; box-shadow:0 5px 15px rgba(0,0,0,0.08); margin-bottom:20px; }
.sukses { background:#e8f5e9; color:#2e7d32; padding:15px; border-radius:8px; margin-bottom:20px; text-align:center; border:2px solid #4caf50; }
.status { padding:6px 14px; border-radius:20px; font-size:12px; font-weight:700; display:inline-block; }
.status.pending { background:#fff3cd; color:#856404; }
.status.diproses { background:#cfe2ff; color:#084298; }
.status.dikirim { background:#d1e7dd; color:#0a3622; }
.status.selesai { background:#d1e7dd; color:#0a3622; }
.status.batal { background:#f8d7da; color:#842029; }
table { width:100%; border-collapse:collapse; margin-top:15px; }
th, td { padding:10px; border-bottom:1px solid #eee; text-align:left; font-size:14px; }
th { background:var(--beige); font-weight:600; }
.trx-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:15px; flex-wrap:wrap; gap:10px; }
.trx-info small { color:#666; line-height:1.8; }
.trx-info b { color:var(--navy); }
.info-admin { background:#fff5f2; border-left:4px solid var(--orange); padding:10px 15px; margin-top:10px; border-radius:5px; font-size:13px; }
.empty { text-align:center; padding:60px 0; }
.empty h2 { margin-bottom:15px; }
.navbar { background:var(--white); padding:20px 0; box-shadow:0 2px 10px rgba(0,0,0,0.05); position:sticky; top:0; z-index:10; margin-bottom:40px; }
.navbar .container { margin:0 auto; padding:0 20px; display:flex; justify-content:space-between; align-items:center; }
.logo { font-size:24px; font-weight:700; color:var(--navy); text-decoration:none; }
@media (max-width:768px){ .trx-header{ flex-direction:column; } }
</style>
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">📚 TAMz</a>
        <div>
            <a href="index.php" style="margin-right:15px; text-decoration:none; color:var(--navy);">← Kembali Belanja</a>
            <a href="logout.php" style="color:red; text-decoration:none; font-size:14px;">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h1 style="margin-bottom:30px;">📦 Riwayat Transaksi</h1>
    
    <?php if(isset($_GET['sukses'])): ?>
    <div class="sukses">
        <b>✅ Checkout Berhasil!</b><br>
        Kode Transaksi: <b><?= $_GET['sukses'] ?></b><br>
        <small>Pesanan lo udah masuk. Admin akan proses secepatnya. Cek status di bawah ya!</small>
    </div>
    <?php endif; ?>
    
    <?php if(mysqli_num_rows($trx) > 0): ?>
        <?php while($t = mysqli_fetch_assoc($trx)): ?>
        <div class="card">
            <div class="trx-header">
                <div class="trx-info">
                    <div style="font-size:18px; margin-bottom:8px;"><b><?= $t['kode_trx'] ?></b></div>
                    <small><?= date('d F Y, H:i', strtotime($t['created_at'])) ?> WIB</small><br>
                    <small><b>Metode Bayar:</b> <?= $t['metode_bayar'] ?></small><br>
                    <small><b>Penerima:</b> <?= $t['nama_penerima'] ?> - <?= $t['no_hp'] ?></small><br>
                    <small><b>Alamat:</b> <?= $t['alamat'] ?></small>
                </div>
                <div style="text-align:right;">
                    <span class="status <?= $t['status'] ?>"><?= strtoupper($t['status']) ?></span>
                    <div style="font-size:22px; font-weight:700; margin-top:10px; color:var(--orange);">
                        Rp<?= number_format($t['total_harga']) ?>
                    </div>
                </div>
            </div>
            
            <?php if($t['catatan_admin']): ?>
            <div class="info-admin">
                <b>📢 Info dari Admin:</b><br>
                <?= nl2br($t['catatan_admin']) ?>
            </div>
            <?php endif; ?>

            <table>
                <tr><th>Buku</th><th style="text-align:center;">Jumlah</th><th style="text-align:right;">Subtotal</th></tr>
                <?php 
                $detail = mysqli_query($conn, "SELECT d.*, b.judul, b.penulis FROM detail_transaksi d JOIN buku b ON d.id_buku=b.id WHERE d.id_transaksi={$t['id']}");
                while($d = mysqli_fetch_assoc($detail)):
                ?>
                <tr>
                    <td>
                        <b><?= $d['judul'] ?></b><br>
                        <small style="color:#666;"><?= $d['penulis'] ?></small>
                    </td>
                    <td style="text-align:center;"><?= $d['jumlah'] ?>x</td>
                    <td style="text-align:right;"><b>Rp<?= number_format($d['harga'] * $d['jumlah']) ?></b></td>
                </tr>
                <?php endwhile; ?>
            </table>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
    <div class="card empty">
        <h2>Belum Ada Transaksi</h2>
        <p style="margin:15px 0; color:#666;">Keranjang lo masih kosong nih. Yuk checkout buku favorit lo!</p>
        <a href="index.php" class="btn-orange">Mulai Belanja</a>
    </div>
    <?php endif; ?>
</div>

</body>
</html>