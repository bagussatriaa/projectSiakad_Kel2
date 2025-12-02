<<<<<<< Updated upstream
=======
<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'prodi') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Akademik</title>
    <link rel="stylesheet" href="http://localhost/projectSiakad_Kel2/style.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Admin Prodi</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="../dashboard/dashboard_prodi.php">Dashboard</a></li>
            <li><a href="kelola_kurikulum.php">Manajemen Kurikulum</a></li>
            <li><a href="mahasiswa.php">Data Mahasiswa & KRS</a></li>
            <li><a href="laporan.php" class="active">Laporan Akademik</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Pusat Laporan Akademik</h2>
        </div>

        <div class="card-container">
            <!-- Laporan 1 -->
            <div class="card">
                <h3>Rekapitulasi Mahasiswa Aktif</h3>
                <p>Melihat jumlah mahasiswa aktif per angkatan dan statusnya.</p>
                <button class="btn-submit" onclick="alert('Fitur Cetak PDF akan diimplementasikan nanti')">Cetak PDF</button>
            </div>

            <!-- Laporan 2 -->
            <div class="card">
                <h3>Transkrip Nilai (KHS)</h3>
                <p>Cetak KHS mahasiswa terpilih.</p>
                <form action="" method="GET">
                   <select class="form-control" style="margin-bottom:10px;">
                       <option>-- Pilih Mahasiswa --</option>
                       <!-- Loop mahasiswa -->
                   </select>
                   <button class="btn-submit">Lihat Transkrip</button>
                </form>
            </div>
        </div>

        <div class="card" style="margin-top:20px;">
            <h3>Preview: Mahasiswa Berprestasi (IPK Tertinggi)</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>IPK Sementara</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" style="text-align:center;">Data IPK belum dikalkulasi secara otomatis.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
>>>>>>> Stashed changes
