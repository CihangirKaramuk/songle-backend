<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'songle');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === FALSE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db(DB_NAME);

// Create songs table if not exists
$create_sarkilar_table = "CREATE TABLE IF NOT EXISTS sarkilar (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kategori VARCHAR(100) NOT NULL,
    cevap VARCHAR(255) NOT NULL,
    sarki TEXT NOT NULL,
    dosya TEXT NOT NULL,
    kapak VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

// Create categories table if not exists
$create_kategoriler_table = "CREATE TABLE IF NOT EXISTS kategoriler (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    isim VARCHAR(100) NOT NULL,
    parent_id INT(11) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$create_kullanicilar_table = "CREATE TABLE IF NOT EXISTS kullanicilar (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    kullanici_adi VARCHAR(100) NOT NULL,
    sifre VARCHAR(255) NOT NULL,
    yetki INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

$create_ayarlar_table = "CREATE TABLE IF NOT EXISTS ayarlar (
    id INT PRIMARY KEY AUTO_INCREMENT,
    kullanici_id INT,
    tema VARCHAR(20) DEFAULT 'dark',
    sayfa_boyutu INT DEFAULT 20,
    bildirim_sesi BOOLEAN DEFAULT true,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($create_sarkilar_table) === FALSE) {
    die("Error creating table: " . $conn->error);
}

if ($conn->query($create_kategoriler_table) === FALSE) {
    die("Error creating table: " . $conn->error);
}

if ($conn->query($create_kullanicilar_table) === FALSE) {
    die("Error creating table: " . $conn->error);
}

if ($conn->query($create_ayarlar_table) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// Create islem_kayitlari table if not exists
$create_islem_kayitlari_table = "CREATE TABLE IF NOT EXISTS islem_kayitlari (
    id INT AUTO_INCREMENT PRIMARY KEY,
    islem_tipi ENUM('sarki_ekleme', 'sarki_silme', 'sarki_degistirme', 'kategori_ekleme', 'kategori_silme', 'kategori_degistirme', 'yetkili_ekleme', 'yetkili_silme', 'yetkili_guncelleme', 'sifre_sifirlama', 'rol_degistirme') NOT NULL,
    kaynak ENUM('deezer', 'mp3', 'manuel', 'admin_panel') NOT NULL,
    kullanici_id INT NOT NULL,
    kullanici_adi VARCHAR(100) NOT NULL,
    tarih TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    detay TEXT NOT NULL,
    sarki_adi VARCHAR(255) NULL,
    sanatci VARCHAR(255) NULL,
    kategori VARCHAR(255) NULL,
    kategori_adi VARCHAR(255) NULL,
    eski_deger TEXT NULL,
    yeni_deger TEXT NULL,
    hedef_kullanici_id INT NULL,
    hedef_kullanici_adi VARCHAR(100) NULL,
    INDEX idx_islem_tipi (islem_tipi),
    INDEX idx_kaynak (kaynak),
    INDEX idx_tarih (tarih),
    INDEX idx_kullanici (kullanici_id)
)";

if ($conn->query($create_islem_kayitlari_table) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// Add foreign key constraint for ayarlar table if it doesn't exist
$check_foreign_key = "SELECT CONSTRAINT_NAME 
                      FROM information_schema.TABLE_CONSTRAINTS 
                      WHERE TABLE_SCHEMA = '" . DB_NAME . "' 
                      AND TABLE_NAME = 'ayarlar' 
                      AND CONSTRAINT_NAME = 'fk_ayarlar_kullanici'";

$result = $conn->query($check_foreign_key);
if ($result->num_rows === 0) {
    $add_foreign_key = "ALTER TABLE ayarlar 
    ADD CONSTRAINT fk_ayarlar_kullanici 
    FOREIGN KEY (kullanici_id) REFERENCES kullanicilar(id) ON DELETE CASCADE";
    $conn->query($add_foreign_key);
}

?>
