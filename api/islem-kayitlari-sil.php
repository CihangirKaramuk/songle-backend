<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

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
