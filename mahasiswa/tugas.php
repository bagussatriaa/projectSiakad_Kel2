<?php $page = 'tugas'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>SIAKAD - Tugas</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- GLOBAL CSS -->
    <link rel="stylesheet" href="../assets/css/global.css">

    <style>
        .card-custom{
            background:#fff;
            border-radius:16px;
            box-shadow:0 4px 12px rgba(0,0,0,.05);
        }

        .btn-custom{
            background:#9AB447;
            color:#fff;
            border:none;
        }

        .btn-custom:hover{
            background:#7f983a;
            color:#fff;
        }

        th{
            background:#F5F5DC !important;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<?php include '../components/sidebar.php'; ?>

<!-- MAIN CONTENT -->
<div class="main">

    <h3 class="mb-4">Tugas Mahasiswa 📚</h3>

    <!-- FORM UPLOAD TUGAS -->
    <div class="card card-custom p-4 mb-5 col-lg-6">
        <h5 class="mb-3">Kumpulkan Tugas</h5>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label class="form-label">Mata Kuliah</label>
                <select class="form-select" name="mata_kuliah" required>
                    <option value="">-- Pilih Mata Kuliah --</option>
                    <option>Pemrograman Web</option>
                    <option>Basis Data</option>
                    <option>Jaringan Komputer</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Judul Tugas</label>
                <input type="text" class="form-control" name="judul" placeholder="Contoh: Tugas CRUD PHP" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Upload File</label>
                <input type="file" class="form-control" name="file" required>
            </div>

            <button class="btn btn-custom px-4">Kumpulkan</button>
        </form>
    </div>

    <!-- DAFTAR TUGAS -->
    <h5 class="mb-3">Daftar Tugas</h5>

    <div class="table-responsive col-lg-10">
        <table class="table table-bordered bg-white rounded-4 overflow-hidden align-middle">
            <thead class="text-center">
                <tr>
                    <th>No</th>
                    <th>Mata Kuliah</th>
                    <th>Judul</th>
                    <th>Deadline</th>
                    <th>Status</th>
                    <th>File</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>Pemrograman Web</td>
                    <td>CRUD PHP</td>
                    <td>10 Juni 2025</td>
                    <td class="text-center">
                        <span class="badge bg-success">Dikumpulkan</span>
                    </td>
                    <td class="text-center">
                        <a href="#" class="btn btn-sm btn-outline-success">Lihat</a>
                    </td>
                </tr>

                <tr>
                    <td class="text-center">2</td>
                    <td>Basis Data</td>
                    <td>Normalisasi</td>
                    <td>8 Juni 2025</td>
                    <td class="text-center">
                        <span class="badge bg-danger">Belum</span>
                    </td>
                    <td class="text-center">
                        <span class="text-muted">-</span>
                    </td>
                </tr>

                <tr>
                    <td class="text-center">3</td>
                    <td>Jaringan Komputer</td>
                    <td>Topologi LAN</td>
                    <td>5 Juni 2025</td>
                    <td class="text-center">
                        <span class="badge bg-warning text-dark">Terlambat</span>
                    </td>
                    <td class="text-center">
                        <span class="text-muted">-</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <p class="text-muted mt-5">© 2025 SIAKAD Vokasi | Tugas Mahasiswa</p>

</div>

</body>
</html>
