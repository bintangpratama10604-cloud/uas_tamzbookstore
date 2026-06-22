<?php 
include 'koneksi.php';
if(!isset($_SESSION['role']) || $_SESSION['role']!= 'admin'){
    header("Location: login.php");
    exit;
}

function compressImage($source, $max_width = 600, $quality = 60){
    if(!function_exists('imagecreatefromjpeg')){
        $type = mime_content_type($source);
        $data = base64_encode(file_get_contents($source));
        return 'data:'.$type.';base64,'.$data;
    }

    $info = getimagesize($source);
    if($info['mime'] == 'image/jpeg' || $info['mime'] == 'image/jpg') $image = imagecreatefromjpeg($source);
    elseif($info['mime'] == 'image/png') $image = imagecreatefrompng($source);
    elseif($info['mime'] == 'image/gif') $image = imagecreatefromgif($source);
    elseif($info['mime'] == 'image/webp') $image = imagecreatefromwebp($source);
    else return false;
    
    $width = imagesx($image);
    $height = imagesy($image);
    if($width > $max_width){
        $new_width = $max_width;
        $new_height = floor($height * ($max_width / $width));
        $tmp = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);
        imagecopyresampled($tmp, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        $image = $tmp;
    }
    ob_start();
    imagejpeg($image, null, $quality);
    $data = ob_get_clean();
    imagedestroy($image);
    return 'data:image/jpeg;base64,'.base64_encode($data);
}

// CRUD BUKU
if(isset($_POST['tambah'])){
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $rating = $_POST['rating'];
    $cover = '';
    if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] && $_FILES['cover']['size'] > 0){
        $cover = compressImage($_FILES['cover']['tmp_name']);
    }
    mysqli_query($conn, "INSERT INTO buku (judul, penulis, penerbit, kategori, harga, stok, rating, cover) VALUES ('$judul', '$penulis', '$penerbit', '$kategori', '$harga', '$stok', '$rating', '$cover')");
    header("Location: admin.php");
    exit;
}

if(isset($_GET['hapus'])){
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM buku WHERE id=$id");
    header("Location: admin.php");
    exit;
}

if(isset($_POST['edit'])){
    $id = intval($_POST['id']);
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $penulis = mysqli_real_escape_string($conn, $_POST['penulis']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $kategori = mysqli_real_escape_string($conn, $_POST['kategori']);
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $rating = $_POST['rating'];
    
    $sql = "UPDATE buku SET judul='$judul', penulis='$penulis', penerbit='$penerbit', kategori='$kategori', harga='$harga', stok='$stok', rating='$rating'";
    
    if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] && $_FILES['cover']['size'] > 0){
        $cover = compressImage($_FILES['cover']['tmp_name']);
        if($cover) $sql .= ", cover='".mysqli_real_escape_string($conn, $cover)."'";
    }
    
    $sql .= " WHERE id=$id";
    mysqli_query($conn, $sql);
    header("Location: admin.php");
    exit;
}

// UPDATE STATUS PESANAN
if(isset($_POST['update_status'])){
    $id_trx = intval($_POST['id_trx']);
    $status = $_POST['status'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan_admin']);
    mysqli_query($conn, "UPDATE transaksi SET status='$status', catatan_admin='$catatan' WHERE id=$id_trx");
    header("Location: admin.php?tab=pesanan");
    exit;
}

$tab = $_GET['tab'] ?? 'buku';
$buku = mysqli_query($conn, "SELECT * FROM buku ORDER BY id DESC");
$edit = null;
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM buku WHERE id=$id"));
}

$pesanan = mysqli_query($conn, "SELECT t.*, u.nama as nama_user, u.email FROM transaksi t JOIN users u ON t.id_user=u.id ORDER BY t.id DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin - <?= NAMA_TOKO?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --beige:#F5F1E6; --navy:#1E2A5E; --orange:#FF6B35; --white:#FFFFFF; --gray:#666666; --red:#DC3545; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background: var(--white); color: var(--navy); }
.container { max-width:1400px; margin:auto; padding:0 20px; }
.btn-orange { background:var(--orange); color:var(--white); border:none; padding:10px 25px; border-radius:25px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; font-size:14px; }
.btn-orange:hover { background:#e85a28; }
.navbar { background:var(--white); padding:20px 0; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
.navbar .container { display:flex; justify-content:space-between; align-items:center; }
.logo { font-size:24px; font-weight:700; color:var(--navy); }
.tabs { display:flex; gap:10px; margin:30px 0; border-bottom:2px solid #eee; }
.tab { padding:12px 25px; text-decoration:none; color:var(--gray); font-weight:600; border-bottom:3px solid transparent; }
.tab.active { color:var(--orange); border-bottom-color:var(--orange); }
.section-title { text-align:center; font-size:28px; margin-bottom:40px; color:var(--navy); font-weight:700; }
.form-box { background:var(--beige); padding:30px; border-radius:15px; margin-bottom:40px; }
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
.form-group { margin-bottom:15px; }
.form-group label { display:block; margin-bottom:5px; font-weight:500; font-size:14px; }
.form-group input,.form-group select, .form-group textarea { width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px; }
.form-group input:focus, .form-group select:focus { outline:none; border-color:var(--orange); }

/* TABEL RAPI KAYAK DI SS */
.table-card { background:var(--white); border-radius:15px; box-shadow:0 5px 20px rgba(0,0,0,0.08); overflow:hidden; }
table { width:100%; border-collapse:collapse; }
th { background:var(--beige); padding:18px 15px; text-align:left; font-weight:600; font-size:14px; color:var(--navy); }
td { padding:15px; border-bottom:1px solid #f0f0f0; font-size:14px; vertical-align:middle; }
tr:last-child td { border-bottom:none; }
tr:hover { background:#fafafa; }
td img { width:45px; height:45px; object-fit:cover; border-radius:8px; }

.btn-action { padding:6px 15px; font-size:12px; border-radius:20px; text-decoration:none; font-weight:600; display:inline-block; }
.btn-edit { background:var(--navy); color:var(--white); }
.btn-edit:hover { background:#0f1a3d; }
.btn-hapus { background:var(--red); color:var(--white); margin-left:5px; }
.btn-hapus:hover { background:#c82333; }

.status { padding:5px 12px; border-radius:15px; font-size:11px; font-weight:600; display:inline-block; }
.status.pending { background:#fff3cd; color:#856404; }
.status.diproses { background:#cfe2ff; color:#084298; }
.status.dikirim { background:#d1e7dd; color:#0a3622; }
.status.selesai { background:#d1e7dd; color:#0a3622; }
.status.batal { background:#f8d7da; color:#842029; }

.modal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:100; }
.modal-content { background:var(--white); max-width:600px; margin:50px auto; padding:30px; border-radius:15px; max-height:80vh; overflow-y:auto; }
.alert-gd { background:#fff3cd; color:#856404; padding:15px; border-radius:10px; margin-bottom:20px; border:1px solid #ffeaa7; font-size:14px; }

@media (max-width:768px){ 
    .form-grid{ grid-template-columns:1fr; } 
    table{ font-size:12px; } 
    th, td{ padding:10px 8px; }
    .btn-action { padding:4px 10px; font-size:11px; }
}
</style>
</head>
<body>

<nav class="navbar">
    <div class="container">
        <div class="logo">📚 TAMz Admin</div>
        <div>
            <span style="margin-right:15px; font-size:14px;">Halo, <?= $_SESSION['nama']?></span>
            <a href="index.php" class="btn-orange">Lihat Toko</a>
            <a href="logout.php" style="margin-left:10px; color:var(--red); text-decoration:none; font-size:14px; font-weight:600;">Logout</a>
        </div>
    </div>
</nav>

<div class="container" style="padding:40px 20px;">
    <?php if(!function_exists('imagecreatefromjpeg')): ?>
    <div class="alert-gd">
        <b>⚠️ Peringatan:</b> GD Library belum aktif. Gambar tidak akan di-compress otomatis. Aktifkan <code>extension=gd</code> di php.ini untuk performa terbaik.
    </div>
    <?php endif; ?>

    <div class="tabs">
        <a href="admin.php?tab=buku" class="tab <?= $tab=='buku'?'active':'' ?>">📚 Kelola Buku</a>
        <a href="admin.php?tab=pesanan" class="tab <?= $tab=='pesanan'?'active':'' ?>">📦 Kelola Pesanan</a>
    </div>

    <?php if($tab=='buku'): ?>
    <h2 class="section-title"><?= $edit? 'Edit Buku' : 'Tambah Buku Baru'?></h2>
    <form method="POST" enctype="multipart/form-data" class="form-box">
        <input type="hidden" name="id" value="<?= $edit['id']?? ''?>">
        <div class="form-grid">
            <div class="form-group">
                <label>Judul Buku</label>
                <input type="text" name="judul" value="<?= $edit['judul']?? ''?>" required>
            </div>
            <div class="form-group">
                <label>Penulis</label>
                <input type="text" name="penulis" value="<?= $edit['penulis']?? ''?>" required>
            </div>
            <div class="form-group">
                <label>Penerbit</label>
                <input type="text" name="penerbit" value="<?= $edit['penerbit']?? ''?>">
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option <?= @$edit['kategori']=='Sejarah'?'selected':'' ?>>Sejarah</option>
                    <option <?= @$edit['kategori']=='Teknologi'?'selected':'' ?>>Teknologi</option>
                    <option <?= @$edit['kategori']=='Kisah'?'selected':'' ?>>Kisah</option>
                    <option <?= @$edit['kategori']=='Fiksi'?'selected':'' ?>>Fiksi</option>
                </select>
            </div>
            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" value="<?= $edit['harga']?? ''?>" required>
            </div>
            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" value="<?= $edit['stok']?? ''?>" required>
            </div>
            <div class="form-group">
                <label>Rating</label>
                <input type="number" step="0.1" max="5" name="rating" value="<?= $edit['rating']?? '4.0'?>" required>
            </div>
            <div class="form-group">
                <label>Cover Buku <?= $edit? '(Kosongkan jika tidak ganti)' : ''?></label>
                <input type="file" name="cover" accept="image/*">
                <?php if($edit && $edit['cover']):?>
                <img src="<?= $edit['cover']?>" width="80" style="margin-top:10px; border-radius:8px;">
                <?php endif;?>
            </div>
        </div>
        <br>
        <?php if($edit):?>
            <button type="submit" name="edit" class="btn-orange">Update Buku</button>
            <a href="admin.php" style="margin-left:10px;">Batal</a>
        <?php else:?>
            <button type="submit" name="tambah" class="btn-orange">Tambah Buku</button>
        <?php endif;?>
    </form>

    <h2 class="section-title">Daftar Buku TAMz</h2>
    <div class="table-card">
        <table>
            <tr>
                <th>Cover</th>
                <th>Judul</th>
                <th>Penulis</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Rating</th>
                <th>Aksi</th>
            </tr>
            <?php while($b = mysqli_fetch_assoc($buku)):?>
            <tr>
                <td><img src="<?= $b['cover']?: 'https://via.placeholder.com/50x70/f5f1e6/1e2a5e?text=No+Cover'?>" alt="<?= $b['judul']?>"></td>
                <td><?= $b['judul']?></td>
                <td><?= $b['penulis']?></td>
                <td><?= $b['kategori']?></td>
                <td>Rp<?= number_format($b['harga'])?></td>
                <td><?= $b['stok']?></td>
                <td>⭐ <?= $b['rating']?></td>
                <td>
                    <a href="admin.php?edit=<?= $b['id']?>" class="btn-action btn-edit">Edit</a>
                    <a href="admin.php?hapus=<?= $b['id']?>" onclick="return confirm('Hapus buku ini?')" class="btn-action btn-hapus">Hapus</a>
                </td>
            </tr>
            <?php endwhile;?>
        </table>
    </div>

    <?php else: ?>
    <h2 class="section-title">Kelola Pesanan Masuk</h2>
    <div class="table-card">
        <table>
            <tr>
                <th>Kode</th><th>Pembeli</th><th>Total</th><th>Bayar</th><th>Status</th><th>Tanggal</th><th>Aksi</th>
            </tr>
            <?php while($p = mysqli_fetch_assoc($pesanan)): ?>
            <tr>
                <td><b><?= $p['kode_trx'] ?></b></td>
                <td><?= $p['nama_user'] ?><br><small style="color:var(--gray)"><?= $p['email'] ?></small></td>
                <td><b>Rp<?= number_format($p['total_harga']) ?></b></td>
                <td><?= $p['metode_bayar'] ?></td>
                <td><span class="status <?= $p['status'] ?>"><?= strtoupper($p['status']) ?></span></td>
                <td><small><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></small></td>
                <td>
                    <button onclick="detailPesanan(<?= $p['id'] ?>)" class="btn-action btn-edit">Detail</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Detail Pesanan -->
<div id="modalDetail" class="modal">
    <div class="modal-content" id="modalContent"></div>
</div>

<script>
function detailPesanan(id){
    fetch('detail_pesanan.php?id='+id)
    .then(res => res.text())
    .then(html => {
        document.getElementById('modalContent').innerHTML = html;
        document.getElementById('modalDetail').style.display = 'block';
    });
}
function tutupModal(){
    document.getElementById('modalDetail').style.display = 'none';
}
window.onclick = function(e){
    if(e.target == document.getElementById('modalDetail')) tutupModal();
}
</script>

</body>
</html>