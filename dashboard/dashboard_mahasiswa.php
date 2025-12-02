<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'orang_tua') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$ortu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM orang_tua WHERE user_id = '$user_id'"));
$ortu_id = $ortu['id'];

$query_anak = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE orang_tua_id = '$ortu_id'");
$anak = mysqli_fetch_assoc($query_anak);

if (!$anak) {
    echo "<script>alert('Data anak tidak ditemukan!'); window.location='../dashboard/dashboard_ortu.php';</script>";
    exit;
}
$nim_anak = $anak['nim'];

// Hitung Statistik Absensi Real
$q_hadir = mysqli_query($koneksi, "SELECT COUNT(*) as tot FROM absensi WHERE mahasiswa_nim='$nim_anak' AND status='hadir'");
$d_hadir = mysqli_fetch_assoc($q_hadir);

$q_sakit = mysqli_query($koneksi, "SELECT COUNT(*) as tot FROM absensi WHERE mahasiswa_nim='$nim_anak' AND status='sakit'");
$d_sakit = mysqli_fetch_assoc($q_sakit);

$q_ijin = mysqli_query($koneksi, "SELECT COUNT(*) as tot FROM absensi WHERE mahasiswa_nim='$nim_anak' AND status='ijin'");
$d_ijin = mysqli_fetch_assoc($q_ijin);

$q_alpha = mysqli_query($koneksi, "SELECT COUNT(*) as tot FROM absensi WHERE mahasiswa_nim='$nim_anak' AND status='alpha'");
$d_alpha = mysqli_fetch_assoc($q_alpha);

$total_pertemuan = $d_hadir['tot'] + $d_sakit['tot'] + $d_ijin['tot'] + $d_alpha['tot'];
$persentase_hadir = ($total_pertemuan > 0) ? ($d_hadir['tot'] / $total_pertemuan) * 100 : 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Monitoring Anak</title>
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
            <h2>Monitoring: <?= $anak['nama'] ?> (<?= $anak['nim'] ?>)</h2>
        </div>

        <div class="card-container">
            <!-- Card Absensi Ringkas -->
            <div class="card">
                <h3>Kehadiran</h3>
                <div style="display:flex; justify-content:space-around; text-align:center; margin-top:20px;">
                    <div>
                        <h2 style="color:var(--primary); margin:0;"><?= $d_hadir['tot'] ?></h2>
                        <small>Hadir</small>
                    </div>
                    <div>
                        <h2 style="color:var(--warning); margin:0;"><?= $d_sakit['tot'] + $d_ijin['tot'] ?></h2>
                        <small>Ijin/Sakit</small>
                    </div>
                    <div>
                        <h2 style="color:var(--danger); margin:0;"><?= $d_alpha['tot'] ?></h2>
                        <small>Alpha</small>
                    </div>
                </div>
                <hr>
                <p style="text-align:center; font-weight:bold;">Persentase: <?= number_format($persentase_hadir, 1) ?>%</p>
            </div>

            <!-- Card Nilai Ringkas -->
            <div class="card" style="grid-column: span 2;">
                <h3>Transkrip Nilai Sementara</h3>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Mata Kuliah</th>
                                <th>Nilai Akhir</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Ambil Mata Kuliah Anak
                            $q_mk_anak = mysqli_query($koneksi, "SELECT mk.id, mk.nama_mk 
                                                                FROM krs_detail kd 
                                                                JOIN krs k ON kd.krs_id = k.id 
                                                                JOIN mata_kuliah mk ON kd.mata_kuliah_id = mk.id 
                                                                WHERE k.mahasiswa_nim = '$nim_anak' AND k.status = 'disetujui'");
                            
                            $found_nilai = false;
                            while($mk = mysqli_fetch_assoc($q_mk_anak)):
                                $mk_id = $mk['id'];
                                // Hitung Nilai Rata-rata seperti logic mahasiswa
                                $q_n = mysqli_query($koneksi, "SELECT jenis_nilai, AVG(nilai) as rata FROM nilai WHERE mahasiswa_nim='$nim_anak' AND mata_kuliah_id='$mk_id' GROUP BY jenis_nilai");
                                
                                $nt=0; $nq=0; $nut=0; $nua=0;
                                while($n = mysqli_fetch_assoc($q_n)) {
                                    if($n['jenis_nilai'] == 'tugas') $nt = $n['rata'];
                                    if($n['jenis_nilai'] == 'quiz') $nq = $n['rata'];
                                    if($n['jenis_nilai'] == 'uts') $nut = $n['rata'];
                                    if($n['jenis_nilai'] == 'uas') $nua = $n['rata'];
                                }
                                
                                $na = ($nt * 0.2) + ($nq * 0.1) + ($nut * 0.3) + ($nua * 0.4);
                                
                                if($na > 0): $found_nilai = true;
                                    $gr = ($na >= 80) ? 'A' : (($na >= 70) ? 'B' : (($na >= 60) ? 'C' : 'D/E'));
                            ?>
                            <tr>
                                <td><?= $mk['nama_mk'] ?></td>
                                <td><strong><?= number_format($na, 2) ?></strong></td>
                                <td><span class="badge badge-secondary"><?= $gr ?></span></td>
                            </tr>
                            <?php endif; endwhile; ?>
                            
                            <?php if(!$found_nilai): ?>
                            <tr><td colspan="3" align="center">Belum ada nilai yang diinput Dosen.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>