<?php
include("koneksi.php");
$sql = "SELECT * FROM data_barang";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Barang</title>
</head>
<body>
<div class="container">
<h1>Data Barang</h1>
<p><a href="tambah.php">Tambahkan Barang</a></p>
<div class="table-wrapper">
<table border="1" cellpadding="8" cellspacing="0" width="100%">
<thead>
<tr>
<th scope="col">Gambar</th>
<th scope="col">Nama Barang</th>
<th scope="col">Kategori</th>
<th scope="col">Harga Jual</th>
<th scope="col">Harga Beli</th>
<th scope="col">Stok</th>
<th scope="col">ID Barang</th>
<th scope="col">Aksi</th>
</tr>
</thead>
<tbody>
<?php if ($result): ?>
<?php if (mysqli_num_rows($result) > 0): ?>
<?php while ($row = mysqli_fetch_assoc($result)): ?>
<?php $imageName = basename($row['gambar']); ?>
<tr>
<td class="image-cell">
<?php if (is_file(__DIR__ . DIRECTORY_SEPARATOR . 'gambar' . DIRECTORY_SEPARATOR . $imageName)): ?>
<img src="gambar/<?= rawurlencode($imageName); ?>" alt="<?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8'); ?>">
<?php else: ?>
<span class="no-image">Tidak ada gambar</span>
<?php endif; ?>
</td>
<td><?= htmlspecialchars($row['nama'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?= htmlspecialchars($row['kategori'], ENT_QUOTES, 'UTF-8'); ?></td>
<td>Rp <?= number_format((float) $row['harga_jual'], 0, ',', '.'); ?></td>
<td>Rp <?= number_format((float) $row['harga_beli'], 0, ',', '.'); ?></td>
<td><?= htmlspecialchars($row['stok'], ENT_QUOTES, 'UTF-8'); ?></td>
<td><?= htmlspecialchars($row['id_barang'], ENT_QUOTES, 'UTF-8'); ?></td>
<td>
<a href="ubah.php?id=<?= rawurlencode((string) $row['id_barang']); ?>">Ubah</a>
<form method="post" action="hapus.php" onsubmit="return confirm('Yakin ingin menghapus barang ini?');">
<input type="hidden" name="id" value="<?= htmlspecialchars((string) $row['id_barang'], ENT_QUOTES, 'UTF-8'); ?>">
<button type="submit">Hapus</button>
</form>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td class="empty-state" colspan="8">Belum ada data barang.</td>
</tr>
<?php endif; ?>
<?php else: ?>
<tr>
<td class="empty-state" colspan="8">Data barang gagal dimuat: <?= htmlspecialchars(mysqli_error($conn), ENT_QUOTES, 'UTF-8'); ?></td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</body>
</html>