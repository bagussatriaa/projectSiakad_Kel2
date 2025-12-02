<?php
session_start();
include '../koneksi/koneksi.php';

// Cek Login & Role Dosen
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil Data Dosen yang login
$username = $_SESSION['username'];
// Karena di tabel users tidak ada NIDN, kita cari via user_id
$user_id = $_SESSION['user_id'];
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM dosen WHERE user_id = '$user_id'"));
$prodi_id = $dosen['prodi_id']; // Asumsi Dosen hanya mengajar di prodinya

// Proses Simpan Nilai
if (isset($_POST['simpan_nilai'])) {
    $mk_id = $_POST['mata_kuliah_id'];
    $jenis = $_POST['jenis_nilai'];
    $deskripsi = $_POST['deskripsi'];
    
    $sukses = 0;
    foreach ($_POST['nilai'] as $nim => $nilai_angka) {
        if ($nilai_angka !== "") { // Hanya simpan jika diisi
            // Cek apakah nilai sudah ada (Update) atau belum (Insert)
            // Untuk simplifikasi prototype, kita insert baru (seharusnya update if exist)
            $query = "INSERT INTO nilai (mahasiswa_nim, mata_kuliah_id, jenis_nilai, nilai, deskripsi) 
                      VALUES ('$nim', '$mk_id', '$jenis', '$nilai_angka', '$deskripsi')";
            if (mysqli_query($koneksi, $query)) {
                $sukses++;
            }
        }
    }
    echo "<script>alert('Berhasil menyimpan $sukses data nilai!'); window.location='input_nilai.php';</script>";
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Nilai - Dosen</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Dosen: <?= $dosen['nama'] ?></small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="../dashboard/dashboard_dosen.php">Dashboard</a></li>
            <li><a href="absensi.php">Kelola Absensi</a></li>
            <li><a href="input_nilai.php" class="active">Input Nilai</a></li>
            <li><a href="tugas.php">Tugas & Quiz</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Input Nilai Mahasiswa</h2>
        </div>

        <!-- Form Pilih Mata Kuliah & Filter -->
        <div class="card">
            <form method="GET" action="">
                <div class="form-group">
                    <label>Pilih Mata Kuliah:</label>
                    <select name="mk_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php
                        // Tampilkan MK sesuai Prodi Dosen
                        $q_mk = mysqli_query($koneksi, "SELECT * FROM mata_kuliah WHERE prodi_id = '$prodi_id'");
                        while($mk = mysqli_fetch_assoc($q_mk)):
                            $selected = (isset($_GET['mk_id']) && $_GET['mk_id'] == $mk['id']) ? 'selected' : '';
                        ?>
                            <option value="<?= $mk['id'] ?>" <?= $selected ?>><?= $mk['nama_mk'] ?> (Smst <?= $mk['semester'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>
        </div>

        <?php if (isset($_GET['mk_id']) && $_GET['mk_id'] != ''): 
            $mk_id_selected = $_GET['mk_id'];
            
            // Ambil Mahasiswa yang mengambil MK ini (Join KRS Detail -> KRS -> Mahasiswa)
            // Filter hanya KRS yang sudah DISETUJUI
            $q_mhs = mysqli_query($koneksi, "SELECT m.nim, m.nama 
                                            FROM krs_detail kd
                                            JOIN krs k ON kd.krs_id = k.id
                                            JOIN mahasiswa m ON k.mahasiswa_nim = m.nim
                                            WHERE kd.mata_kuliah_id = '$mk_id_selected' 
                                            AND k.status = 'disetujui'
                                            ORDER BY m.nim ASC");
        ?>
        <div class="card" style="margin-top: 20px;">
            <h3>Form Penilaian</h3>
            <form method="POST" action="">
                <input type="hidden" name="mata_kuliah_id" value="<?= $mk_id_selected ?>">
                
                <div class="form-group">
                    <label>Jenis Nilai:</label>
                    <select name="jenis_nilai" class="form-control" required>
                        <option value="tugas">Tugas</option>
                        <option value="quiz">Quiz</option>
                        <option value="uts">UTS</option>
                        <option value="uas">UAS</option>
                        <option value="proyek">Proyek</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Deskripsi (Opsional):</label>
                    <input type="text" name="deskripsi" class="form-control" placeholder="Contoh: Tugas Pertemuan 1">
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th width="150">Nilai (0-100)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($q_mhs) > 0): ?>
                                <?php while($mhs = mysqli_fetch_assoc($q_mhs)): ?>
                                <tr>
                                    <td><?= $mhs['nim'] ?></td>
                                    <td><?= $mhs['nama'] ?></td>
                                    <td>
                                        <input type="number" name="nilai[<?= $mhs['nim'] ?>]" class="form-control" min="0" max="100" step="0.01">
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" style="text-align:center; color:red;">
                                        Belum ada mahasiswa yang mengambil mata kuliah ini (atau KRS belum disetujui).
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if(mysqli_num_rows($q_mhs) > 0): ?>
                    <button type="submit" name="simpan_nilai" class="btn-submit">Simpan Nilai</button>
                <?php endif; ?>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>