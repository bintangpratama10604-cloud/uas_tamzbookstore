<?php 
include 'koneksi.php';
if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}
$id_user = $_SESSION['id'];

// Hapus item
if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM keranjang WHERE id=$id AND id_user=$id_user");
    header("Location: keranjang.php");
    exit;
}

// Update jumlah
if(isset($_POST['update'])){
    foreach($_POST['jumlah'] as $id => $jml){
        $jml = intval($jml);
        if($jml > 0) mysqli_query($conn, "UPDATE keranjang SET jumlah=$jml WHERE id=$id AND id_user=$id_user");
    }
    header("Location: keranjang.php");
    exit;
}

$keranjang = mysqli_query($conn, "SELECT k.*, b.judul, b.penulis, b.harga, b.cover FROM keranjang k JOIN buku b ON k.id_buku=b.id WHERE k.id_user=$id_user");
$total = 0;
?>
<!DOCTYPE html>
<html>
<head>
<title>Keranjang - <?= NAMA_TOKO ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --beige:#F5F1E6; --navy:#1E2A5E; --orange:#FF6B35; --white:#FFFFFF; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:var(--beige); color:var(--navy); }
.container { max-width:1000px; margin:40px auto; padding:0 20px; }
.btn-orange { background:var(--orange); color:var(--white); border:none; padding:10px 25px; border-radius:25px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; }
.card { background:var(--white); border-radius:15px; padding:30px; box-shadow:0 5px 15px rgba(0,0,0,0.08); }
h1 { margin-bottom:30px; }
table { width:100%; border-collapse:collapse; }
th, td { padding:15px; border-bottom:1px solid #eee; text-align:left; }
th { background:var(--beige); }
.cart-item { display:flex; gap:15px; align-items:center; }
.cart-item img { width:60px; height:80px; object-fit:cover; border-radius:8px; }
.jumlah { width:60px; padding:5px; border:1px solid #ddd; border-radius:8px; text-align:center; }
.total-box { text-align:right; margin-top:30px; font-size:20px; font-weight:700; }
.btn-hapus { color:red; text-decoration:none; font-size:13px; }
.empty { text-align:center; padding:60px 0; color:#666; }
</style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>🛒 Keranjang Belanja</h1>
        <?php if(mysqli_num_rows($keranjang) > 0): ?>
        <form method="POST">
            <table>
                <tr><th>Produk</th><th>Harga</th><th>Jumlah</th><th>Subtotal</th><th></th></tr>
                <?php while($k = mysqli_fetch_assoc($keranjang)): 
                    $subtotal = $k['harga'] * $k['jumlah'];
                    $total += $subtotal;
                ?>
                <tr>
                    <td>
                        <div class="cart-item">
                            <img src="<?= $k['cover'] ?: 'https://via.placeholder.com/60x80' ?>">
                            <div>
                                <b><?= $k['judul'] ?></b><br>
                                <small style="color:#666;"><?= $k['penulis'] ?></small>
                            </div>
                        </div>
                    </td>
                    <td>Rp<?= number_format($k['harga']) ?></td>
                    <td><input type="number" name="jumlah[<?= $k['id'] ?>]" value="<?= $k['jumlah'] ?>" min="1" class="jumlah"></td>
                    <td><b>Rp<?= number_format($subtotal) ?></b></td>
                    <td><a href="keranjang.php?hapus=<?= $k['id'] ?>" class="btn-hapus" onclick="return confirm('Hapus?')">Hapus</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <div style="display:flex; justify-content:space-between; margin-top:20px;">
                <button type="submit" name="update" class="btn-orange" style="background:var(--navy);">Update Keranjang</button>
                <div class="total-box">Total: Rp<?= number_format($total) ?></div>
            </div>
            <div style="text-align:right; margin-top:20px;">
                <a href="index.php" style="margin-right:15px;">Lanjut Belanja</a>
                <a href="checkout.php" class="btn-orange">Checkout</a>
            </div>
        </form>
        <?php else: ?>
        <div class="empty">
            <h2>Keranjang Kosong</h2>
            <p style="margin:15px 0;">Yuk belanja dulu!</p>
            <a href="index.php" class="btn-orange">Mulai Belanja</a>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>