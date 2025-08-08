<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    // Filtreleme parametreleri
    $islem_tipi = $_GET['islem_tipi'] ?? '';
    $kaynak = $_GET['kaynak'] ?? '';
    $sayfa = (int)($_GET['sayfa'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 10);
    $offset = ($sayfa - 1) * $limit;
    
    // SQL sorgusu oluştur
    $where_conditions = [];
    $params = [];
    
    if ($islem_tipi) {
        $where_conditions[] = "islem_tipi = ?";
        $params[] = $islem_tipi;
    }
    
    if ($kaynak) {
        $where_conditions[] = "kaynak = ?";
        $params[] = $kaynak;
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Toplam kayıt sayısını al
    $count_sql = "SELECT COUNT(*) as total FROM islem_kayitlari $where_clause";
    $count_stmt = $conn->prepare($count_sql);
    if (!empty($params)) {
        $count_stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total'];
    
    // Kayıtları al
    $sql = "SELECT * FROM islem_kayitlari $where_clause ORDER BY tarih DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    $kayitlar = [];
    while ($row = $result->fetch_assoc()) {
        $kayitlar[] = $row;
    }
    
    // Yanıt oluştur
    $response = [
        'success' => true,
        'data' => $kayitlar,
        'pagination' => [
            'current_page' => $sayfa,
            'total_pages' => ceil($total_records / $limit),
            'total_records' => $total_records,
            'limit' => $limit
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Veritabanı hatası: ' . $e->getMessage()
    ]);
}

$conn->close();
?> 
