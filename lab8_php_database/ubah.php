<?php
require_once __DIR__ . '/koneksi.php';

$error = '';
$categories = ['Komputer', 'Elektronik', 'Hand Phone'];
$idValue = $_SERVER['REQUEST_METHOD'] === 'POST'
    ? (string) ($_POST['id'] ?? '')
    : (string) ($_GET['id'] ?? '');

if (!ctype_digit($idValue)) {
    http_response_code(400);
    exit('ID barang tidak valid.');
}

$id = (int) $idValue;
$select = mysqli_prepare($conn, 'SELECT id_barang, nama, kategori, harga_jual, harga_beli, stok, gambar FROM data_barang WHERE id_barang = ?');
if ($select === false) {
    exit('Data barang gagal dimuat: ' . htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'));
}

mysqli_stmt_bind_param($select, 'i', $id);
mysqli_stmt_execute($select);
mysqli_stmt_bind_result($select, $dataId, $dataNama, $dataKategori, $dataHargaJual, $dataHargaBeli, $dataStok, $dataGambar);

if (!mysqli_stmt_fetch($select)) {
    mysqli_stmt_close($select);
    http_response_code(404);
    exit('Data barang tidak ditemukan.');
}

mysqli_stmt_close($select);
$data = [
    'id_barang' => $dataId,
    'nama' => $dataNama,
    'kategori' => $dataKategori,
    'harga_jual' => $dataHargaJual,
    'harga_beli' => $dataHargaBeli,
    'stok' => $dataStok,
    'gambar' => $dataGambar,
];

$nama = $data['nama'];
$kategori = $data['kategori'];
$hargaJual = (string) $data['harga_jual'];
$hargaBeli = (string) $data['harga_beli'];
$stok = (string) $data['stok'];
$gambar = (string) ($data['gambar'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $kategori = $_POST['kategori'] ?? '';
    $hargaJual = trim($_POST['harga_jual'] ?? '');
    $hargaBeli = trim($_POST['harga_beli'] ?? '');
    $stok = trim($_POST['stok'] ?? '');

    $categoryIsValid = in_array($kategori, $categories, true);
    if ($nama === '' || !$categoryIsValid ||
        !is_numeric($hargaJual) || (float) $hargaJual < 0 ||
        !is_numeric($hargaBeli) || (float) $hargaBeli < 0 ||
        !ctype_digit($stok)) {
        $error = 'Lengkapi semua data dengan format yang benar.';
    }

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
        $update = mysqli_prepare(
            $conn,
            'UPDATE data_barang SET nama = ?, kategori = ?, harga_jual = ?, harga_beli = ?, stok = ?, gambar = ? WHERE id_barang = ?'
        );

        if ($update === false) {
            $error = 'Data barang gagal diperbarui: ' . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param(
                $update,
                'ssssssi',
                $nama,
                $kategori,
                $hargaJual,
                $hargaBeli,
                $stok,
                $gambar,
                $id
            );

            if (mysqli_stmt_execute($update)) {
                mysqli_stmt_close($update);
                header('Location: index.php');
                exit;
            }

            $error = 'Data barang gagal diperbarui: ' . mysqli_stmt_error($update);
            mysqli_stmt_close($update);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ubah Barang</title>
</head>
<body>
    <h1>Ubah Barang</h1>

    <?php if ($error !== ''): ?>
        <p role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <form method="post" action="ubah.php" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string) $data['id_barang'], ENT_QUOTES, 'UTF-8'); ?>">
        <fieldset>
            <legend>Informasi Barang</legend>
            <table cellpadding="6">
                <tr>
                    <td><label for="nama">Nama Barang</label></td>
                    <td><input id="nama" type="text" name="nama" value="<?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="kategori">Kategori</label></td>
                    <td>
                        <select id="kategori" name="kategori" required>
                            <?php foreach ($categories as $option): ?>
                                <option value="<?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?>" <?= $kategori === $option ? 'selected' : ''; ?>><?= htmlspecialchars($option, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label for="harga_jual">Harga Jual</label></td>
                    <td><input id="harga_jual" type="number" name="harga_jual" min="0" step="0.01" value="<?= htmlspecialchars($hargaJual, ENT_QUOTES, 'UTF-8'); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="harga_beli">Harga Beli</label></td>
                    <td><input id="harga_beli" type="number" name="harga_beli" min="0" step="0.01" value="<?= htmlspecialchars($hargaBeli, ENT_QUOTES, 'UTF-8'); ?>" required></td>
                </tr>
                <tr>
                    <td><label for="stok">Stok</label></td>
                    <td><input id="stok" type="number" name="stok" min="0" step="1" value="<?= htmlspecialchars($stok, ENT_QUOTES, 'UTF-8'); ?>" required></td>
                </tr>
                <tr>
                    <td>Gambar Saat Ini</td>
                    <td>
                        <?php if ($gambar !== '' && is_file(__DIR__ . DIRECTORY_SEPARATOR . 'gambar' . DIRECTORY_SEPARATOR . basename($gambar))): ?>
                            <img src="gambar/<?= rawurlencode(basename($gambar)); ?>" alt="Gambar <?= htmlspecialchars($nama, ENT_QUOTES, 'UTF-8'); ?>" width="100" height="100">
                        <?php else: ?>
                            Tidak ada gambar
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><label for="file_gambar">Ganti Gambar</label></td>
                    <td><input id="file_gambar" type="file" name="file_gambar" accept="image/jpeg,image/png,image/gif,image/webp"></td>
                </tr>
            </table>
        </fieldset>

        <p>
            <button type="submit">Simpan Perubahan</button>
            <a href="index.php">Batal</a>
        </p>
    </form>
</body>
</html>
