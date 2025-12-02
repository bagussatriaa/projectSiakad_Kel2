<<<<<<< Updated upstream
=======
<?php
// auth/login.php
session_start();
include '../koneksi/koneksi.php';

// Jika sudah login, redirect sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'prodi') header("Location: ../dashboard/dashboard_prodi.php");
    else if ($_SESSION['role'] == 'dosen') header("Location: ../dashboard/dashboard_dosen.php");
    else if ($_SESSION['role'] == 'mahasiswa') header("Location: ../dashboard/dashboard_mahasiswa.php");
    else if ($_SESSION['role'] == 'orang_tua') header("Location: ../dashboard/dashboard_ortu.php");
    exit;
}

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    
    // Enkripsi password dengan MD5 (sesuai data dummy, production disarankan password_verify)
    $password_md5 = md5($password);

    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password_md5'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        
        // Set Session
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['role'] = $data['role'];

        // Redirect berdasarkan Role
        switch($data['role']) {
            case 'prodi':
                header("Location: ../dashboard/dashboard_prodi.php");
                break;
            case 'dosen':
                header("Location: ../dashboard/dashboard_dosen.php");
                break;
            case 'mahasiswa':
                header("Location: ../dashboard/dashboard_mahasiswa.php");
                break;
            case 'orang_tua':
                header("Location: ../dashboard/dashboard_ortu.php");
                break;
        }
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIAKAD Vokasi USU</title>
    <!-- CSS Eksternal -->
    <link rel="stylesheet" href="http://localhost/projectSiakad_Kel2/style.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <h2>SIAKAD Vokasi</h2>
        <p>Silakan login untuk masuk ke sistem</p>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
            </div>
            <button type="submit" name="login" class="btn-login">LOGIN</button>
        </form>
    </div>
</div>

</body>
</html>
>>>>>>> Stashed changes
