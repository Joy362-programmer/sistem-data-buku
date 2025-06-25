<?php
include 'koneksi.php';

$error = '';
$success = '';

// Ambil data buku berdasarkan ISBN
$isbn = isset($_GET['isbn']) ? $_GET['isbn'] : '';

// Jika tidak ada ISBN, redirect ke index
if (empty($isbn)) {
    header("Location: index.php");
    exit;
}

// Ambil data buku dari database
$stmt = $conn->prepare("SELECT * FROM buku WHERE isbn = ?");
$stmt->bind_param("s", $isbn);
$stmt->execute();
$result = $stmt->get_result();
$buku = $result->fetch_assoc();
$stmt->close();

// Jika buku tidak ditemukan
if (!$buku) {
    $error = "Buku dengan ISBN $isbn tidak ditemukan!";
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn = $_POST['isbn'];
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun = $_POST['tahun'];

    // Validasi
    $errors = [];
    if (empty($judul)) $errors[] = "Judul harus diisi!";
    if (empty($penulis)) $errors[] = "Penulis harus diisi!";
    if (empty($penerbit)) $errors[] = "Penerbit harus diisi!";
    if (empty($tahun)) $errors[] = "Tahun terbit harus diisi!";
    
    // Validasi tahun
    $current_year = date('Y');
    if (!empty($tahun) && ($tahun < 1800 || $tahun > $current_year + 1)) {
        $errors[] = "Tahun terbit tidak valid (1800-$current_year)";
    }
    
    if (count($errors) === 0) {
        $stmt = $conn->prepare("UPDATE buku SET 
            judul=?, penulis=?, penerbit=?, tahun_terbit=? 
            WHERE isbn=?");
        $stmt->bind_param("sssss", $judul, $penulis, $penerbit, $tahun, $isbn);
        
        if ($stmt->execute()) {
            $success = "Data buku berhasil diperbarui!";
            
            // Ambil data terbaru
            $stmt = $conn->prepare("SELECT * FROM buku WHERE isbn = ?");
            $stmt->bind_param("s", $isbn);
            $stmt->execute();
            $result = $stmt->get_result();
            $buku = $result->fetch_assoc();
        } else {
            $error = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Buku</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #1cc88a;
            --light: #f8f9fc;
            --dark: #5a5c69;
            --danger: #e74a3b;
            --warning: #f6c23e;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--light);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .form-container {
            width: 90%;
            max-width: 500px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        
        .form-header {
            background: var(--warning);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        
        .form-header h2 {
            margin: 0;
            font-size: 24px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .form-header h2 i {
            margin-right: 10px;
        }
        
        .form-body {
            padding: 30px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: <?php echo $error ? 'flex' : 'none'; ?>;
            align-items: center;
        }
        
        .error-message i,
        .success-message i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: <?php echo $success ? 'flex' : 'none'; ?>;
            align-items: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark);
            font-weight: 500;
        }
        
        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #d1d3e2;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="number"]:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-submit {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 5px;
            width: 100%;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .btn-submit:hover {
            background: #17a673;
            transform: translateY(-2px);
        }
        
        .btn-submit i {
            margin-right: 8px;
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .back-link:hover {
            color: #2e59d9;
            text-decoration: underline;
        }
        
        .back-link i {
            margin-right: 5px;
        }
        
        .isbn-display {
            background-color: #f8f9fc;
            padding: 12px 15px;
            border: 1px solid #d1d3e2;
            border-radius: 5px;
            font-size: 16px;
            color: var(--dark);
        }
        
        /* Loading spinner */
        .loader {
            display: none;
            margin-right: 10px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top: 2px solid white;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .book-icon {
            position: absolute;
            top: -20px;
            right: 20px;
            font-size: 60px;
            color: rgba(255,255,255,0.2);
        }

        /* Perubahan baru */
        .change-comparison {
            display: flex;
            gap: 10px;
            margin-top: 5px;
            font-size: 14px;
        }
        
        .old-value {
            color: var(--danger);
            text-decoration: line-through;
        }
        
        .new-value {
            color: var(--secondary);
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="form-container">
    <div class="form-header">
        <i class="fas fa-book book-icon"></i>
        <h2><i class="fas fa-edit"></i> Edit Buku</h2>
    </div>
    
    <div class="form-body">
        <?php if ($error): ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($buku): ?>
        <form method="post" id="editForm">
            <div class="form-group">
                <label>ISBN</label>
                <div class="isbn-display"><?php echo htmlspecialchars($buku['isbn']); ?></div>
                <input type="hidden" name="isbn" value="<?php echo htmlspecialchars($buku['isbn']); ?>">
            </div>
            
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="judul" value="<?php echo htmlspecialchars($buku['judul']); ?>" required autofocus>
                <?php if (isset($_POST['judul']) && $_POST['judul'] !== $buku['judul']): ?>
                <div class="change-comparison">
                    <span class="old-value"><?= htmlspecialchars($buku['judul']) ?></span>
                    <span>→</span>
                    <span class="new-value"><?= htmlspecialchars($_POST['judul']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Penulis</label>
                <input type="text" name="penulis" value="<?php echo htmlspecialchars($buku['penulis']); ?>" required>
                <?php if (isset($_POST['penulis']) && $_POST['penulis'] !== $buku['penulis']): ?>
                <div class="change-comparison">
                    <span class="old-value"><?= htmlspecialchars($buku['penulis']) ?></span>
                    <span>→</span>
                    <span class="new-value"><?= htmlspecialchars($_POST['penulis']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Penerbit</label>
                <input type="text" name="penerbit" value="<?php echo htmlspecialchars($buku['penerbit']); ?>" required>
                <?php if (isset($_POST['penerbit']) && $_POST['penerbit'] !== $buku['penerbit']): ?>
                <div class="change-comparison">
                    <span class="old-value"><?= htmlspecialchars($buku['penerbit']) ?></span>
                    <span>→</span>
                    <span class="new-value"><?= htmlspecialchars($_POST['penerbit']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label>Tahun Terbit</label>
                <input type="number" name="tahun" value="<?php echo htmlspecialchars($buku['tahun_terbit']); ?>" min="1800" max="<?= date('Y') + 1 ?>" required>
                <?php if (isset($_POST['tahun']) && $_POST['tahun'] != $buku['tahun_terbit']): ?>
                <div class="change-comparison">
                    <span class="old-value"><?= htmlspecialchars($buku['tahun_terbit']) ?></span>
                    <span>→</span>
                    <span class="new-value"><?= htmlspecialchars($_POST['tahun']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn-submit" id="submitBtn">
                <div class="loader" id="loader"></div>
                <i class="fas fa-save"></i> Simpan Perubahan
            </button>
        </form>
        <?php else: ?>
        <div class="error-message">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Buku</a>
    </div>
</div>

<script>
document.getElementById('editForm').addEventListener('submit', function(e) {
    var submitBtn = document.getElementById('submitBtn');
    var loader = document.getElementById('loader');
    
    // Tampilkan loader
    loader.style.display = 'block';
    
    // Nonaktifkan tombol
    submitBtn.disabled = true;
    
    // Konfirmasi perubahan
    if (!confirm('Anda yakin ingin menyimpan perubahan?')) {
        e.preventDefault();
        loader.style.display = 'none';
        submitBtn.disabled = false;
        return false;
    }
    
    // Ganti teks tombol
    var submitText = submitBtn.querySelector('i').nextSibling;
    if (submitText) {
        submitText.textContent = ' Menyimpan...';
    }
});

// Auto-focus pada input pertama
document.querySelector('input[name="judul"]').focus();

// Auto-save draft setiap 5 detik
setInterval(() => {
    if (!document.getElementById('loader').style.display) {
        const formData = {
            judul: document.querySelector('input[name="judul"]').value,
            penulis: document.querySelector('input[name="penulis"]').value,
            penerbit: document.querySelector('input[name="penerbit"]').value,
            tahun: document.querySelector('input[name="tahun"]').value
        };
        localStorage.setItem('book_draft', JSON.stringify(formData));
    }
}, 5000);
</script>

</body>
</html>