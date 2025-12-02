<<<<<<< Updated upstream
=======
<?php
session_start();
// Cek Login & Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <link rel="stylesheet" href="http://localhost/projectSiakad_Kel2/style.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Mahasiswa</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard_mahasiswa.php" class="active">Dashboard</a></li>
            <li><a href="../mahasiswa/krs.php">Isi KRS</a></li>
            <li><a href="../mahasiswa/nilai.php">Lihat KHS / Nilai</a></li>
            <li><a href="../mahasiswa/absensi.php">Riwayat Absensi</a></li>
            <li><a href="../mahasiswa/tugas.php">Tugas Saya</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Hai, <?php echo $_SESSION['username']; ?></h2>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Status Akademik</h3>
                <p>Semester: 3 | IPK: 3.75</p>
            </div>
            <div class="card">
                <h3>Jadwal Hari Ini</h3>
                <p>Tidak ada jadwal kuliah.</p>
            </div>
            <div class="card">
                <h3>Tugas Pending</h3>
                <p>2 Tugas belum dikumpulkan.</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
>>>>>>> Stashed changes
