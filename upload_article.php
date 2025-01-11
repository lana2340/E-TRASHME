<?php
header('Content-Type: application/json');

// Tampilkan semua error untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

// Periksa apakah data dikirimkan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;

    if ($action === 'upload_article') {
        // Proses upload artikel
        $title = $_POST['title'] ?? null;
        $content = $_POST['content'] ?? null;
        $imageName = null;

        // Validasi data
        if (empty($title) || empty($content)) {
            echo json_encode(["success" => false, "message" => "Judul dan konten harus diisi"]);
            exit;
        }

        // Periksa apakah file gambar diunggah
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "uploads/";
            $imageName = time() . "_" . basename($_FILES['image']['name']);
            $targetFilePath = $targetDir . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                echo json_encode(["success" => false, "message" => "Gagal mengunggah gambar"]);
                exit;
            }
        }

        // Simpan data ke tabel edukasi
        $stmt = $conn->prepare("INSERT INTO edukasi (title, content, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $content, $imageName);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Artikel berhasil disimpan"]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menyimpan artikel"]);
        }

        $stmt->close();
    } elseif ($action === 'upload_image') {
        // Proses upload gambar dan deskripsi
        $description = $_POST['description'] ?? null;
        $imageName = null;

        // Validasi data
        if (empty($description)) {
            echo json_encode(["success" => false, "message" => "Deskripsi harus diisi"]);
            exit;
        }

        // Periksa apakah file gambar diunggah
        if (!empty($_FILES['image']['name'])) {
            $targetDir = "uploads/";
            $imageName = time() . "_" . basename($_FILES['image']['name']);
            $targetFilePath = $targetDir . $imageName;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                echo json_encode(["success" => false, "message" => "Gagal mengunggah gambar"]);
                exit;
            }
        }

        // Simpan data ke tabel gambar
        $stmt = $conn->prepare("INSERT INTO gambar (image, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $imageName, $description);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Gambar berhasil disimpan"]);
        } else {
            echo json_encode(["success" => false, "message" => "Gagal menyimpan gambar"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["success" => false, "message" => "Aksi tidak valid"]);
    }
}
$conn->close();
?>
