<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'prodi') {
    header("Location: ../auth/login.php");
    exit;
}

// Proses Tambah MK
if (isset($_POST['tambah_mk'])) {
    $kode = $_POST['kode_mk'];
    $nama = $_POST['nama_mk'];
    $sks = $_POST['sks'];
    $smt = $_POST['semester'];
    $prodi = 1; // Default D3 TI (Sesuai ID Prodi di DB)

    $insert = mysqli_query($koneksi, "INSERT INTO mata_kuliah (kode_mk, nama_mk, sks, semester, prodi_id) VALUES ('$kode', '$nama', '$sks', '$smt', '$prodi')");
    
    if ($insert) {
        echo "<script>alert('Mata Kuliah Berhasil Ditambahkan'); window.location='kelola_kurikulum.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Kurikulum</title>
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
            <li><a href="../dashboard/dashboard_prodi.php">Dashboard</a></li>
            <li><a href="kelola_kurikulum.php" class="active">Manajemen Kurikulum</a></li>
            <li><a href="mahasiswa.php">Data Mahasiswa & KRS</a></li>
            <li><a href="laporan.php">Laporan Akademik</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Manajemen Kurikulum & Mata Kuliah</h2>
        </div>

        <!-- Form Tambah MK -->
        <div class="card" style="margin-bottom: 20px;">
            <h3>Tambah Mata Kuliah Baru</h3>
            <form method="POST" action="">
                <div style="display:flex; gap:10px;">
                    <input type="text" name="kode_mk" class="form-control" placeholder="Kode MK (mis: TIK101)" required>
                    <input type="text" name="nama_mk" class="form-control" placeholder="Nama Mata Kuliah" style="flex:2;" required>
                    <input type="number" name="sks" class="form-control" placeholder="SKS" style="width:80px;" required>
                    <input type="number" name="semester" class="form-control" placeholder="Smt" style="width:80px;" required>
                    <button type="submit" name="tambah_mk" class="btn-submit">Tambah</button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar MK -->
        <div class="card">
            <h3>Daftar Mata Kuliah Aktif</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Mata Kuliah</th>
                            <th>SKS</th>
                            <th>Semester</th>
                            <th>Prodi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($koneksi, "SELECT * FROM mata_kuliah ORDER BY semester ASC, kode_mk ASC");
                        while($row = mysqli_fetch_assoc($query)):
                        ?>
                        <tr>
                            <td><?= $row['kode_mk'] ?></td>
                            <td><?= $row['nama_mk'] ?></td>
                            <td><?= $row['sks'] ?></td>
                            <td><?= $row['semester'] ?></td>
                            <td>D3 Teknik Informatika</td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>