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

// LOGIC MENGHITUNG NILAI AKHIR & IPK
// 1. Ambil semua MK yang diambil mahasiswa ini
$q_mk = mysqli_query($koneksi, "SELECT mk.id, mk.nama_mk, mk.sks, mk.kode_mk
                               FROM krs_detail kd
                               JOIN krs k ON kd.krs_id = k.id
                               JOIN mata_kuliah mk ON kd.mata_kuliah_id = mk.id
                               WHERE k.mahasiswa_nim = '$nim' AND k.status = 'disetujui'
                               ORDER BY mk.semester ASC");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Transkrip Nilai</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <!-- Sidebar Menu sama seperti sebelumnya -->
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Mahasiswa</small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="../dashboard/dashboard_mahasiswa.php">Dashboard</a></li>
            <li><a href="krs.php">Isi KRS</a></li>
            <li><a href="nilai.php" class="active">Lihat KHS / Nilai</a></li>
            <li><a href="absensi.php">Riwayat Absensi</a></li>
            <li><a href="tugas.php">Tugas Saya</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Transkrip Nilai Sementara (Real-Time)</h2>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Rincian Nilai</th>
                            <th>Nilai Akhir</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_bobot = 0;
                        $total_sks_diambil = 0;
                        $ada_data = false;

                        while($mk = mysqli_fetch_assoc($q_mk)):
                            $ada_data = true;
                            $mk_id = $mk['id'];
                            
                            // 2. Ambil komponen nilai per MK (Tugas, Quiz, UTS, UAS)
                            // Jika ada beberapa tugas, kita rata-ratakan dulu, atau ambil nilai terakhir
                            // Di sini kita ambil rata-rata per kategori jika ada duplikat
                            $q_nilai = mysqli_query($koneksi, "SELECT jenis_nilai, AVG(nilai) as nilai_rata 
                                                              FROM nilai 
                                                              WHERE mahasiswa_nim = '$nim' AND mata_kuliah_id = '$mk_id'
                                                              GROUP BY jenis_nilai");
                            
                            // Inisialisasi Nilai
                            $n_tugas = 0; $n_quiz = 0; $n_uts = 0; $n_uas = 0; $n_proyek = 0;
                            
                            while($n = mysqli_fetch_assoc($q_nilai)) {
                                if($n['jenis_nilai'] == 'tugas') $n_tugas = $n['nilai_rata'];
                                if($n['jenis_nilai'] == 'quiz') $n_quiz = $n['nilai_rata'];
                                if($n['jenis_nilai'] == 'uts') $n_uts = $n['nilai_rata'];
                                if($n['jenis_nilai'] == 'uas') $n_uas = $n['nilai_rata'];
                                if($n['jenis_nilai'] == 'proyek') $n_proyek = $n['nilai_rata'];
                            }

                            // 3. Rumus Hitung Nilai Akhir (Contoh Bobot)
                            // Tugas 20%, Quiz 10%, UTS 30%, UAS 40%
                            // Jika mata kuliah Proyek, bobot beda. Kita pakai standar umum saja dulu.
                            $nilai_akhir = ($n_tugas * 0.2) + ($n_quiz * 0.1) + ($n_uts * 0.3) + ($n_uas * 0.4);
                            
                            // Konversi ke Grade
                            if ($nilai_akhir >= 80) { $grade = 'A'; $bobot = 4.0; }
                            elseif ($nilai_akhir >= 75) { $grade = 'B+'; $bobot = 3.5; }
                            elseif ($nilai_akhir >= 70) { $grade = 'B'; $bobot = 3.0; }
                            elseif ($nilai_akhir >= 65) { $grade = 'C+'; $bobot = 2.5; }
                            elseif ($nilai_akhir >= 60) { $grade = 'C'; $bobot = 2.0; }
                            elseif ($nilai_akhir >= 50) { $grade = 'D'; $bobot = 1.0; }
                            else { $grade = 'E'; $bobot = 0; }

                            // Jika belum ada nilai sama sekali
                            if ($nilai_akhir == 0) { $grade = '-'; $bobot = 0; }

                            $total_sks_diambil += $mk['sks'];
                            $total_bobot += ($bobot * $mk['sks']);
                        ?>
                        <tr>
                            <td><?= $mk['kode_mk'] ?></td>
                            <td><?= $mk['nama_mk'] ?></td>
                            <td><?= $mk['sks'] ?></td>
                            <td>
                                <small>
                                    Tugas: <?= number_format($n_tugas,1) ?> | Quiz: <?= number_format($n_quiz,1) ?><br>
                                    UTS: <?= number_format($n_uts,1) ?> | UAS: <?= number_format($n_uas,1) ?>
                                </small>
                            </td>
                            <td><strong><?= number_format($nilai_akhir, 2) ?></strong></td>
                            <td>
                                <span class="badge <?= ($grade == 'A' || $grade == 'B') ? 'badge-success' : (($grade == 'E' || $grade == 'D') ? 'badge-danger' : 'badge-warning') ?>">
                                    <?= $grade ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                    <tfoot>
                        <?php if($ada_data && $total_sks_diambil > 0): 
                            $ipk = $total_bobot / $total_sks_diambil;
                        ?>
                        <tr style="background-color: #f8f9fa;">
                            <td colspan="2" align="right"><strong>Total SKS:</strong></td>
                            <td><strong><?= $total_sks_diambil ?></strong></td>
                            <td colspan="2" align="right"><strong>Indeks Prestasi (IP) Sementara:</strong></td>
                            <td><strong style="font-size:16px; color:var(--primary);"><?= number_format($ipk, 2) ?></strong></td>
                        </tr>
                        <?php else: ?>
                        <tr><td colspan="6" align="center">Belum ada mata kuliah yang diambil (KRS belum disetujui).</td></tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>