<?php
include 'koneksi.php';
if(!isset($_SESSION['role']) || $_SESSION['role']!= 'admin') exit;

$id = intval($_GET['id']);
$trx = mysqli_fetch_assoc(mysqli_query($conn, "SELECT t.*, u.nama as nama_user, u.email FROM transaksi t JOIN users u ON t.id_user=u.id WHERE t.id=$id"));
$detail = mysqli_query($conn, "SELECT d.*, b.judul FROM detail_transaksi d JOIN buku b ON d.id_buku=b.id WHERE d.id_transaksi=$id");
?>
<h2>Detail Pesanan <?= $trx['kode_trx'] ?></h2>
<p><b>Pembeli:</b> <?= $trx['nama_user'] ?> (<?= $trx['email'] ?>)</p>
<p><b>Penerima:</b> <?= $trx['nama_penerima'] ?> - <?= $trx['no_hp'] ?></p>
<p><b>Alamat:</b> <?= $trx['alamat'] ?></p>
<p><b>Metode Bayar:</b> <?= $trx['metode_bayar'] ?></p>
<hr style="margin:20px 0;">
<table style="width:100%;">
    <tr><th>Buku</th><th>Jumlah</th><th>Subtotal</th></tr>
    <?php while($d = mysqli_fetch_assoc($detail)): ?>
    <tr>
        <td><?= $d['judul'] ?></td>
        <td><?= $d['jumlah'] ?></td>
        <td>Rp<?= number_format($d['harga'] * $d['jumlah']) ?></td>
    </tr>
    <?php endwhile; ?>
</table>
<h3 style="text-align:right; margin-top:15px;">Total: Rp<?= number_format($trx['total_harga']) ?></h3>
<hr style="margin:20px 0;">
<form method="POST" action="admin.php?tab=pesanan">
    <input type="hidden" name="id_trx" value="<?= $trx['id'] ?>">
    <div class="form-group">
        <label>Update Status</label>
        <select name="status" required>
            <option value="pending" <?= $trx['status']=='pending'?'selected':'' ?>>Pending</option>
            <option value="diproses" <?= $trx['status']=='diproses'?'selected':'' ?>>Diproses</option>
            <option value="dikirim" <?= $trx['status']=='dikirim'?'selected':'' ?>>Dikirim</option>
            <option value="selesai" <?= $trx['status']=='selesai'?'selected':'' ?>>Selesai</option>
            <option value="batal" <?= $trx['status']=='batal'?'selected':'' ?>>Batal</option>
        </select>
    </div>
    <div class="form-group">
        <label>Catatan Admin / No. Resi</label>
        <textarea name="catatan_admin" rows="2"><?= $trx['catatan_admin'] ?></textarea>
    </div>
    <button type="submit" name="update_status" class="btn-orange">Update Pesanan</button>
    <button type="button" onclick="tutupModal()" style="margin-left:10px;">Tutup</button>
</form>