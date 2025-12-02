<?php
session_start();
include '../koneksi/koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'orang_tua') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$ortu = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM orang_tua WHERE user_id = '$user_id'"));
$ortu_id = $ortu['id'];

// Cari data Mahasiswa (Anak) yang terkait dengan Orang Tua ini
$query_anak = mysqli_query($koneksi, "SELECT m.*, p.nama_prodi FROM mahasiswa m JOIN prodi p ON m.prodi_id = p.id WHERE m.orang_tua_id = '$ortu_id'");
$anak = mysqli_fetch_assoc($query_anak);

if (!$anak) {
    // Jika tidak ada data anak terdaftar
    $nama_anak = 'Data Anak Belum Terdaftar';
    $status_mhs = 'N/A';
    $nim_anak = '';
} else {
    $nama_anak = $anak['nama'];
    $nim_anak = $anak['nim'];
    $prodi_anak = $anak['nama_prodi'];
    $status_mhs = 'Aktif (D3 TI Vokasi)';

    // --- LOGIC STATISTIK ANAK ---
    
    // 1. IPK Sementara Anak (Sama dengan logic di mahasiswa/nilai.php)
    $q_mk_diambil = mysqli_query($koneksi, "SELECT mk.id, mk.sks FROM krs_detail kd 
                                           JOIN krs k ON kd.krs_id = k.id
                                           JOIN mata_kuliah mk ON kd.mata_kuliah_id = mk.id
                                           WHERE k.mahasiswa_nim = '$nim_anak' AND k.status = 'disetujui'");
    
    $total_bobot = 0;
    $total_sks_diambil = 0;
    while($mk = mysqli_fetch_assoc($q_mk_diambil)) {
        $mk_id = $mk['id'];
        $sks = $mk['sks'];
        
        $q_nilai_comp = mysqli_query($koneksi, "SELECT jenis_nilai, AVG(nilai) as rata FROM nilai WHERE mahasiswa_nim = '$nim_anak' AND mata_kuliah_id = '$mk_id' GROUP BY jenis_nilai");
        $nt=0; $nq=0; $nut=0; $nua=0;
        while($n = mysqli_fetch_assoc($q_nilai_comp)) {
            if($n['jenis_nilai'] == 'tugas') $nt = $n['rata'];
            if($n['jenis_nilai'] == 'quiz') $nq = $n['rata'];
            if($n['jenis_nilai'] == 'uts') $nut = $n['rata'];
            if($n['jenis_nilai'] == 'uas') $nua = $n['rata'];
        }
        
        $nilai_akhir = ($nt * 0.2) + ($nq * 0.1) + ($nut * 0.3) + ($nua * 0.4);
        $bobot = 0;
        if ($nilai_akhir >= 80) $bobot = 4.0;
        elseif ($nilai_akhir >= 70) $bobot = 3.0;
        elseif ($nilai_akhir >= 60) $bobot = 2.0;

        if ($nilai_akhir > 0) {
            $total_sks_diambil += $sks;
            $total_bobot += ($bobot * $sks);
        }
    }
    $ipk_anak = ($total_sks_diambil > 0) ? $total_bobot / $total_sks_diambil : 0;


    // 2. Data Kehadiran Terakhir
    $q_last_absen = mysqli_query($koneksi, "SELECT a.tanggal, a.status, mk.nama_mk 
                                            FROM absensi a 
                                            JOIN mata_kuliah mk ON a.mata_kuliah_id = mk.id 
                                            WHERE a.mahasiswa_nim = '$nim_anak'
                                            ORDER BY a.tanggal DESC LIMIT 1");
    $last_absen = mysqli_fetch_assoc($q_last_absen);

    // 3. Statistik Alpha (Peringatan Dini)
    $q_alpha = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM absensi WHERE mahasiswa_nim = '$nim_anak' AND status = 'alpha'");
    $d_alpha = mysqli_fetch_assoc($q_alpha);
    $total_alpha = $d_alpha['total'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Orang Tua</title>
    <link rel="stylesheet" href="../style.css">
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
            <h2>Portal Monitoring Akademik Wali</h2>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </div>

        <?php if (!$anak): ?>
             <div class="alert alert-danger">
                Akun ini belum terhubung ke data mahasiswa manapun. Silakan hubungi Program Studi.
            </div>
        <?php else: ?>

            <div class="card">
                <h3>Informasi Anak</h3>
                <p>Nama Mahasiswa: <strong><?= $nama_anak ?> (<?= $nim_anak ?>)</strong></p>
                <p>Program Studi: <strong><?= $prodi_anak ?></strong></p>
                <p>Status: <span class="badge badge-success"><?= $status_mhs ?></span></p>
            </div>

            <div class="card-container">
                <!-- Card 1: IPK Anak -->
                <div class="card">
                    <h3 style="color:var(--primary);">IPK Sementara</h3>
                    <h1 style="font-size: 50px; margin: 5px 0 15px 0; color: var(--dark);">
                        <?= number_format($ipk_anak, 2) ?>
                    </h1>
                    <p>Total SKS Dinilai: <?= $total_sks_diambil ?></p>
                    <a href="../orangTua/monitoring.php" class="badge badge-primary">Lihat Detail Nilai</a>
                </div>

                <!-- Card 2: Kehadiran Terakhir -->
                <div class="card">
                    <h3 style="color:var(--dark);">Kehadiran Terakhir</h3>
                    <?php if ($last_absen): 
                        $status_color = ($last_absen['status'] == 'hadir') ? 'badge-success' : (($last_absen['status'] == 'alpha') ? 'badge-danger' : 'badge-warning');
                    ?>
                        <p style="margin-bottom: 5px;">Mata Kuliah: <strong><?= $last_absen['nama_mk'] ?></strong></p>
                        <p style="margin-bottom: 15px;">Tanggal: <?= date('d M Y', strtotime($last_absen['tanggal'])) ?></p>
                        Status: <span class="badge <?= $status_color ?>"><?= strtoupper($last_absen['status']) ?></span>
                    <?php else: ?>
                        <p>Belum ada data absensi yang tercatat.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Card 3: Peringatan Dini Alpha -->
                <div class="card">
                    <h3 style="color:var(--danger);">Peringatan Alpha</h3>
                    <h1 style="font-size: 50px; margin: 5px 0 15px 0; color: <?= $total_alpha > 3 ? 'var(--danger)' : 'var(--warning)' ?>;">
                        <?= $total_alpha ?>
                    </h1>
                    <p>Total ketidakhadiran tanpa keterangan.</p>
                    <?php if ($total_alpha > 3): ?>
                        <span class="badge badge-danger">Wajib Hubungi Dosen Wali!</span>
                    <?php else: ?>
                        <span class="badge badge-success">Kehadiran Baik</span>
                    <?php endif; ?>
                </div>

            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>