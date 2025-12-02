<<<<<<< Updated upstream
=======
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Absensi - Mahasiswa</title>
    <link rel="stylesheet" href="http://localhost/projectSiakad_Kel2/style.css">
</head>
<body>

<div class="dashboard-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>SIAKAD VOKASI</h3>
            <small>Mahasiswa: <?= $mhs['nama'] ?></small>
        </div>
        <ul class="sidebar-menu">
            <li><a href="../dashboard/dashboard_mahasiswa.php">Dashboard</a></li>
            <li><a href="krs.php">Isi KRS</a></li>
            <li><a href="nilai.php">Lihat KHS / Nilai</a></li>
            <li><a href="absensi.php" class="active">Riwayat Absensi</a></li>
            <li><a href="tugas.php">Tugas Saya</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Riwayat Kehadiran</h2>
        </div>

        <div class="card">
            <h3>Rekap Kehadiran Per Mata Kuliah</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Pertemuan Ke</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT a.*, mk.nama_mk 
                                  FROM absensi a
                                  JOIN mata_kuliah mk ON a.mata_kuliah_id = mk.id
                                  WHERE a.mahasiswa_nim = '$nim'
                                  ORDER BY mk.nama_mk ASC, a.pertemuan_ke ASC";
                        
                        $result = mysqli_query($koneksi, $query);
                        
                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                                $status_color = 'badge-secondary';
                                if($row['status'] == 'hadir') $status_color = 'badge-success';
                                elseif($row['status'] == 'alpha') $status_color = 'badge-danger';
                                elseif($row['status'] == 'sakit') $status_color = 'badge-warning';
                        ?>
                        <tr>
                            <td><?= $row['nama_mk'] ?></td>
                            <td>Pertemuan <?= $row['pertemuan_ke'] ?></td>
                            <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                            <td>
                                <span class="badge <?= $status_color ?>"><?= strtoupper($row['status']) ?></span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" align="center">Belum ada data absensi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
>>>>>>> Stashed changes
