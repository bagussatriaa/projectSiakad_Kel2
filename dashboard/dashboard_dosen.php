<?php
session_start();
// Cek Login & Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Dosen</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Dosen</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard_dosen.php" class="active">Dashboard</a></li>
            <li><a href="../dosen/absensi.php">Kelola Absensi</a></li>
            <li><a href="../dosen/input_nilai.php">Input Nilai</a></li>
            <li><a href="../dosen/tugas.php">Tugas & Quiz</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Dashboard Dosen</h2>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Jadwal Mengajar</h3>
                <p>Senin: Pemrograman Web (08.00)</p>
            </div>
            <div class="card">
                <h3>Input Nilai</h3>
                <p>Batas waktu input nilai: 30 Des</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>