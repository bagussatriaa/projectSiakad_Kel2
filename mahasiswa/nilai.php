<<<<<<< Updated upstream
=======
<?php
session_start();
include '../koneksi/koneksi.php';

// Cek Login & Role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query_mhs = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE user_id = '$user_id'");
$mhs = mysqli_fetch_assoc($query_mhs);
$nim = $mhs['nim'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Nilai Mahasiswa</title>
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
            <h2>Kartu Hasil Studi (KHS)</h2>
        </div>

        <div class="card">
            <h3>Transkrip Nilai Sementara</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Tugas</th>
                            <th>Quiz</th>
                            <th>UTS</th>
                            <th>UAS</th>
                            <th>Nilai Akhir</th>
                            <th>Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query Logic: Mengambil data nilai per mata kuliah
                        // Catatan: Ini contoh query sederhana. Idealnya perlu di-group by mata kuliah jika struktur tabel 'nilai' menyimpan per komponen.
                        // Di sini kita asumsikan 1 row di tabel 'nilai' mewakili 1 komponen, jadi kita perlu logika agregasi atau tampilan per komponen.
                        // Untuk simplifikasi, kita tampilkan list komponen nilai yang ada.
                        
                        $q_nilai = mysqli_query($koneksi, "SELECT n.*, mk.nama_mk, mk.sks 
                                                          FROM nilai n 
                                                          JOIN mata_kuliah mk ON n.mata_kuliah_id = mk.id 
                                                          WHERE n.mahasiswa_nim = '$nim'
                                                          ORDER BY mk.nama_mk ASC");
                        
                        if(mysqli_num_rows($q_nilai) > 0):
                            while($row = mysqli_fetch_assoc($q_nilai)):
                        ?>
                        <tr>
                            <td><?= $row['nama_mk'] ?></td>
                            <!-- Karena struktur tabel nilai 'jenis_nilai' terpisah per baris, tampilan ini menampilkan per item penilaian -->
                            <td colspan="4">
                                <?= strtoupper($row['jenis_nilai']) ?>: <?= $row['nilai'] ?>
                            </td>
                            <td><?= $row['nilai'] ?></td>
                            <td>
                                <?php 
                                    $val = $row['nilai'];
                                    if($val >= 80) echo 'A';
                                    elseif($val >= 70) echo 'B';
                                    elseif($val >= 60) echo 'C';
                                    elseif($val >= 50) echo 'D';
                                    else echo 'E';
                                ?>
                            </td>
                        </tr>
                        <?php endwhile; 
                        else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">Belum ada data nilai.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <p><small>*Nilai ditampilkan per komponen penilaian yang sudah diinput Dosen.</small></p>
        </div>
    </div>
</div>

</body>
</html>
>>>>>>> Stashed changes
