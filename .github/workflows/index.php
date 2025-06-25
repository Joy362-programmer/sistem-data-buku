<?php
include 'koneksi.php';

// Fitur Pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = '';
if (!empty($search)) {
    $where = " WHERE judul LIKE '%$search%' OR penulis LIKE '%$search%' OR penerbit LIKE '%$search%'";
}

// Fitur Pagination
$per_page = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page - 1) * $per_page : 0;

// Hitung total data
$total_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM buku $where");
$total_row = mysqli_fetch_assoc($total_result);
$total_data = $total_row['total'];
$total_pages = ceil($total_data / $per_page);

// Query data dengan pagination
$query = "SELECT * FROM buku $where LIMIT $start, $per_page";
$data = mysqli_query($conn, $query);

// Proses hapus jika ada parameter hapus
if (isset($_GET['hapus'])) {
    $isbn = mysqli_real_escape_string($conn, $_GET['hapus']);
    mysqli_query($conn, "DELETE FROM buku WHERE isbn = '$isbn'");
    header("Location: index.php?page=$page&search=".urlencode($search));
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Buku</title>
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
            margin: 40px;
            background: var(--light);
            color: var(--dark);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
            gap: 15px;
        }

        h2 {
            border-bottom: 2px solid var(--primary);
            padding-bottom: 10px;
            margin: 0;
            font-size: 28px;
        }

        .tambah {
            display: inline-flex;
            align-items: center;
            background: var(--secondary);
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.3s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .tambah:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }

        .search-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .search-box {
            flex-grow: 1;
            padding: 10px 15px;
            border: 1px solid #d1d3e2;
            border-radius: 5px;
            font-size: 16px;
        }

        .search-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem rgba(0,0,0,0.09);
        }

        th, td {
            padding: 15px;
            border-bottom: 1px solid #e3e6f0;
            text-align: left;
        }

        th {
            background: var(--primary);
            color: white;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 14px;
            text-decoration: none;
            color: white;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .edit { background-color: var(--primary); }
        .edit:hover { background-color: #2e59d9; transform: translateY(-2px); }

        .hapus { background-color: var(--danger); }
        .hapus:hover { background-color: #c0392b; transform: translateY(-2px); }

        .aksi-container {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }

        .page-link {
            padding: 8px 15px;
            border: 1px solid #d1d3e2;
            border-radius: 5px;
            text-decoration: none;
            color: var(--primary);
        }

        .page-link:hover,
        .page-link.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            background: white;
            border-radius: 10px;
            margin-top: 30px;
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #e3e6f0;
        }
    </style>
</head>
<body>

<div class="header-container">
    <h2>Daftar Buku</h2>
    <a class="tambah" href="tambah.php"><i class="fas fa-plus"></i> Tambah Buku</a>
</div>

<form method="get" class="search-container">
    <input type="text" name="search" class="search-box" placeholder="Cari buku..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="search-btn"><i class="fas fa-search"></i> Cari</button>
</form>

<?php if (mysqli_num_rows($data) > 0): ?>
<table>
<thead>
    <tr>
        <th>ISBN</th>
        <th>Judul</th>
        <th>Penulis</th>
        <th>Penerbit</th>
        <th>Tahun</th>
        <th>Aksi</th>
    </tr>
</thead>
<tbody>
<?php while ($row = mysqli_fetch_array($data)): ?>
<tr>
    <td><?= htmlspecialchars($row['isbn']) ?></td>
    <td><?= htmlspecialchars($row['judul']) ?></td>
    <td><?= htmlspecialchars($row['penulis']) ?></td>
    <td><?= htmlspecialchars($row['penerbit']) ?></td>
    <td><?= htmlspecialchars($row['tahun_terbit']) ?></td>
    <td>
        <div class="aksi-container">
            <a href="edit.php?isbn=<?= urlencode($row['isbn']) ?>" class="btn edit">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="index.php?hapus=<?= urlencode($row['isbn']) ?>&page=<?= $page ?>&search=<?= urlencode($search) ?>" 
               class="btn hapus" 
               onclick="return confirm('Yakin ingin menghapus buku ini?');">
                <i class="fas fa-trash"></i> Hapus
            </a>
        </div>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<!-- Pagination -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="page-link"><i class="fas fa-chevron-left"></i></a>
    <?php endif; ?>
    
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="page-link <?= $i == $page ? 'active' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>
    
    <?php if ($page < $total_pages): ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="page-link"><i class="fas fa-chevron-right"></i></a>
    <?php endif; ?>
</div>

<?php else: ?>
<div class="empty-state">
    <i class="fas fa-book-open"></i>
    <h3>Tidak ada data buku</h3>
    <p>Silakan tambahkan buku baru</p>
</div>
<?php endif; ?>

</body>
</html>
