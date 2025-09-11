<?php
require_once '../config/session.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 3600');

require_once '../config/database.php';

// Handle CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Admin zorunlu (işlem kayıtları sadece admin görsün)
if (!isset($_SESSION['yetki']) || (int)$_SESSION['yetki'] !== 1) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Yetkisiz']);
    exit;
}

try {
    // Filtreleme parametreleri
    $islem_tipi = $_GET['islem_tipi'] ?? '';
    $kaynak = $_GET['kaynak'] ?? '';
    $from_date = $_GET['from_date'] ?? '';
    $to_date = $_GET['to_date'] ?? '';
    $sayfa = (int)($_GET['sayfa'] ?? 1);
    $limit = (int)($_GET['limit'] ?? 10);
    $limit = $limit > 0 ? $limit : 10;
    $sayfa = $sayfa > 0 ? $sayfa : 1;
    $offset = ($sayfa - 1) * $limit;

    // WHERE koşulları ve parametre tipleri/values
    $where_conditions = [];
    $where_types = '';
    $where_values = [];

    if (!empty($islem_tipi)) {
        // Virgülle ayrılmış işlem tiplerini destekle
        if (strpos($islem_tipi, ',') !== false) {
            $islem_tipleri = explode(',', $islem_tipi);
            $placeholders = str_repeat('?,', count($islem_tipleri) - 1) . '?';
            $where_conditions[] = "islem_tipi IN ($placeholders)";
            $where_types .= str_repeat('s', count($islem_tipleri));
            $where_values = array_merge($where_values, $islem_tipleri);
        } else {
            $where_conditions[] = 'islem_tipi = ?';
            $where_types .= 's';
            $where_values[] = $islem_tipi;
        }
    }

    if (!empty($kaynak)) {
        $where_conditions[] = 'kaynak = ?';
        $where_types .= 's';
        $where_values[] = $kaynak;
    }

    if (!empty($from_date)) {
        $where_conditions[] = 'DATE(tarih) >= ?';
        $where_types .= 's';
        $where_values[] = $from_date;
    }

    if (!empty($to_date)) {
        $where_conditions[] = 'DATE(tarih) <= ?';
        $where_types .= 's';
        $where_values[] = $to_date;
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // Toplam kayıt sayısı
    $count_sql = "SELECT COUNT(*) as total FROM islem_kayitlari $where_clause";
    $count_stmt = $conn->prepare($count_sql);
    if (!empty($where_values)) {
        $count_stmt->bind_param($where_types, ...$where_values);
    }
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = (int)($count_result->fetch_assoc()['total'] ?? 0);

    // Kayıtları al (sayfalı)
    $sql = "SELECT * FROM islem_kayitlari $where_clause ORDER BY tarih DESC LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($sql);
    if (!empty($where_values)) {
        // whereTypes + ii (limit, offset)
        $types = $where_types . 'ii';
        $values = array_merge($where_values, [$limit, $offset]);
        $stmt->bind_param($types, ...$values);
    } else {
        // Sadece limit/offset
        $stmt->bind_param('ii', $limit, $offset);
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
