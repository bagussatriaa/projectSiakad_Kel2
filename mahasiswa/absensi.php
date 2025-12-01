<?php $page = 'absensi'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>SIAKAD - Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/global.css">
    <style>
        body{
            font-family: 'Poppins', sans-serif;
            background:#FCFCFC;
        }

        /* .sidebar{
            width:230px;
            height:100vh;
            background:#F5F5DC;
            padding:25px 20px;
            position:fixed;
            left:0;
            top:0;
        }

        .sidebar h2{
            text-align:center;
            margin-bottom:40px;
        }

        .sidebar a{
            display:block;
            text-decoration:none;
            color:#333;
            margin-bottom:15px;
            padding:10px 15px;
            border-radius:10px;
            transition:.2s;
        } */

        /* .sidebar a:hover,
        .sidebar .active{
            background:#9AB4474D;
        } */

        .main{
            margin-left:230px;
            padding:30px;
        }

        .card-custom{
            background:#ffffff;
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

    <h3 class="mb-4">Absensi Mahasiswa 📝</h3>

    <!-- FORM ABSENSI -->
    <div class="card card-custom p-4 mb-5 col-lg-6">
        <h5 class="mb-3">Form Absensi</h5>

        <form method="POST">

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
                <label class="form-label">Pertemuan ke-</label>
                <select class="form-select" name="pertemuan" required>
                    <?php for($i=1;$i<=16;$i++): ?>
                        <option>Pertemuan <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label d-block">Status Kehadiran</label>
                
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" value="Hadir" required>
                    <label class="form-check-label">Hadir</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="status" value="Tidak Hadir">
                    <label class="form-check-label">Tidak Hadir</label>
                </div>
            </div>

            <button class="btn btn-custom px-4">Kirim Absensi</button>

        </form>
    </div>

    <!-- RIWAYAT -->
    <h5 class="mb-3">Riwayat Absensi</h5>

    <div class="table-responsive col-lg-8">
        <table class="table table-bordered bg-white rounded-4 overflow-hidden">
            <thead>
                <tr>
                    <th>Mata Kuliah</th>
                    <th>Pertemuan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Pemrograman Web</td>
                    <td>1</td>
                    <td>02 Juni 2025</td>
                    <td><span class="badge bg-success">Hadir</span></td>
                </tr>

                <tr>
                    <td>Basis Data</td>
                    <td>1</td>
                    <td>03 Juni 2025</td>
                    <td><span class="badge bg-danger">Tidak Hadir</span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <p class="text-muted mt-5">© 2025 SIAKAD Vokasi | Absensi Mahasiswa</p>

</div>

</body>
</html>
