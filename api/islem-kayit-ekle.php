<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Sadece POST metodu desteklenir']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Session'dan kullanıcı bilgisini al
    $kullanici_id = $_SESSION['kullanici_id'] ?? 1;
    $kullanici_adi = $_SESSION['kullanici_adi'] ?? 'admin';
    
    // Session'da kullanıcı adı yoksa veya eski ise veritabanından al
    if ($kullanici_adi === 'admin' || empty($kullanici_adi)) {
        $sql = "SELECT kullanici_adi FROM kullanicilar WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $kullanici_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            $kullanici_adi = $user['kullanici_adi'];
            // Session'ı güncelle
            $_SESSION['kullanici_adi'] = $kullanici_adi;
        }
    }
    
    // Eğer input'tan gelen kullanıcı bilgisi varsa onu kullan, yoksa session'dan al
    $input['kullanici_id'] = $input['kullanici_id'] ?? $kullanici_id;
    $input['kullanici_adi'] = $input['kullanici_adi'] ?? $kullanici_adi;
    
    // Gerekli alanları kontrol et
    $required_fields = ['islem_tipi', 'kaynak', 'detay'];
    foreach ($required_fields as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => "$field alanı gereklidir"]);
            exit;
        }
    }
    
    $sql = "INSERT INTO islem_kayitlari (
        islem_tipi, kaynak, kullanici_id, kullanici_adi, detay, 
        sarki_adi, sanatci, kategori, kategori_adi, eski_deger, yeni_deger
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissssssss", 
        $input['islem_tipi'],
        $input['kaynak'],
        $input['kullanici_id'],
        $input['kullanici_adi'],
        $input['detay'],
        $input['sarki_adi'] ?? null,
        $input['sanatci'] ?? null,
        $input['kategori'] ?? null,
        $input['kategori_adi'] ?? null,
        $input['eski_deger'] ?? null,
        $input['yeni_deger'] ?? null
    );
    
    $stmt->execute();
    $kayit_id = $conn->insert_id;
    
    echo json_encode([
        'success' => true,
        'message' => 'İşlem kaydı başarıyla eklendi',
        'kayit_id' => $kayit_id
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}

$conn->close();
?> 
