<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../config/session.php';

// Helper to return error responses as JSON and exit
function jsonError(string $message, int $statusCode = 500): void {
    http_response_code($statusCode);
    echo json_encode(["error" => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

// Helper to return success responses as JSON
function jsonSuccess($data = null, string $message = "Success", int $statusCode = 200): void {
    http_response_code($statusCode);
    $response = ["success" => true, "message" => $message];
    if ($data !== null) {
        $response["data"] = $data;
    }
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
}

// Helper function to get user settings
function getAyarlar($kullanici_id) {
    global $conn;
    
    $kullanici_id = $conn->real_escape_string($kullanici_id);
    
    // Check if settings exist for this user
    $sql = "SELECT tema, sayfa_boyutu, bildirim_sesi FROM ayarlar WHERE kullanici_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $kullanici_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        // Return default settings if no settings exist
        return [
            "tema" => "dark",
            "sayfa_boyutu" => 20,
            "bildirim_sesi" => true
        ];
    }
}

// Helper function to save user settings
function saveAyarlar($kullanici_id, $ayarlar) {
    global $conn;
    
    $kullanici_id = $conn->real_escape_string($kullanici_id);
    $tema = $conn->real_escape_string($ayarlar['tema']);
    $sayfa_boyutu = (int)$ayarlar['sayfa_boyutu'];
    $bildirim_sesi = $ayarlar['bildirim_sesi'] ? 1 : 0;
    
    // Check if settings already exist for this user
    $check_sql = "SELECT id FROM ayarlar WHERE kullanici_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $kullanici_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing settings
        $sql = "UPDATE ayarlar SET tema = ?, sayfa_boyutu = ?, bildirim_sesi = ? WHERE kullanici_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siis", $tema, $sayfa_boyutu, $bildirim_sesi, $kullanici_id);
    } else {
        // Insert new settings
        $sql = "INSERT INTO ayarlar (kullanici_id, tema, sayfa_boyutu, bildirim_sesi) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isis", $kullanici_id, $tema, $sayfa_boyutu, $bildirim_sesi);
    }
    
    if (!$stmt->execute()) {
        jsonError("Veritabanı hatası: " . $stmt->error);
    }
    
    // Log the settings save operation with detailed information
    error_log("Ayarlar kaydedildi - Kullanıcı ID: $kullanici_id, Tema: {$ayarlar['tema']}, Sayfa Boyutu: {$ayarlar['sayfa_boyutu']}");
    
    return true;
}

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // GET /api/ayarlar?kullanici_id=X (Get user settings)
        
        if (!isset($_GET['kullanici_id'])) {
            jsonError("Kullanıcı ID'si gereklidir", 400);
        }
        
        $kullanici_id = $_GET['kullanici_id'];
        // Sadece kendi ayarını okuyabilsin (admin istediği kullanıcıyı okuyabilir)
        if (!isset($_SESSION['yetki'])) {
            jsonError('Yetkisiz', 401);
        }
        if ((int)$_SESSION['yetki'] !== 1 && (int)$kullanici_id !== (int)($_SESSION['kullanici_id'] ?? 0)) {
            jsonError('Erişim reddedildi', 403);
        }
        
        // Validate user exists using prepared statement
        $user_check_sql = "SELECT id FROM kullanicilar WHERE id = ?";
        $user_stmt = $conn->prepare($user_check_sql);
        $user_stmt->bind_param("i", $kullanici_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        if ($user_result->num_rows === 0) {
            jsonError("Kullanıcı bulunamadı", 404);
        }
        
        $ayarlar = getAyarlar($kullanici_id);
        jsonSuccess($ayarlar, "Ayarlar başarıyla getirildi");
        break;
        
    case 'POST':
        // POST /api/ayarlar (Save user settings)
        
        // JSON input'u al
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            jsonError("Invalid JSON input", 400);
        }
        
        // Gerekli alanları kontrol et
        if (!isset($input['kullanici_id'])) {
            jsonError("Kullanıcı ID'si gereklidir", 400);
        }
        
        $kullanici_id = $input['kullanici_id'];
        if (!isset($_SESSION['yetki'])) {
            jsonError('Yetkisiz', 401);
        }
        if ((int)$_SESSION['yetki'] !== 1 && (int)$kullanici_id !== (int)($_SESSION['kullanici_id'] ?? 0)) {
            jsonError('Erişim reddedildi', 403);
        }
        
        // Validate user exists using prepared statement
        $user_check_sql = "SELECT id FROM kullanicilar WHERE id = ?";
        $user_stmt = $conn->prepare($user_check_sql);
        $user_stmt->bind_param("i", $kullanici_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();
        if ($user_result->num_rows === 0) {
            jsonError("Kullanıcı bulunamadı", 404);
        }
        
        // Validate settings data
        $ayarlar = [
            'tema' => isset($input['tema']) ? $input['tema'] : 'dark',
            'sayfa_boyutu' => isset($input['sayfa_boyutu']) ? (int)$input['sayfa_boyutu'] : 20,
            'bildirim_sesi' => isset($input['bildirim_sesi']) ? (bool)$input['bildirim_sesi'] : true
        ];
        
        // Validate tema
        if (!in_array($ayarlar['tema'], ['dark', 'light'])) {
            jsonError("Geçersiz tema değeri. 'dark' veya 'light' olmalıdır.", 400);
        }
        
        // Validate sayfa_boyutu
        if ($ayarlar['sayfa_boyutu'] < 1 || $ayarlar['sayfa_boyutu'] > 100) {
            jsonError("Sayfa boyutu 1-100 arasında olmalıdır.", 400);
        }
        
        if (saveAyarlar($kullanici_id, $ayarlar)) {
            jsonSuccess(null, "Ayarlar başarıyla kaydedildi");
        } else {
            jsonError("Ayarlar kaydedilirken hata oluştu");
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed."]);
        break;
}

$conn->close();
?> 
