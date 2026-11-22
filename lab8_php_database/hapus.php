<?php
require_once __DIR__ . '/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Permintaan tidak diizinkan.');
}

$idValue = (string) ($_POST['id'] ?? '');
if (!ctype_digit($idValue)) {
    http_response_code(400);
    exit('ID barang tidak valid.');
}

$id = (int) $idValue;
$statement = mysqli_prepare($conn, 'DELETE FROM data_barang WHERE id_barang = ?');
if ($statement === false) {
    exit('Barang gagal dihapus: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'));
}

mysqli_stmt_bind_param($statement, 'i', $id);
if (!mysqli_stmt_execute($statement)) {
    $error = mysqli_stmt_error($statement);
    mysqli_stmt_close($statement);
    exit('Barang gagal dihapus: ' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8'));
}

$deletedRows = mysqli_stmt_affected_rows($statement);
mysqli_stmt_close($statement);
if ($deletedRows === 0) {
    http_response_code(404);
    exit('Data barang tidak ditemukan.');
}

header('Location: index.php');
exit;
