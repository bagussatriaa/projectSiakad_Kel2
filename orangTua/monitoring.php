<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'orang_tua') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
// Cari data Orang Tua
$ortu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM orang_tua WHERE user_id = '$user_id'"));
$ortu_id = $ortu['id'];

// Cari data Mahasiswa (Anak)
$query_anak = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE orang_tua_id = '$ortu_id'");
$anak = mysqli_fetch_assoc($query_anak);

if (!$anak) {
    echo "Data anak tidak ditemukan. Hubungi admin.";
    exit;
}

$nim_anak = $anak['nim'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring - Orang Tua</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Orang Tua: <?= $ortu['nama'] ?></small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="../dashboard/dashboard_ortu.php">Dashboard</a></li>
            <li><a href="monitoring.php" class="active">Monitoring Nilai & Absen</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Monitoring Akademik: <?= $anak['nama'] ?> (<?= $anak['nim'] ?>)</h2>
        </div>

        <div class="card-container">
            <!-- Monitoring Nilai -->
            <div class="card" style="grid-column: span 2;">
                <h3>Perkembangan Nilai Studi</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>Jenis</th>
                                <th>Nilai</th>
                                <th>Predikat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $q_nilai = mysqli_query($koneksi, "SELECT n.*, mk.nama_mk 
                                                              FROM nilai n 
                                                              JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id
                                                              WHERE n.mahasiswa_nim = '$nim_anak'
                                                              ORDER BY mk.nama_mk ASC");
                            while($n = mysqli_fetch_assoc($q_nilai)):
                            ?>
                            <tr>
                                <td><?= $n['nama_mk'] ?></td>
                                <td><?= strtoupper($n['jenis_nilai']) ?></td>
                                <td><strong><?= $n['nilai'] ?></strong></td>
                                <td>
                                    <?php 
                                        if($n['nilai'] >= 80) echo 'A (Sangat Baik)';
                                        elseif($n['nilai'] >= 70) echo 'B (Baik)';
                                        elseif($n['nilai'] >= 60) echo 'C (Cukup)';
                                        else echo 'D/E (Kurang)';
                                    ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Monitoring Absensi -->
            <div class="card">
                <h3>Statistik Kehadiran</h3>
                <?php 
                // Hitung Alpha
                $q_alpha = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM absensi WHERE mahasiswa_nim = '$nim_anak' AND status = 'alpha'");
                $d_alpha = mysqli_fetch_assoc($q_alpha);
                
                // Hitung Hadir
                $q_hadir = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM absensi WHERE mahasiswa_nim = '$nim_anak' AND status = 'hadir'");
                $d_hadir = mysqli_fetch_assoc($q_hadir);
                ?>
                <div style="text-align:center; padding: 20px;">
                    <div style="font-size: 30px; font-weight:bold; color: #28a745;"><?= $d_hadir['total'] ?></div>
                    <small>Total Hadir</small>
                    <hr>
                    <div style="font-size: 30px; font-weight:bold; color: #dc3545;"><?= $d_alpha['total'] ?></div>
                    <small>Total Alpha (Tanpa Keterangan)</small>
                </div>
                <?php if($d_alpha['total'] > 3): ?>
                    <div class="alert alert-danger" style="margin-top:10px;">
                        <strong>Peringatan!</strong> Absensi anak Anda sudah mengkhawatirkan. Mohon hubungi Dosen Wali.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>