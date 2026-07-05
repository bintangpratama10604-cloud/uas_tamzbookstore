<?php 
include 'koneksi.php';
if(isset($_SESSION['role'])){
    header("Location: index.php");
    exit;
}
$error = "";
$sukses = "";
if(isset($_POST['daftar'])){
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pass1 = $_POST['password'];
    $pass2 = $_POST['password2'];
    
    if($pass1 != $pass2){
        $error = "Password tidak sama!";
    } else {
        $cek = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($cek) > 0){
            $error = "Email sudah terdaftar!";
        } else {
            $pass = md5($pass1);
            mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$pass', 'user')");
            $sukses = "Pendaftaran berhasil! Silakan login.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Daftar - <?= NAMA_TOKO ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --beige:#F5F1E6; --navy:#1E2A5E; --orange:#FF6B35; --white:#FFFFFF; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:var(--beige); display:flex; align-items:center; justify-content:center; min-height:100vh; padding:20px 0; }
.login-box { background:var(--white); padding:40px; border-radius:20px; width:380px; box-shadow:0 10px 40px rgba(0,0,0,0.1); }
.login-box h2 { text-align:center; color:var(--navy); margin-bottom:10px; }
.login-box p { text-align:center; color:#666; font-size:14px; margin-bottom:25px; }
.form-group { margin-bottom:15px; }
.form-group label { display:block; margin-bottom:5px; font-weight:500; color:var(--navy); }
.form-group input { width:100%; padding:12px; border:1px solid #ddd; border-radius:25px; outline:none; }
.btn-orange { background:var(--orange); color:var(--white); border:none; padding:12px; border-radius:25px; cursor:pointer; font-weight:600; width:100%; font-size:16px; }
.btn-orange:hover { background:#e85a28; }
.error { background:#ffebee; color:#c62828; padding:10px; border-radius:8px; text-align:center; margin-bottom:15px; font-size:14px; }
.sukses { background:#e8f5e9; color:#2e7d32; padding:10px; border-radius:8px; text-align:center; margin-bottom:15px; font-size:14px; }
.info { text-align:center; margin-top:20px; font-size:14px; color:#666; }
.info a { color:var(--orange); text-decoration:none; font-weight:600; }
</style>
</head>
<body>
<div class="login-box">
    <h2>📚 Daftar Akun</h2>
    <p>Buat akun TAMz Bookstore gratis</p>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <?php if($sukses) echo "<div class='sukses'>$sukses</div>"; ?>
    <form method="POST">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Masukkan nama" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="email@kamu.com" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Min 6 karakter" required>
        </div>
        <div class="form-group">
            <label>Ulangi Password</label>
            <input type="password" name="password2" placeholder="Ulangi password" required>
        </div>
        <button type="submit" name="daftar" class="btn-orange">Daftar Sekarang</button>
    </form>
    <div class="info">
        Sudah punya akun? <a href="login.php">Login di sini</a>
    </div>
</div>
</body>
</html>