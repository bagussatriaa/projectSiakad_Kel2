<<<<<<< Updated upstream
=======
<?php
session_start();
// Cek Login & Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'prodi') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Program Studi</title>
    <link rel="stylesheet" href="http://localhost/projectSiakad_Kel2/style.css">
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Admin Prodi</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard_prodi.php" class="active">Dashboard</a></li>
            <li><a href="../prodi/kelola_kurikulum.php">Manajemen Kurikulum</a></li>
            <li><a href="../prodi/mahasiswa.php">Data Mahasiswa</a></li>
            <li><a href="../prodi/laporan.php">Laporan Akademik</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h2>Selamat Datang, <?php echo $_SESSION['username']; ?></h2>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Statistik Mahasiswa</h3>
                <p>Total Mahasiswa Aktif: 150</p>
            </div>
            <div class="card">
                <h3>Monitoring KRS</h3>
                <p>50 Mahasiswa belum KRS</p>
            </div>
            <div class="card">
                <h3>Monitoring KHS</h3>
                <p>Lihat hasil studi semester ini</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
>>>>>>> Stashed changes
