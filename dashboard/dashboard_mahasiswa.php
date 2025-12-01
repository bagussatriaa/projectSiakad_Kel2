<?php
// dashboard_mahasiswa.php
// Dummy data sementara (nanti diganti oleh BE)
$nama = "Andi Pratama";
$nim = "23123456";
$prodi = "D3 Teknik Informatika";
$semester = "4";
$ipk = "3.58";
$total_sks = "78";
$kehadiran_pct = 18;
$recent_activity = [
    ["text" => "Mengumpulkan tugas: Dashboard Web", "time" => "2 jam lalu"],
    ["text" => "Mengisi KRS semester genap", "time" => "1 hari lalu"],
    ["text" => "Mengisi absensi: Pemrograman Web (Pert 3)", "time" => "3 hari lalu"],
];
?>

<?php $page = 'dashboard'; ?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Dashboard Mahasiswa - SIAKAD</title>

  <!-- Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Global & Page CSS -->
  <link rel="stylesheet" href="../assets/css/dashboardMahasiswa.css">
  <link rel="stylesheet" href="../assets/css/global.css">
  <link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

<div class="dashboard-wrapper">
  <!-- SIDEBAR -->
  <?php include '../components/sidebar.php'; ?>


  <!-- MAIN -->
  <main class="main">

    <!-- header top -->
    <div class="d-flex justify-content-between align-items-center mb-4">
      <div>
        <h4 class="mb-1">Halo, <?= htmlspecialchars($nama) ?> 👋</h4>
        <div class="text-muted small"><?= htmlspecialchars($nim) ?> • <?= htmlspecialchars($prodi) ?> • Semester <?= htmlspecialchars($semester) ?></div>
      </div>

      <div class="d-flex align-items-center gap-3">
        <!-- profile avatar simple -->
        <div class="profile-avatar text-center">
          <div class="avatar-circle"><?= strtoupper(substr($nama,0,1)) ?></div>
        </div>
      </div>
    </div>

    <!-- Info Cards -->
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
        <div class="card info-card p-3">
          <small class="text-muted">Total SKS</small>
          <div class="fs-5 fw-bold"><?= $total_sks ?></div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card info-card p-3">
          <small class="text-muted">IPK</small>
          <div class="fs-5 fw-bold"><?= $ipk ?></div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card info-card p-3">
          <small class="text-muted">Kehadiran</small>
          <div class="d-flex align-items-center justify-content-between">
            <div class="fs-5 fw-bold"><?= $kehadiran_pct ?>%</div>
            <div style="width:45%;">
              <div class="progress" style="height:8px;">
                <div class="progress-bar" role="progressbar" style="width: <?= $kehadiran_pct ?>%;" aria-valuenow="<?= $kehadiran_pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-6 col-md-3">
        <div class="card info-card p-3">
          <small class="text-muted">Pengumuman</small>
          <div class="fs-6">UTS dimulai 7 hari lagi</div>
        </div>
      </div>
    </div>

    <!-- Shortcut Menu -->
    <div class="card p-3 mb-4 card-custom">
      <div class="row gx-3 gy-3">
        <div class="col-6 col-md-4 col-lg-2">
          <a href="krs.php" class="shortcut-card d-flex flex-column align-items-start p-3">
            <div class="shortcut-icon">📚</div>
            <div class="fw-semibold mt-2">KRS</div>
            <small class="text-muted">Ambil / lihat MK</small>
          </a>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <a href="absensi.php" class="shortcut-card d-flex flex-column align-items-start p-3">
            <div class="shortcut-icon">📝</div>
            <div class="fw-semibold mt-2">Absensi</div>
            <small class="text-muted">Isi & riwayat</small>
          </a>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <a href="nilai.php" class="shortcut-card d-flex flex-column align-items-start p-3">
            <div class="shortcut-icon">📈</div>
            <div class="fw-semibold mt-2">Nilai</div>
            <small class="text-muted">IPK & KHS</small>
          </a>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <a href="tugas.php" class="shortcut-card d-flex flex-column align-items-start p-3">
            <div class="shortcut-icon">📥</div>
            <div class="fw-semibold mt-2">Tugas</div>
            <small class="text-muted">Submit & status</small>
          </a>
        </div>

        <div class="col-6 col-md-4 col-lg-2">
          <a href="prestasi.php" class="shortcut-card d-flex flex-column align-items-start p-3">
            <div class="shortcut-icon">🏆</div>
            <div class="fw-semibold mt-2">Prestasi</div>
            <small class="text-muted">Input & lihat</small>
          </a>
        </div>

        
      </div>
    </div>

    <!-- Activity / Reminder -->
    <div class="row">
      <div class="col-lg-7">
        <div class="card p-3 card-custom mb-4">
          <h6 class="mb-3">Aktivitas Terbaru</h6>
          <ul class="list-unstyled mb-0">
            <?php foreach($recent_activity as $act): ?>
              <li class="py-2 border-bottom">
                <div class="d-flex justify-content-between">
                  <div><?= htmlspecialchars($act['text']) ?></div>
                  <div class="text-muted small"><?= htmlspecialchars($act['time']) ?></div>
                </div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="card p-3 card-custom mb-4">
          <h6 class="mb-3">Reminder</h6>
          <div class="mb-2">🔔 <strong>Belum mengisi absensi hari ini</strong></div>
          <div class="mb-3 text-muted">Jangan lupa isi absensi untuk mata kuliah Pemrograman Web.</div>

          <a href="absensi.php" class="btn btn-custom btn-sm">Isi Absensi Sekarang</a>
        </div>
      </div>
    </div>

    <footer class="mt-4 text-muted small">
      © 2025 SIAKAD Vokasi
    </footer>

  </main>
  </div>

  <!-- Bootstrap JS (opsional untuk interaksi) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
