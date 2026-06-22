<?php 
include 'koneksi.php';
// Hitung isi keranjang
$jml_keranjang = 0;
if(isset($_SESSION['id'])){
    $id_user = $_SESSION['id'];
    $q = mysqli_query($conn, "SELECT SUM(jumlah) as total FROM keranjang WHERE id_user=$id_user");
    $jml_keranjang = mysqli_fetch_assoc($q)['total'] ?? 0;
}

// Filter kategori
$kategori_aktif = $_GET['kategori'] ?? '';
$where = $kategori_aktif ? "WHERE kategori='$kategori_aktif'" : "";
$buku = mysqli_query($conn, "SELECT * FROM buku $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title><?= NAMA_TOKO ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --beige:#F5F1E6; --navy:#1E2A5E; --orange:#FF6B35; --white:#FFFFFF; --gray:#666666; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background: var(--white); color: var(--navy); }
.container { max-width:1200px; margin:auto; padding:0 20px; }
.btn-orange { background:var(--orange); color:var(--white); border:none; padding:10px 25px; border-radius:25px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; }
.btn-orange:hover { background:#e85a28; }
.btn-outline { background:transparent; color:var(--navy); border:2px solid var(--navy); padding:8px 20px; border-radius:25px; cursor:pointer; font-weight:600; text-decoration:none; display:inline-block; }
.navbar { background:var(--white); padding:20px 0; box-shadow:0 2px 10px rgba(0,0,0,0.05); position:sticky; top:0; z-index:10; }
.navbar .container { display:flex; justify-content:space-between; align-items:center; }
.logo { font-size:24px; font-weight:700; color:var(--navy); text-decoration:none; }
.nav-menu a { margin:0 15px; text-decoration:none; color:var(--navy); font-weight:500; }
.nav-menu a.active { color:var(--orange); }
.nav-right { display:flex; align-items:center; gap:15px; }
.cart-icon { position:relative; font-size:24px; text-decoration:none; color:var(--navy); }
.cart-badge { position:absolute; top:-8px; right:-8px; background:var(--orange); color:var(--white); border-radius:50%; width:20px; height:20px; font-size:11px; display:flex; align-items:center; justify-content:center; font-weight:700; }
.hero { background:var(--beige); padding:60px 0; }
.hero-content { display:flex; align-items:center; gap:40px; }
.hero-text h1 { font-size:42px; line-height:1.2; margin-bottom:15px; }
.hero-text p { color:var(--gray); margin-bottom:25px; max-width:450px; }
.section { padding:60px 0; }
.section-title { text-align:center; font-size:28px; margin-bottom:40px; color:var(--navy); }
.book-types { display:flex; justify-content:center; gap:40px; flex-wrap:wrap; }
.type-item { text-align:center; text-decoration:none; color:var(--navy); }
.type-icon { width:80px; height:80px; background:var(--navy); color:var(--white); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; font-size:32px; transition:0.3s; }
.type-item:hover .type-icon, .type-item.active .type-icon { background:var(--orange); transform:scale(1.1); }
.book-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:25px; }
.book-card { background:var(--white); border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.08); overflow:hidden; transition:0.3s; }
.book-card:hover { transform:translateY(-5px); }
.book-card img { width:100%; height:260px; object-fit:cover; background:var(--beige); }
.book-info { padding:15px; }
.book-info h4 { font-size:16px; margin-bottom:5px; height:40px; overflow:hidden; }
.book-info .author { font-size:13px; color:var(--gray); margin-bottom:8px; }
.book-info .rating { font-size:13px; color:var(--orange); margin-bottom:10px; }
.book-bottom { display:flex; justify-content:space-between; align-items:center; }
.book-price { font-size:18px; font-weight:700; color:var(--navy); }
.btn-add { background:var(--orange); color:var(--white); border:none; padding:6px 15px; border-radius:20px; font-size:13px; cursor:pointer; text-decoration:none; }
.footer { background:var(--navy); color:var(--white); padding:50px 0 20px; margin-top:60px; }
.footer-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:30px; margin-bottom:30px; }
.footer h4 { margin-bottom:15px; }
.footer a, .footer p { color:#ccc; font-size:14px; text-decoration:none; line-height:2; }
@media (max-width:768px){ .hero-content{ flex-direction:column; text-align:center; } .book-types{ gap:20px; } .footer-grid{ grid-template-columns:1fr 1fr; } }
</style>
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="index.php" class="logo">📚 TAMz</a>
        <div class="nav-menu">
            <a href="index.php" class="<?= !$kategori_aktif ? 'active' : '' ?>">Home</a>
            <?php if(isset($_SESSION['role'])): ?>
            <a href="riwayat.php">Riwayat</a>
            <?php endif; ?>
        </div>
        <div class="nav-right">
            <?php if(isset($_SESSION['role'])): ?>
                <a href="keranjang.php" class="cart-icon">🛒
                    <?php if($jml_keranjang > 0): ?>
                    <span class="cart-badge"><?= $jml_keranjang ?></span>
                    <?php endif; ?>
                </a>
                <span style="font-size:14px;">Halo, <?= $_SESSION['nama'] ?></span>
                <?php if($_SESSION['role'] == 'admin'): ?>
                    <a href="admin.php" class="btn-orange">Admin</a>
                <?php endif; ?>
                <a href="logout.php" style="color:red; text-decoration:none; font-size:14px;">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn-outline">Login</a>
                <a href="register.php" class="btn-orange">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>2024 Reading Challenge</h1>
                <p>Want to get more out of your reading life in 2024? We've got a challenge just for you, and a free reading challenge kit to help you see it through.</p>
                <a href="#" class="btn-orange">Learn more</a>
            </div>
            <div><img src="https://cdn-icons-png.flaticon.com/512/3389/3389081.png" width="350"></div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Kategori Buku</h2>
        <div class="book-types">
            <a href="index.php" class="type-item <?= !$kategori_aktif ? 'active' : '' ?>">
                <div class="type-icon">📚</div><p>Semua</p>
            </a>
            <a href="index.php?kategori=Sejarah" class="type-item <?= $kategori_aktif=='Sejarah' ? 'active' : '' ?>">
                <div class="type-icon">📜</div><p>Sejarah</p>
            </a>
            <a href="index.php?kategori=Teknologi" class="type-item <?= $kategori_aktif=='Teknologi' ? 'active' : '' ?>">
                <div class="type-icon">💻</div><p>Teknologi</p>
            </a>
            <a href="index.php?kategori=Kisah" class="type-item <?= $kategori_aktif=='Kisah' ? 'active' : '' ?>">
                <div class="type-icon">✨</div><p>Kisah</p>
            </a>
            <a href="index.php?kategori=Fiksi" class="type-item <?= $kategori_aktif=='Fiksi' ? 'active' : '' ?>">
                <div class="type-icon">🤖</div><p>Fiksi</p>
            </a>
        </div>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">
        <h2 class="section-title"><?= $kategori_aktif ? "Kategori: $kategori_aktif" : "Semua Buku" ?></h2>
        <div class="book-grid">
            <?php if(mysqli_num_rows($buku) > 0): ?>
            <?php while($b = mysqli_fetch_assoc($buku)): ?>
            <div class="book-card">
                <img src="<?= $b['cover'] ? $b['cover'] : 'https://via.placeholder.com/200x260/F5F1E6/1E2A5E?text=No+Cover' ?>" alt="<?= $b['judul'] ?>">
                <div class="book-info">
                    <h4><?= $b['judul'] ?></h4>
                    <p class="author"><?= $b['penulis'] ?></p>
                    <p class="rating">⭐ <?= $b['rating'] ?></p>
                    <div class="book-bottom">
                        <span class="book-price">Rp<?= number_format($b['harga']) ?></span>
                        <?php if(isset($_SESSION['role'])): ?>
                            <a href="tambah_keranjang.php?id=<?= $b['id'] ?>" class="btn-add">+ Keranjang</a>
                        <?php else: ?>
                            <a href="login.php" class="btn-add">+ Keranjang</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
            <?php else: ?>
            <p style="grid-column:1/-1; text-align:center; color:#666;">Belum ada buku di kategori ini.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div><h4>TAMz</h4><p>Best bookstore for your reading journey</p></div>
            <div><h4>Contact us</h4><p>085137330553<br>085333769300</p></div>
            <div><h4>Address</h4><p>Mataram, NUSA TENGGARA BARAT<br>Universitas Teknologi Mataram</p></div>
            <div><h4>Subscribe</h4><input type="email" placeholder="Email" style="padding:8px; border-radius:20px; border:none; width:100%;"></div>
        </div>
    </div>
</footer>

</body>
</html>