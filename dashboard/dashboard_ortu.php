<<<<<<< Updated upstream
=======
<?php
session_start();
// Cek Login & Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'orang_tua') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Orang Tua</title>
    <link rel="stylesheet" href="http://localhost/projectSiakad_Kel2/style.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Orang Tua</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard_ortu.php" class="active">Dashboard</a></li>
            <li><a href="../orangTua/monitoring.php">Monitoring Nilai & Absen</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Portal Orang Tua</h2>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Monitoring Anak</h3>
                <p>Nama: Andi Mahasiswa</p>
                <p>Status: Aktif</p>
            </div>
            <div class="card">
                <h3>Kehadiran Terakhir</h3>
                <p>Pemrograman Web: Hadir</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
>>>>>>> Stashed changes
