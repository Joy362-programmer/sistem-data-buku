<?php
include 'koneksi.php';
$error = '';
$success = '';

if (isset($_POST['simpan'])) {
    $isbn     = $_POST['isbn'];
    $judul    = $_POST['judul'];
    $penulis  = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun    = $_POST['tahun'];

    if (empty($isbn) || empty($judul) || empty($penulis) || empty($penerbit) || empty($tahun)) {
        $error = "Semua field harus diisi!";
    } else {
        $check = mysqli_query($conn, "SELECT * FROM buku WHERE isbn='$isbn'");
        if (mysqli_num_rows($check) > 0) {
            $error = "ISBN sudah terdaftar!";
        } else {
            $query = mysqli_query($conn, "INSERT INTO buku (isbn, judul, penulis, penerbit, tahun_terbit) 
                                          VALUES ('$isbn','$judul','$penulis','$penerbit','$tahun')");
            if ($query) {
                header("Location: index.php");
                exit;
            } else {
                $error = "Gagal menyimpan: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Buku</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #1cc88a;
            --light: #f8f9fc;
            --dark: #5a5c69;
            --danger: #e74a3b;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
            width: 90%;
            max-width: 500px;
        }

        h2 {
            margin-top: 0;
            color: var(--primary);
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        .btn-submit {
            background: var(--secondary);
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: #17a673;
        }

        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--primary);
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2><i class="fas fa-plus"></i> Tambah Buku</h2>

    <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>ISBN</label>
            <input type="text" name="isbn" required>
        </div>

        <div class="form-group">
            <label>Judul</label>
            <input type="text" name="judul" required>
        </div>

        <div class="form-group">
            <label>Penulis</label>
            <input type="text" name="penulis" required>
        </div>

        <div class="form-group">
            <label>Penerbit</label>
            <input type="text" name="penerbit" required>
        </div>

        <div class="form-group">
            <label>Tahun Terbit</label>
            <input type="number" name="tahun" required>
        </div>

        <button type="submit" name="simpan" class="btn-submit">
            <i class="fas fa-save"></i> Simpan
        </button>
    </form>

    <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Buku</a>
</div>

</body>
</html>
