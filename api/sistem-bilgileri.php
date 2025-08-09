<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../config/session.php';

try {
    // Toplam şarkı sayısı
    $sql = "SELECT COUNT(*) as toplam_sarki FROM sarkilar";
    $result = $conn->query($sql);
    $sarkiResult = $result->fetch_assoc();
    
    // Toplam kategori sayısı
    $sql = "SELECT COUNT(*) as toplam_kategori FROM kategoriler";
    $result = $conn->query($sql);
    $kategoriResult = $result->fetch_assoc();
    
    // Toplam kullanıcı sayısı
    $sql = "SELECT COUNT(*) as toplam_kullanici FROM kullanicilar";
    $result = $conn->query($sql);
    $kullaniciResult = $result->fetch_assoc();
    
    // Son 7 günlük işlem kayıtları sayısı
    $sql = "SELECT COUNT(*) as son_7_gun_islem FROM islem_kayitlari WHERE tarih >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $result = $conn->query($sql);
    $son7GunResult = $result->fetch_assoc();
    
    // Bugünkü işlem kayıtları sayısı
    $sql = "SELECT COUNT(*) as bugun_islem FROM islem_kayitlari WHERE DATE(tarih) = CURDATE()";
    $result = $conn->query($sql);
    $bugunResult = $result->fetch_assoc();
    
    echo json_encode([
        'success' => true,
        'data' => [
            'toplam_sarki' => (int)$sarkiResult['toplam_sarki'],
            'toplam_kategori' => (int)$kategoriResult['toplam_kategori'],
            'toplam_kullanici' => (int)$kullaniciResult['toplam_kullanici'],
            'son_7_gun_islem' => (int)$son7GunResult['son_7_gun_islem'],
            'bugun_islem' => (int)$bugunResult['bugun_islem']
        ]
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conn->close();
?> 
