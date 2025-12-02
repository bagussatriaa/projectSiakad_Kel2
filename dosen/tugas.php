<<<<<<< Updated upstream
=======
<?php
session_start();
include '../koneksi/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'dosen') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$dosen = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM dosen WHERE user_id = '$user_id'"));
$nidn = $dosen['nidn'];
$prodi_id = $dosen['prodi_id'];

// Proses Tambah Tugas
if (isset($_POST['tambah_tugas'])) {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $mk_id = $_POST['mata_kuliah_id'];
    $deadline = $_POST['deadline']; // Format: YYYY-MM-DDTHH:MM

    $query = "INSERT INTO tugas (judul, deskripsi, deadline, mata_kuliah_id, dosen_nidn) 
              VALUES ('$judul', '$deskripsi', '$deadline', '$mk_id', '$nidn')";
    
    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Tugas berhasil ditambahkan!'); window.location='tugas.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah tugas');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Tugas - Dosen</title>
    <link rel="stylesheet" href="http://localhost/projectSiakad_Kel2/style.css">
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
            <li><a href="input_nilai.php">Input Nilai</a></li>
            <li><a href="tugas.php" class="active">Tugas & Quiz</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Manajemen Tugas Kuliah</h2>
        </div>

        <!-- Form Tambah Tugas -->
        <div class="card">
            <h3>Buat Tugas Baru</h3>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Mata Kuliah</label>
                    <select name="mata_kuliah_id" class="form-control" required>
                        <option value="">-- Pilih Mata Kuliah --</option>
                        <?php
                        $q_mk = mysqli_query($koneksi, "SELECT * FROM mata_kuliah WHERE prodi_id = '$prodi_id'");
                        while($mk = mysqli_fetch_assoc($q_mk)):
                        ?>
                            <option value="<?= $mk['id'] ?>"><?= $mk['nama_mk'] ?> (Smt <?= $mk['semester'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Judul Tugas</label>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Makalah Database" required>
                </div>
                <div class="form-group">
                    <label>Deskripsi / Instruksi</label>
                    <textarea name="deskripsi" class="form-control" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Deadline Pengumpulan</label>
                    <input type="datetime-local" name="deadline" class="form-control" required>
                </div>
                <button type="submit" name="tambah_tugas" class="btn-submit">Simpan Tugas</button>
            </form>
        </div>

        <!-- List Tugas Aktif -->
        <div class="card" style="margin-top: 20px;">
            <h3>Daftar Tugas yang Diberikan</h3>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Judul</th>
                            <th>Deadline</th>
                            <th>Jml Pengumpul</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query ambil tugas + hitung jumlah submission
                        $q_tugas = mysqli_query($koneksi, "SELECT t.*, mk.nama_mk, 
                                                          (SELECT COUNT(*) FROM submission_tugas st WHERE st.tugas_id = t.id) as total_submit
                                                          FROM tugas t 
                                                          JOIN mata_kuliah mk ON t.mata_kuliah_id = mk.id
                                                          WHERE t.dosen_nidn = '$nidn'
                                                          ORDER BY t.deadline DESC");
                        
                        while($row = mysqli_fetch_assoc($q_tugas)):
                        ?>
                        <tr>
                            <td><?= $row['nama_mk'] ?></td>
                            <td><?= $row['judul'] ?></td>
                            <td><?= date('d M Y H:i', strtotime($row['deadline'])) ?></td>
                            <td><?= $row['total_submit'] ?> Mhs</td>
                            <td>
                                <button class="btn-submit" style="padding: 5px 10px; font-size: 12px;">Lihat Pengumpulan</button>
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
>>>>>>> Stashed changes
