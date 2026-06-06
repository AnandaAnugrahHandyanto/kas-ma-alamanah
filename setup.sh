#!/bin/bash
# Setup Database untuk Sistem Informasi Kas MA Al-Amanah
# Run with: bash setup.sh
# Will require sudo for MariaDB root access

set -e

echo "=== Setup Database Kas MA Al-Amanah ==="
echo ""

# Check if sudo is available
if ! command -v sudo &> /dev/null; then
    echo "Error: sudo diperlukan untuk setup database."
    exit 1
fi

# Import database
echo "1. Mengimport database dengan sudo..."
echo "   (Masukkan password sudo jika diminta)"
sudo mariadb < database.sql
echo "   ✅ Database 'db_kas_alamanah' berhasil dibuat."

# Give root@localhost access via mysql_native_password with empty password
echo ""
echo "2. Mengubah autentikasi root ke native_password (empty password)..."
echo "   Ini memungkinkan PHP connect via TCP."
sudo mariadb -e "
    ALTER USER 'root'@'localhost' IDENTIFIED BY '';
    FLUSH PRIVILEGES;
" 2>/dev/null || \
sudo mariadb -e "
    SET PASSWORD FOR 'root'@'localhost' = PASSWORD('');
    FLUSH PRIVILEGES;
" 2>/dev/null || \
echo "   ⚠️  Gagal mengubah password root, tapi mungkin sudah bisa connect via socket."

# Verify
echo ""
echo "3. Verifikasi koneksi..."
mariadb -u root -e "USE db_kas_alamanah; SHOW TABLES;" 2>&1 || \
echo "   ⚠️  Perlu cek manual. Coba jalankan: mariadb -u root"
echo ""

echo "=== Selesai! ==="
echo "Jalankan: php -S localhost:8000"
echo "Login: admin / admin123"
