<?php
header('Content-Type: application/json');

// Konfigurasi database
$host = 'localhost';
$dbname = 'dbSI'; // Nama database
$username = 'root';
$password = '';

// Koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Koneksi gagal: " . $conn->connect_error]);
    exit;
}

// Ambil data artikel dari tabel edukasi
$sqlArticles = "SELECT id, title, content, image, created_at FROM edukasi ORDER BY created_at DESC";
$resultArticles = $conn->query($sqlArticles);

$articles = [];
if ($resultArticles->num_rows > 0) {
    while ($row = $resultArticles->fetch_assoc()) {
        $articles[] = $row;
    }
}

// Ambil data gambar dari tabel gambar
$sqlImages = "SELECT id, image, description, created_at FROM gambar ORDER BY created_at DESC";
$resultImages = $conn->query($sqlImages);

$images = [];
if ($resultImages->num_rows > 0) {
    while ($row = $resultImages->fetch_assoc()) {
        $images[] = $row;
    }
}

// Gabungkan data artikel dan gambar
echo json_encode([
    "success" => true,
    "articles" => $articles,
    "images" => $images
]);

$conn->close();
?>
