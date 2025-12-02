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

// Ambil Data Mahasiswa
$user_id = $_SESSION['user_id'];
$query_mhs = mysqli_query($koneksi, "SELECT * FROM mahasiswa WHERE user_id = '$user_id'");
$mhs = mysqli_fetch_assoc($query_mhs);
$nim = $mhs['nim'];

// Proses Simpan KRS
if (isset($_POST['simpan_krs'])) {
    $tahun_akademik = '2023/2024'; // Hardcode sementara atau ambil dari setting
    $semester = 3; // Contoh semester aktif
    
    // 1. Buat Header KRS
    $insert_krs = mysqli_query($koneksi, "INSERT INTO krs (mahasiswa_nim, semester, tahun_akademik, status) VALUES ('$nim', '$semester', '$tahun_akademik', 'diajukan')");
    
    if ($insert_krs) {
        $krs_id = mysqli_insert_id($koneksi);
        
        // 2. Masukkan Detail Mata Kuliah
        if (!empty($_POST['mk'])) {
            foreach ($_POST['mk'] as $mk_id) {
                mysqli_query($koneksi, "INSERT INTO krs_detail (krs_id, mata_kuliah_id) VALUES ('$krs_id', '$mk_id')");
            }
        }
        echo "<script>alert('KRS Berhasil diajukan!'); window.location='krs.php';</script>";
    } else {
        echo "<script>alert('Gagal mengajukan KRS');</script>";
    }
}

// Cek Status KRS Saat Ini (Contoh Semester 3)
$cek_krs = mysqli_query($koneksi, "SELECT * FROM krs WHERE mahasiswa_nim = '$nim' AND semester = 3");
$data_krs = mysqli_fetch_assoc($cek_krs);
$sudah_krs = mysqli_num_rows($cek_krs) > 0;

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>KRS Mahasiswa</title>
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
            <li><a href="krs.php" class="active">Isi KRS</a></li>
            <li><a href="nilai.php">Lihat KHS / Nilai</a></li>
            <li><a href="absensi.php">Riwayat Absensi</a></li>
            <li><a href="tugas.php">Tugas Saya</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Kartu Rencana Studi (KRS)</h2>
        </div>

        <?php if ($sudah_krs): ?>
            <div class="card">
                <h3>Status KRS: <span class="badge badge-warning"><?= strtoupper($data_krs['status']) ?></span></h3>
                <p>Anda sudah mengisi KRS untuk semester ini.</p>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Kode MK</th>
                                <th>Mata Kuliah</th>
                                <th>SKS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $id_krs = $data_krs['id'];
                            $q_detail = mysqli_query($koneksi, "SELECT mk.* FROM krs_detail kd 
                                                               JOIN mata_kuliah mk ON kd.mata_kuliah_id = mk.id 
                                                               WHERE kd.krs_id = '$id_krs'");
                            $total_sks = 0;
                            while($row = mysqli_fetch_assoc($q_detail)):
                                $total_sks += $row['sks'];
                            ?>
                            <tr>
                                <td><?= $row['kode_mk'] ?></td>
                                <td><?= $row['nama_mk'] ?></td>
                                <td><?= $row['sks'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                            <tr>
                                <td colspan="2"><strong>Total SKS</strong></td>
                                <td><strong><?= $total_sks ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            
            <div class="card">
                <h3>Form Pengisian KRS</h3>
                <p>Silakan pilih mata kuliah yang akan diambil.</p>
                <form action="" method="POST">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="50">Pilih</th>
                                    <th>Kode MK</th>
                                    <th>Mata Kuliah</th>
                                    <th>SKS</th>
                                    <th>Semester</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Tampilkan Semua MK (Seharusnya difilter berdasarkan kurikulum prodi)
                                $q_mk = mysqli_query($koneksi, "SELECT * FROM mata_kuliah ORDER BY semester ASC");
                                while($mk = mysqli_fetch_assoc($q_mk)):
                                ?>
                                <tr>
                                    <td><input type="checkbox" name="mk[]" value="<?= $mk['id'] ?>"></td>
                                    <td><?= $mk['kode_mk'] ?></td>
                                    <td><?= $mk['nama_mk'] ?></td>
                                    <td><?= $mk['sks'] ?></td>
                                    <td><?= $mk['semester'] ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <button type="submit" name="simpan_krs" class="btn-submit">Simpan KRS</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
>>>>>>> Stashed changes
