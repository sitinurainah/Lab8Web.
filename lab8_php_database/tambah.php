<?php
require_once __DIR__ . '/koneksi.php';

$error = '';
$nama = '';
$kategori = 'Komputer';
$harga_jual = '';
$harga_beli = '';
$stok = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $kategori = $_POST['kategori'] ?? '';
    $harga_jual = trim($_POST['harga_jual'] ?? '');
    $harga_beli = trim($_POST['harga_beli'] ?? '');
    $stok = trim($_POST['stok'] ?? '');

    $kategoriValid = in_array($kategori, ['Komputer', 'Elektronik', 'Hand Phone'], true);
    if ($nama === '' || !$kategoriValid || !is_numeric($harga_jual) ||
        !is_numeric($harga_beli) || !ctype_digit($stok)) {
        $error = 'Lengkapi semua data dengan format yang benar.';
    }

    $gambar = '';
    if ($error === '' && isset($_FILES['file_gambar']) &&
        $_FILES['file_gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['file_gambar']['error'] !== UPLOAD_ERR_OK) {
            $error = 'Gambar gagal diunggah.';
        } else {
            $mimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];
            $fileInfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($_FILES['file_gambar']['tmp_name']);

            if (!isset($mimeTypes[$mimeType])) {
                $error = 'Pilih file gambar JPG, PNG, GIF, atau WEBP.';
            } else {
                $uploadDirectory = __DIR__ . DIRECTORY_SEPARATOR . 'gambar';
                if (!is_dir($uploadDirectory) &&
                    !mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
                    $error = 'Folder penyimpanan gambar tidak dapat dibuat.';
                } else {
                    $fileName = bin2hex(random_bytes(8)) . '.' . $mimeTypes[$mimeType];
                    $destination = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;

                    if (!move_uploaded_file($_FILES['file_gambar']['tmp_name'], $destination)) {
                        $error = 'Gambar gagal disimpan.';
                    } else {
                        $gambar = $fileName;
                    }
                }
            }
        }
    }

    if ($error === '') {
        $statement = mysqli_prepare(
            $conn,
            'INSERT INTO data_barang (nama, kategori, harga_jual, harga_beli, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)'
        );

        if ($statement === false) {
            $error = 'Data barang gagal disimpan: ' . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param(
                $statement,
                'ssssss',
                $nama,
                $kategori,
                $harga_jual,
                $harga_beli,
                $stok,
                $gambar
            );

            if (mysqli_stmt_execute($statement)) {
                mysqli_stmt_close($statement);
                header('Location: index.php');
                exit;
            }

            $error = 'Data barang gagal disimpan: ' . mysqli_stmt_error($statement);
            mysqli_stmt_close($statement);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tambah Barang</title>
</head>
<body>
    <h1>Tambah Barang</h1>

    <?php if ($error !== ''): ?>
        <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="post" action="tambah.php" enctype="multipart/form-data">
        <p>
            <label for="nama">Nama Barang</label><br>
            <input id="nama" type="text" name="nama" value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>" required>
        </p>
        <p>
            <label for="kategori">Kategori</label><br>
            <select id="kategori" name="kategori" required>
                <option value="Komputer" <?= $kategori === 'Komputer' ? 'selected' : ''; ?>>Komputer</option>
                <option value="Elektronik" <?= $kategori === 'Elektronik' ? 'selected' : ''; ?>>Elektronik</option>
                <option value="Hand Phone" <?= $kategori === 'Hand Phone' ? 'selected' : ''; ?>>Hand Phone</option>
            </select>
        </p>
        <p>
            <label for="harga_jual">Harga Jual</label><br>
            <input id="harga_jual" type="number" name="harga_jual" min="0" step="0.01" value="<?= htmlspecialchars($harga_jual, ENT_QUOTES, 'UTF-8'); ?>" required>
        </p>
        <p>
            <label for="harga_beli">Harga Beli</label><br>
            <input id="harga_beli" type="number" name="harga_beli" min="0" step="0.01" value="<?= htmlspecialchars($harga_beli, ENT_QUOTES, 'UTF-8'); ?>" required>
        </p>
        <p>
            <label for="stok">Stok</label><br>
            <input id="stok" type="number" name="stok" min="0" step="1" value="<?= htmlspecialchars($stok, ENT_QUOTES, 'UTF-8'); ?>" required>
        </p>
        <p>
            <label for="file_gambar">File Gambar (opsional)</label><br>
            <input id="file_gambar" type="file" name="file_gambar" accept="image/jpeg,image/png,image/gif,image/webp">
        </p>
        <button type="submit">Simpan</button>
    </form>

    <p><a href="index.php">Kembali ke Data Barang</a></p>
</body>
</html>
