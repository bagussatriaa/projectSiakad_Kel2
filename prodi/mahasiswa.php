<?php
session_start();
include '../koneksi/koneksi.php';

// Cek Role Prodi
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'prodi') {
    header("Location: ../auth/login.php");
    exit;
}

// Proses Approve/Tolak KRS
if (isset($_POST['aksi_krs'])) {
    $krs_id = $_POST['krs_id'];
    $status_baru = $_POST['status_baru']; // 'disetujui' atau 'ditolak'
    
    $update = mysqli_query($koneksi, "UPDATE krs SET status = '$status_baru' WHERE id = '$krs_id'");
    
    if ($update) {
        echo "<script>alert('Status KRS berhasil diubah menjadi $status_baru'); window.location='mahasiswa.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Mahasiswa - Prodi</title>
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
            <li><a href="kelola_kurikulum.php">Manajemen Kurikulum</a></li>
            <li><a href="mahasiswa.php" class="active">Data Mahasiswa & KRS</a></li>
            <li><a href="laporan.php">Laporan Akademik</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Data Mahasiswa & Validasi KRS</h2>
        </div>

        <div class="card">
            <h3>Daftar Mahasiswa & Status KRS (Semester Aktif)</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NIM</th>
                            <th>Nama Mahasiswa</th>
                            <th>Angkatan/Prodi</th>
                            <th>Status KRS</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query: Ambil mahasiswa + Status KRS mereka di semester ini (misal semester 3)
                        // Left join karena mungkin ada mahasiswa yang belum isi KRS sama sekali
                        $query = mysqli_query($koneksi, "SELECT m.*, k.id as krs_id, k.status as status_krs 
                                                        FROM mahasiswa m 
                                                        LEFT JOIN krs k ON m.nim = k.mahasiswa_nim AND k.semester = 3
                                                        ORDER BY m.nim ASC");
                        
                        while($row = mysqli_fetch_assoc($query)):
                        ?>
                        <tr>
                            <td><?= $row['nim'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td><?= $row['prodi_id'] ?> (D3 TI)</td>
                            <td>
                                <?php if($row['status_krs']): ?>
                                    <span class="badge 
                                        <?= $row['status_krs'] == 'disetujui' ? 'badge-success' : 
                                           ($row['status_krs'] == 'ditolak' ? 'badge-danger' : 'badge-warning') ?>">
                                        <?= strtoupper($row['status_krs']) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-danger">BELUM ISI</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['status_krs'] == 'diajukan'): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="krs_id" value="<?= $row['krs_id'] ?>">
                                        <input type="hidden" name="status_baru" value="disetujui">
                                        <button type="submit" name="aksi_krs" class="btn-submit" style="padding:5px 10px; font-size:12px;">Approve</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="krs_id" value="<?= $row['krs_id'] ?>">
                                        <input type="hidden" name="status_baru" value="ditolak">
                                        <button type="submit" name="aksi_krs" class="btn-submit" style="background:#dc3545; padding:5px 10px; font-size:12px;">Tolak</button>
                                    </form>
                                <?php elseif($row['status_krs'] == 'disetujui'): ?>
                                    <small>Terverifikasi</small>
                                <?php else: ?>
                                    <small>-</small>
                                <?php endif; ?>
                            </td>
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