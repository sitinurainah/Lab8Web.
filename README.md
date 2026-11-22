# Lab8Web.

Aplikasi manajemen data barang sederhana berbasis PHP dan MySQL (melalui XAMPP dan phpMyAdmin) untuk operasi CRUD (Create, Read, Update, Delete) data barang pada basis data latihan1.   Struktur Folderlab8_php_database/index.php: Halaman utama daftar data barang dan aksi hapus   koneksi.php: Konfigurasi koneksi ke server MySQL dan basis data latihan1   tambah.php: Form dan proses tambah data barang   ubah.php: Form dan proses ubah data barang serta unggah gambar   hapus.php: Proses hapus data barang menggunakan metode POST dan prepared statement   Konfigurasi DatabaseBuka phpMyAdmin di browser (http://localhost/phpmyadmin/index.php?route=/database/sql&db=latihan1).   Buat basis data bernama latihan1 (jika belum ada).   Jalankan perintah SQL berikut untuk membuat tabel data_barang:   CREATE TABLE data_barang (
id_barang int(10) auto_increment Primary Key,
kategori varchar(30),
nama varchar(30),
gambar varchar(100),
harga_beli decimal(10,0),
harga_jual decimal(10,0),
stok int(4)
);   Struktur Kolom data_barangid_barang: int(10) — Primary Key, AUTO_INCREMENT   kategori: varchar(30) — Kategori barang (contoh: Komputer, Elektronik, Hand Phone)   nama: varchar(30) — Nama barang   gambar: varchar(100) — Nama file gambar barang   harga_beli: decimal(10,0) — Harga beli barang   harga_jual: decimal(10,0) — Harga jual barang   stok: int(4) — Jumlah stok barang   Konfigurasi Koneksi (koneksi.php)Isi konfigurasi koneksi pada koneksi.php:$host = "localhost";
$user = "root";
$pass = "";
$db = "latihan1";
$conn = mysqli_connect($host,$user, $pass,$db);
if ($conn == false)
{
echo "Koneksi ke server gagal.";
die();
} #else echo "Koneksi berhasil";   Cara Menjalankan di Lokal (XAMPP)Pastikan layanan Apache dan MySQL pada XAMPP sudah berjalan.   Letakkan folder proyek ke dalam direktori htdocs (contoh: C:\xampp\htdocs\lab8_php_database).   Akses melalui browser:Cek koneksi: http://localhost/lab8_php_database/koneksi.php   Direktori proyek: http://localhost/lab8_php_database/   Fitur UtamaKoneksi Basis Data (koneksi.php): Menghubungkan PHP ke MySQL (latihan1) menggunakan mysqli_connect dengan penanganan jika koneksi gagal.   Tambah Barang (tambah.php): Form input untuk Nama Barang, Kategori (Komputer, Elektronik, Hand Phone), Harga Jual, Harga Beli, Stok, serta File Gambar (opsional) dan tombol Simpan.   Ubah Barang (ubah.php): Form untuk mengubah data barang yang sudah ada, lengkap dengan validasi input dan pengecekan tipe file gambar (JPG, PNG, GIF, WEBP menggunakan finfo) serta opsi mengganti gambar.   Hapus Barang (hapus.php): Menghapus data barang berdasarkan id_barang melalui permintaan POST, validasi angka (ctype_digit), dan mysqli_prepare / mysqli_stmt_bind_param.   


<img width="1600" height="738" alt="hasil 3" src="https://github.com/user-attachments/assets/83455881-afea-4158-800c-e23fb3ae1504" />
<img width="1443" height="815" alt="hasil 2" src="https://github.com/user-attachments/assets/f5c697b7-66f6-4abe-9c60-3f5cfabf6f3d" />
<img width="1435" height="893" alt="hasil 2 1" src="https://github.com/user-attachments/assets/f01f635d-717a-4608-99a5-7191bc0c5648" />
<img width="1445" height="825" alt="hasil 1" src="https://github.com/user-attachments/assets/15c6c187-ea2c-4f19-b697-7ff4e65c839e" />
<img width="1184" height="789" alt="gambar 5" src="https://github.com/user-attachments/assets/2e37a85f-77aa-4037-8158-f38aa36d3d1f" />
<img width="1198" height="783" alt="gambar 4" src="https://github.com/user-attachments/assets/d5424628-4aee-420c-be77-8bce2931be6a" />
<img width="882" height="728" alt="gambar 3" src="https://github.com/user-attachments/assets/d83ae173-4e3c-4408-bac0-b29982f6bb9c" />
<img width="1185" height="776" alt="gambar 2" src="https://github.com/user-attachments/assets/945fb28e-6a01-48d1-adfe-23cb594903d1" />
<img width="1440" height="832" alt="gambar 1" src="https://github.com/user-attachments/assets/d6e5e03c-9f08-4dc1-b898-cc8e789cf82d" />
<img width="1597" height="780" alt="hasil 5" src="https://github.com/user-attachments/assets/e28c1b9f-b51a-411c-8122-fae896721174" />
<img width="1600" height="764" alt="hasil 4" src="https://github.com/user-attachments/assets/bb57b669-39ec-458d-bfe9-55b10cd141c0" />
