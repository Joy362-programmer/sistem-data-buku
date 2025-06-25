<?php
include 'koneksi.php';
$isbn = $_GET['isbn'];
mysqli_query($conn, "DELETE FROM buku WHERE isbn='$isbn'");
header("Location: index.php");
exit;
?>
