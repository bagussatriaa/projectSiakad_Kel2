<?php
session_start();
include '../koneksi/koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'prodi') {
    header("Location: ../auth/login.php");
    exit;
}

// 1. Hitung Total Mahasiswa Aktif
$q_mhs = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM mahasiswa");
$d_mhs = mysqli_fetch_assoc($q_mhs);
$total_mhs = $d_mhs['total'];

// 2. Hitung Status KRS
// Belum KRS (Mahasiswa yang tidak ada di tabel KRS untuk semester ini, misal sem 3)
// Catatan: Logic ini disederhanakan. Idealnya cek per semester aktif.
$q_krs_submit = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM krs WHERE status != 'draft'");
$d_krs_submit = mysqli_fetch_assoc($q_krs_submit);
$sudah_krs = $d_krs_submit['total'];

$belum_krs = $total_mhs - $sudah_krs;

// 3. Menghitung KRS yang Perlu Validasi (Status 'diajukan')
$q_need_approve = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM krs WHERE status = 'diajukan'");
$d_need_approve = mysqli_fetch_assoc($q_need_approve);
$butuh_validasi = $d_need_approve['total'];

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Program Studi</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Admin Prodi</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard_prodi.php" class="active">Dashboard</a></li>
            <li><a href="../prodi/kelola_kurikulum.php">Manajemen Kurikulum</a></li>
            <li><a href="../prodi/mahasiswa.php">Data Mahasiswa & KRS</a></li>
            <li><a href="../prodi/laporan.php">Laporan Akademik</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Dashboard Statistik Prodi</h2>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Mahasiswa Aktif</h3>
                <h1 style="font-size: 40px; color: var(--primary); margin: 10px 0;"><?= $total_mhs ?></h1>
                <p>Total Mahasiswa Terdaftar</p>
            </div>
            <div class="card">
                <h3>Monitoring KRS</h3>
                <h1 style="font-size: 40px; color: var(--warning); margin: 10px 0;"><?= $butuh_validasi ?></h1>
                <p>KRS Menunggu Persetujuan</p>
                <?php if($butuh_validasi > 0): ?>
                    <a href="../prodi/mahasiswa.php" class="badge badge-warning">Segera Validasi</a>
                <?php endif; ?>
            </div>
            <div class="card">
                <h3>Status Pengisian</h3>
                <p>Sudah Isi KRS: <strong><?= $sudah_krs ?></strong></p>
                <p>Belum Isi KRS: <strong><?= $belum_krs ?></strong></p>
            </div>
        </div>
    </div>
</div>

</body>
</html>