-- Table users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('admin', 'bendahara') NOT NULL DEFAULT 'bendahara',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table kategori
CREATE TABLE IF NOT EXISTS kategori (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    jenis ENUM('masuk', 'keluar') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table transaksi
CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    keterangan TEXT NOT NULL,
    jenis ENUM('masuk', 'keluar') NOT NULL,
    jumlah DECIMAL(12,2) NOT NULL,
    kategori_id INT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default users
INSERT INTO users (username, password, nama_lengkap, role) VALUES
('admin', '$2y$12$62coh14Kb741qZxDO/sCI.VM/qPgECJZNMehhtPZ8a8jGHAgtV4ya', 'Administrator', 'admin'),
('bendahara', '$2y$12$rGrDtLuS/N9ZNztQRMkSlOgixxb8bBd..zLSrg4u7W3EVeI3XOhHy', 'Bendahara Sekolah', 'bendahara')
ON DUPLICATE KEY UPDATE username=username;

-- Insert kategori default
INSERT INTO kategori (nama_kategori, jenis) VALUES
('SPP Siswa', 'masuk'),
('Dana BOS', 'masuk'),
('Donasi', 'masuk'),
('Lain-lain (Masuk)', 'masuk'),
('Belanja ATK', 'keluar'),
('Honorarium Guru', 'keluar'),
('Operasional Sekolah', 'keluar'),
('Kegiatan Siswa', 'keluar'),
('Lain-lain (Keluar)', 'keluar')
ON DUPLICATE KEY UPDATE nama_kategori=nama_kategori;

-- Insert contoh transaksi
INSERT INTO transaksi (tanggal, keterangan, jenis, jumlah, kategori_id, user_id) VALUES
('2025-01-15', 'SPP Bulan Januari', 'masuk', 500000.00, 1, 2),
('2025-01-20', 'Dana BOS Tahap 1', 'masuk', 25000000.00, 2, 2),
('2025-02-05', 'Beli ATK Kantor', 'keluar', 750000.00, 5, 2),
('2025-02-10', 'Honorarium Guru Februari', 'keluar', 3000000.00, 6, 2),
('2025-02-15', 'Donasi Alumni', 'masuk', 1000000.00, 3, 2)
ON DUPLICATE KEY UPDATE id=id;
