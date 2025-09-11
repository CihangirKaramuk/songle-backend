<?php
require_once '../config/session.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 3600');

require_once '../config/database.php';
require_once '../config/session.php';

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if (!isset($_SESSION['yetki']) || (int)$_SESSION['yetki'] !== 1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Yetkisiz']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Sadece POST metodu desteklenir']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $kayit_ids = $input['kayit_ids'] ?? [];
    
    if (empty($kayit_ids)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Silinecek kayıt ID\'leri belirtilmedi']);
        exit;
    }
    
    // ID'leri güvenli hale getir
    $placeholders = str_repeat('?,', count($kayit_ids) - 1) . '?';
    $sql = "DELETE FROM islem_kayitlari WHERE id IN ($placeholders)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(str_repeat('i', count($kayit_ids)), ...$kayit_ids);
    $stmt->execute();
    
    $deleted_count = $stmt->affected_rows;
    
    echo json_encode([
        'success' => true,
        'message' => "$deleted_count kayıt başarıyla silindi",
        'deleted_count' => $deleted_count
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
