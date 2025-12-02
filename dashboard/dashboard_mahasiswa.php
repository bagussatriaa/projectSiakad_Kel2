<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$mhs = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE user_id = '$user_id'"));
$nim = $mhs['nim'];

// --- 1. DATA AKADEMIK DASAR ---
$q_krs_latest = mysqli_query($koneksi, "SELECT * FROM krs WHERE mahasiswa_nim = '$nim' ORDER BY semester DESC LIMIT 1");
$data_krs_latest = mysqli_fetch_assoc($q_krs_latest);
$krs_status = $data_krs_latest ? $data_krs_latest['status'] : 'belum_isi';
$semester_aktif = $data_krs_latest ? $data_krs_latest['semester'] : 'N/A';

// --- 2. HITUNG SKS LULUS & IPK SEMENTARA ---
$total_bobot = 0;
$total_sks_diambil = 0;
$total_sks_lulus = 0;

$q_mk_diambil = mysqli_query($koneksi, "SELECT mk.id, mk.sks FROM krs_detail kd 
                                       JOIN krs k ON kd.krs_id = k.id
                                       JOIN mata_kuliah mk ON kd.mata_kuliah_id = mk.id
                                       WHERE k.mahasiswa_nim = '$nim' AND k.status = 'disetujui'");

while($mk = mysqli_fetch_assoc($q_mk_diambil)) {
    $mk_id = $mk['id'];
    $sks = $mk['sks'];
    
    // Ambil Rata-rata Nilai Komponen
    $q_nilai_comp = mysqli_query($koneksi, "SELECT jenis_nilai, AVG(nilai) as rata FROM nilai WHERE mahasiswa_nim = '$nim' AND mata_kuliah_id = '$mk_id' GROUP BY jenis_nilai");
    $nt=0; $nq=0; $nut=0; $nua=0;
    while($n = mysqli_fetch_assoc($q_nilai_comp)) {
        if($n['jenis_nilai'] == 'tugas') $nt = $n['rata'];
        if($n['jenis_nilai'] == 'quiz') $nq = $n['rata'];
        if($n['jenis_nilai'] == 'uts') $nut = $n['rata'];
        if($n['jenis_nilai'] == 'uas') $nua = $n['rata'];
    }
    
    // Hitung Nilai Akhir (Contoh: T20, Q10, UT30, UA40)
    $nilai_akhir = ($nt * 0.2) + ($nq * 0.1) + ($nut * 0.3) + ($nua * 0.4);
    
    // Konversi ke Bobot
    $bobot = 0;
    if ($nilai_akhir >= 80) { $bobot = 4.0; }
    elseif ($nilai_akhir >= 70) { $bobot = 3.0; }
    elseif ($nilai_akhir >= 60) { $bobot = 2.0; }
    else { $bobot = 0; }
    
    // Jika ada nilai (minimal UTS/UAS terisi)
    if ($nut > 0 || $nua > 0) {
        $total_sks_diambil += $sks;
        $total_bobot += ($bobot * $sks);

        if ($bobot >= 2.0) { // Lulus jika grade C ke atas
            $total_sks_lulus += $sks;
        }
    }
}

$ipk_sementara = ($total_sks_diambil > 0) ? ($total_bobot / $total_sks_diambil) : 0;

// --- 3. HITUNG TUGAS PENDING ---
$q_tugas_pending = mysqli_query($koneksi, "SELECT COUNT(*) as total 
    FROM tugas t
    JOIN krs_detail kd ON t.mata_kuliah_id = kd.mata_kuliah_id
    JOIN krs k ON kd.krs_id = k.id
    WHERE k.mahasiswa_nim = '$nim' AND k.status = 'disetujui'
    AND t.id NOT IN (SELECT tugas_id FROM submission_tugas WHERE mahasiswa_nim = '$nim')");

$d_pending = mysqli_fetch_assoc($q_tugas_pending);
$tugas_pending = $d_pending['total'];

// --- 4. RANGKUMAN KEHADIRAN ---
$q_absensi = mysqli_query($koneksi, "SELECT COUNT(*) as total_absensi, 
                                      SUM(CASE WHEN status = 'hadir' THEN 1 ELSE 0 END) as total_hadir 
                                      FROM absensi WHERE mahasiswa_nim = '$nim'");
$d_absensi = mysqli_fetch_assoc($q_absensi);
$total_absensi = $d_absensi['total_absensi'];
$total_hadir = $d_absensi['total_hadir'];
$persen_hadir = ($total_absensi > 0) ? ($total_hadir / $total_absensi) * 100 : 0;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Mahasiswa</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Mahasiswa: <?= $mhs['nama'] ?></small>
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
            <h2>Dashboard Akademik</h2>
            <a href="../auth/logout.php" class="btn-logout">Logout</a>
        </div>
        
        <!-- Peringatan KRS/Umum -->
        <?php if ($krs_status == 'diajukan'): ?>
            <div class="alert alert-warning">
                KRS Semester <?= $semester_aktif ?> Anda masih **MENUNGGU PERSETUJUAN** Program Studi. Silakan hubungi Kaprodi.
            </div>
        <?php elseif ($krs_status == 'ditolak'): ?>
            <div class="alert alert-danger">
                KRS Semester <?= $semester_aktif ?> Anda **DITOLAK**. Silakan periksa kembali di menu KRS.
            </div>
        <?php elseif ($krs_status == 'belum_isi'): ?>
            <div class="alert alert-danger">
                Anda **BELUM MENGISI KRS** untuk semester ini. Segera isi KRS agar bisa mengikuti kuliah dan mendapat nilai.
            </div>
        <?php endif; ?>

        <div class="card-container">
            <!-- 1. KARTU IPK & SKS -->
            <div class="card">
                <h3 style="color:var(--primary);">Indeks Prestasi Kumulatif (IPK)</h3>
                <h1 style="font-size: 50px; margin: 5px 0 15px 0; color: var(--dark);">
                    <?= number_format($ipk_sementara, 2) ?>
                </h1>
                <p>Semester Aktif: <strong><?= $semester_aktif ?></strong></p>
                <p>SKS Lulus: <strong><?= $total_sks_lulus ?></strong> / SKS Total: <strong><?= $total_sks_diambil ?></strong></p>
            </div>
            
            <!-- 2. KARTU TUGAS PENDING -->
            <div class="card">
                <h3 style="color:var(--dark);">Tugas yang Belum Dikumpulkan</h3>
                <h1 style="font-size: 50px; margin: 5px 0 15px 0; color: <?= $tugas_pending > 0 ? 'var(--danger)' : 'var(--primary)' ?>;">
                    <?= $tugas_pending ?>
                </h1>
                <p>Total tugas menanti deadline.</p>
                <?php if($tugas_pending > 0): ?>
                    <a href="../mahasiswa/tugas.php" class="badge badge-danger">Cek Tugas Sekarang</a>
                <?php else: ?>
                    <span class="badge badge-success">Tidak Ada Tugas Pending!</span>
                <?php endif; ?>
            </div>

            <!-- 3. KARTU RANGKUMAN KEHADIRAN -->
            <div class="card">
                <h3 style="color:var(--dark);">Persentase Kehadiran</h3>
                <h1 style="font-size: 50px; margin: 5px 0 15px 0; color: <?= $persen_hadir < 75 ? 'var(--warning)' : 'var(--primary)' ?>;">
                    <?= number_format($persen_hadir, 1) ?>%
                </h1>
                <p>Total Hadir: <?= $total_hadir ?> dari <?= $total_absensi ?> Pertemuan</p>
                <?php if($persen_hadir < 75 && $total_absensi > 0): ?>
                    <span class="badge badge-warning">Waspada! Dibawah batas aman 75%.</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>