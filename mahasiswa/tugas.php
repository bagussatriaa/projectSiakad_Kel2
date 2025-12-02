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

// Proses Upload Tugas
if (isset($_POST['upload_tugas'])) {
    $tugas_id = $_POST['tugas_id'];
    // Simulasi upload file (menyimpan nama file saja di DB untuk prototype)
    // Pada implementasi nyata, gunakan move_uploaded_file()
    $nama_file = "Tugas_" . $nim . "_" . time() . ".pdf"; 
    
    $cek_submit = mysqli_query($koneksi, "SELECT * FROM submission_tugas WHERE tugas_id='$tugas_id' AND mahasiswa_nim='$nim'");
    if(mysqli_num_rows($cek_submit) > 0){
        // Jika sudah ada, update
        $query = "UPDATE submission_tugas SET file_path='$nama_file', submitted_at=NOW() WHERE tugas_id='$tugas_id' AND mahasiswa_nim='$nim'";
    } else {
        // Jika belum, insert
        $query = "INSERT INTO submission_tugas (tugas_id, mahasiswa_nim, file_path) VALUES ('$tugas_id', '$nim', '$nama_file')";
    }

    if (mysqli_query($koneksi, $query)) {
        echo "<script>alert('Tugas berhasil dikumpulkan!'); window.location='tugas.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tugas Saya - Mahasiswa</title>
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
            <li><a href="absensi.php">Riwayat Absensi</a></li>
            <li><a href="tugas.php" class="active">Tugas Saya</a></li>
            <li><a href="../auth/logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h2>Daftar Tugas Kuliah</h2>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Mata Kuliah</th>
                            <th>Judul Tugas</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th>Nilai</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query: Ambil tugas berdasarkan MK yang diambil di KRS (disetujui)
                        $query = "SELECT t.*, mk.nama_mk, st.file_path, st.nilai, st.submitted_at 
                                  FROM tugas t
                                  JOIN mata_kuliah mk ON t.mata_kuliah_id = mk.id
                                  JOIN krs_detail kd ON mk.id = kd.mata_kuliah_id
                                  JOIN krs k ON kd.krs_id = k.id
                                  LEFT JOIN submission_tugas st ON t.id = st.tugas_id AND st.mahasiswa_nim = '$nim'
                                  WHERE k.mahasiswa_nim = '$nim' AND k.status = 'disetujui'
                                  ORDER BY t.deadline ASC";
                        
                        $result = mysqli_query($koneksi, $query);
                        
                        if(mysqli_num_rows($result) > 0):
                            while($row = mysqli_fetch_assoc($result)):
                                $is_submitted = !empty($row['file_path']);
                                $is_late = strtotime($row['deadline']) < time();
                        ?>
                        <tr>
                            <td><?= $row['nama_mk'] ?></td>
                            <td>
                                <strong><?= $row['judul'] ?></strong><br>
                                <small><?= substr($row['deskripsi'], 0, 50) ?>...</small>
                            </td>
                            <td>
                                <?= date('d M Y H:i', strtotime($row['deadline'])) ?>
                                <?php if($is_late && !$is_submitted) echo '<br><span class="badge badge-danger">Telat</span>'; ?>
                            </td>
                            <td>
                                <?php if($is_submitted): ?>
                                    <span class="badge badge-success">Dikumpulkan</span><br>
                                    <small><?= date('d/m H:i', strtotime($row['submitted_at'])) ?></small>
                                <?php else: ?>
                                    <span class="badge badge-warning">Belum</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['nilai'] ? $row['nilai'] : '-' ?></td>
                            <td>
                                <?php if(!$row['nilai']): // Jika sudah dinilai tidak bisa upload ulang ?>
                                    <form method="POST" action="">
                                        <input type="hidden" name="tugas_id" value="<?= $row['id'] ?>">
                                        <!-- Simulasi input file -->
                                        <button type="submit" name="upload_tugas" class="btn-submit" style="font-size:12px;">
                                            <?= $is_submitted ? 'Upload Ulang' : 'Upload File' ?>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    Selesai
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" align="center">Belum ada tugas atau KRS belum disetujui.</td></tr>
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
