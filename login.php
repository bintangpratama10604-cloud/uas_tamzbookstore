<?php 
include 'koneksi.php';
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == 'admin') header("Location: admin.php");
    else header("Location: index.php");
    exit;
}
$error = "";
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $pass = md5($_POST['password']);
    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$pass'");
    $data = mysqli_fetch_assoc($query);
    if($data){
        $_SESSION['id'] = $data['id'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'];
        if($data['role'] == 'admin') header("Location: admin.php");
        else header("Location: index.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Login - <?= NAMA_TOKO ?></title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');
:root { --beige:#F5F1E6; --navy:#1E2A5E; --orange:#FF6B35; --white:#FFFFFF; }
* { margin:0; padding:0; box-sizing:border-box; font-family:'Poppins', sans-serif; }
body { background:var(--beige); display:flex; align-items:center; justify-content:center; height:100vh; }
.login-box { background:var(--white); padding:40px; border-radius:20px; width:380px; box-shadow:0 10px 40px rgba(0,0,0,0.1); }
.login-box h2 { text-align:center; color:var(--navy); margin-bottom:10px; }
.login-box p { text-align:center; color:#666; font-size:14px; margin-bottom:25px; }
.form-group { margin-bottom:15px; }
.form-group label { display:block; margin-bottom:5px; font-weight:500; color:var(--navy); }
.form-group input { width:100%; padding:12px; border:1px solid #ddd; border-radius:25px; outline:none; }
.btn-orange { background:var(--orange); color:var(--white); border:none; padding:12px; border-radius:25px; cursor:pointer; font-weight:600; width:100%; font-size:16px; }
.btn-orange:hover { background:#e85a28; }
.error { background:#ffebee; color:#c62828; padding:10px; border-radius:8px; text-align:center; margin-bottom:15px; font-size:14px; }
.info { text-align:center; margin-top:20px; font-size:13px; color:#666; }
</style>
</head>
<body>
<div class="login-box">
    <h2>📚 TAMz Bookstore</h2>
    <p>Silakan login untuk melanjutkan</p>
    <?php if($error) echo "<div class='error'>$error</div>"; ?>
    <form method="POST">
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" placeholder="admin@tamz.com" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
        </div>
        <button type="submit" name="login" class="btn-orange">Sign In</button>
    </form>
    <div class="info">
        Belum punya akun? <a href="register.php" style="color:var(--orange); text-decoration:none; font-weight:600;">Daftar di sini</a><br><br>
        <b>Admin:</b> admin@tamz.com / tamz123<br>
        <b>User:</b> user@tamz.com / user123
    </div>
</body>
</html>