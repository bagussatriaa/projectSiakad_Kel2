<?php
session_start();
include '../koneksi/koneksi.php';

// Cek Login & Role Dosen
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM dosen WHERE user_id = '$user_id'"));
$prodi_id = $dosen['prodi_id'];

// Proses Simpan Absensi
if (isset($_POST['simpan_absensi'])) {
    $mk_id = $_POST['mata_kuliah_id'];
    $pertemuan = $_POST['pertemuan_ke'];
    $tanggal = $_POST['tanggal'];
    
    $sukses = 0;
    foreach ($_POST['status'] as $nim => $status_kehadiran) {
        $query = "INSERT INTO absensi (pertemuan_ke, tanggal, mahasiswa_nim, mata_kuliah_id, status) 
                  VALUES ('$pertemuan', '$tanggal', '$nim', '$mk_id', '$status_kehadiran')";
        if (mysqli_query($koneksi, $query)) {
            $sukses++;
        }
    }
    echo "<script>alert('Absensi pertemuan ke-$pertemuan berhasil disimpan!'); window.location='absensi.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi - Dosen</title>
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
            <li><a href="absensi.php" class="active">Kelola Absensi</a></li>
            <li><a href="input_nilai.php">Input Nilai</a></li>
            <li><a href="tugas.php">Tugas & Quiz</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Kelola Absensi Kelas</h2>
        </div>

        <div class="card">
            <form method="GET" action="">
                <div class="form-group">
                    <label>Pilih Mata Kuliah:</label>
                    <select name="mk_id" class="form-control" onchange="this.form.submit()">
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php
                        $q_mk = mysqli_query($koneksi, "SELECT * FROM mata_kuliah WHERE prodi_id = '$prodi_id'");
                        while($mk = mysqli_fetch_assoc($q_mk)):
                            $selected = (isset($_GET['mk_id']) && $_GET['mk_id'] == $mk['id']) ? 'selected' : '';
                        ?>
                            <option value="<?= $mk['id'] ?>" <?= $selected ?>><?= $mk['nama_mk'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </form>
        </div>

        <?php if (isset($_GET['mk_id']) && $_GET['mk_id'] != ''): 
             $mk_id_selected = $_GET['mk_id'];
             $q_mhs = mysqli_query($koneksi, "SELECT m.nim, m.nama 
                                             FROM krs_detail kd
                                             JOIN krs k ON kd.krs_id = k.id
                                             JOIN mahasiswa m ON k.mahasiswa_nim = m.nim
                                             WHERE kd.mata_kuliah_id = '$mk_id_selected' 
                                             AND k.status = 'disetujui'
                                             ORDER BY m.nim ASC");
        ?>
        <div class="card" style="margin-top: 20px;">
            <h3>Input Kehadiran</h3>
            <form method="POST" action="">
                <input type="hidden" name="mata_kuliah_id" value="<?= $mk_id_selected ?>">
                
                <div style="display: flex; gap: 20px;">
                    <div class="form-group" style="flex:1;">
                        <label>Pertemuan Ke-</label>
                        <select name="pertemuan_ke" class="form-control" required>
                            <?php for($i=1; $i<=16; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="form-group" style="flex:1;">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>NIM</th>
                                <th>Nama</th>
                                <th>Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(mysqli_num_rows($q_mhs) > 0): ?>
                                <?php while($mhs = mysqli_fetch_assoc($q_mhs)): ?>
                                <tr>
                                    <td><?= $mhs['nim'] ?></td>
                                    <td><?= $mhs['nama'] ?></td>
                                    <td>
                                        <select name="status[<?= $mhs['nim'] ?>]" class="form-control">
                                            <option value="hadir">Hadir</option>
                                            <option value="ijin">Ijin</option>
                                            <option value="sakit">Sakit</option>
                                            <option value="alpha">Alpha</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="3">Data mahasiswa tidak ditemukan (Cek Status KRS).</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if(mysqli_num_rows($q_mhs) > 0): ?>
                    <button type="submit" name="simpan_absensi" class="btn-submit">Simpan Absensi</button>
                <?php endif; ?>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>