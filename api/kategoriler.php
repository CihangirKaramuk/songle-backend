<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

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

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // GET /kategoriler.php?is_parent=1 (Ana kategoriler)
        // GET /kategoriler.php?is_parent=0 (Alt kategoriler)
        // GET /kategoriler.php (Tüm kategoriler)
        
        if(isset($_GET['is_parent'])) {
            $is_parent = $_GET['is_parent'];
            if($is_parent == "1") {
                // Ana kategoriler (parent_id NULL olanlar) - parent_isim alanını da ekle
                $sql = "SELECT k.*, p.isim as parent_isim 
                        FROM kategoriler k 
                        LEFT JOIN kategoriler p ON k.parent_id = p.id 
                        WHERE k.parent_id IS NULL 
                        ORDER BY k.isim ASC";
            } else if($is_parent == "0") {
                // Alt kategoriler (parent_id NULL olmayanlar)
                $sql = "SELECT k.*, p.isim as parent_isim 
                        FROM kategoriler k 
                        LEFT JOIN kategoriler p ON k.parent_id = p.id 
                        WHERE k.parent_id IS NOT NULL 
                        ORDER BY k.isim ASC";
            } else {
                jsonError("Invalid is_parent parameter. Use 1 for parent categories or 0 for child categories.", 400);
            }
        } else if(isset($_GET['kategori_id'])) {
            // Belirli bir kategorinin alt kategorileri - parent_isim alanını da ekle
            $kategori_id = $conn->real_escape_string($_GET['kategori_id']);
            $sql = "SELECT k.*, p.isim as parent_isim 
                    FROM kategoriler k 
                    LEFT JOIN kategoriler p ON k.parent_id = p.id 
                    WHERE k.parent_id = '$kategori_id' 
                    ORDER BY k.isim ASC";
        } else {
            // Tüm kategoriler (parent bilgisi ile)
            $sql = "SELECT k.*, p.isim as parent_isim 
                    FROM kategoriler k 
                    LEFT JOIN kategoriler p ON k.parent_id = p.id 
                    ORDER BY k.isim ASC";
        }
        
        $result = $conn->query($sql);
        if ($result === false) {
            jsonError("Database query error: " . $conn->error);
        }
        
        $kategoriler = [];
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Kategori tipini belirle
                $row['tip'] = $row['parent_id'] === null ? 'Ana Kategori' : 'Alt Kategori';
                $kategoriler[] = $row;
            }
        }
        
        jsonSuccess($kategoriler, "Kategoriler başarıyla getirildi");
        break;
        
    case 'POST':
        // POST /kategoriler.php (Yeni kategori ekleme)
        
        // JSON input'u al
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            jsonError("Invalid JSON input", 400);
        }
        
        // Gerekli alanları kontrol et
        if (!isset($input['isim']) || empty(trim($input['isim']))) {
            jsonError("Kategori ismi gereklidir", 400);
        }
        
        $isim = $conn->real_escape_string(trim($input['isim']));
        $parent_id = null;
        
        // Parent ID varsa kontrol et
        if (isset($input['parent_id']) && !empty($input['parent_id'])) {
            $parent_id = $conn->real_escape_string($input['parent_id']);
            
            // Parent kategorinin var olup olmadığını kontrol et
            $check_parent = $conn->query("SELECT id FROM kategoriler WHERE id = '$parent_id'");
            if ($check_parent->num_rows === 0) {
                jsonError("Belirtilen parent kategori bulunamadı", 400);
            }
        }
        
        // Aynı isimde kategori var mı kontrol et
        $check_sql = "SELECT id FROM kategoriler WHERE isim = '$isim'";
        if ($parent_id) {
            $check_sql .= " AND parent_id = '$parent_id'";
        } else {
            $check_sql .= " AND parent_id IS NULL";
        }
        
        $check_result = $conn->query($check_sql);
        if ($check_result->num_rows > 0) {
            jsonError("Bu isimde bir kategori zaten mevcut", 400);
        }
        
        // Kategoriyi ekle
        $sql = "INSERT INTO kategoriler (isim, parent_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $isim, $parent_id);
        
        if ($stmt->execute()) {
            $new_id = $conn->insert_id;
            
            // Eklenen kategoriyi getir
            $get_sql = "SELECT k.*, p.isim as parent_isim 
                        FROM kategoriler k 
                        LEFT JOIN kategoriler p ON k.parent_id = p.id 
                        WHERE k.id = ?";
            $get_stmt = $conn->prepare($get_sql);
            $get_stmt->bind_param("i", $new_id);
            $get_stmt->execute();
            $result = $get_stmt->get_result();
            $new_category = $result->fetch_assoc();
            
            // Kategori tipini belirle
            $new_category['tip'] = $new_category['parent_id'] === null ? 'Ana Kategori' : 'Alt Kategori';
            
            jsonSuccess($new_category, "Kategori başarıyla eklendi", 201);
        } else {
            jsonError("Kategori eklenirken hata oluştu: " . $stmt->error);
        }
        break;
        
    case 'PUT':
        // PUT /kategoriler.php?id=X (Kategori güncelleme)
        
        // ID'yi URL'den al
        if (!isset($_GET['id'])) {
            jsonError("Kategori ID'si gereklidir", 400);
        }
        
        $id = $conn->real_escape_string($_GET['id']);
        
        // JSON input'u al
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!$input) {
            jsonError("Invalid JSON input", 400);
        }
        
        // Kategorinin var olup olmadığını kontrol et
        $check_result = $conn->query("SELECT * FROM kategoriler WHERE id = '$id'");
        if ($check_result->num_rows === 0) {
            jsonError("Kategori bulunamadı", 404);
        }
        
        $current_category = $check_result->fetch_assoc();
        
        // Güncellenecek alanları hazırla
        $updates = [];
        $types = "";
        $values = [];
        
        if (isset($input['isim']) && !empty(trim($input['isim']))) {
            $new_isim = $conn->real_escape_string(trim($input['isim']));
            
            // Aynı isimde başka kategori var mı kontrol et
            $check_sql = "SELECT id FROM kategoriler WHERE isim = '$new_isim' AND id != '$id'";
            if ($current_category['parent_id']) {
                $check_sql .= " AND parent_id = '{$current_category['parent_id']}'";
            } else {
                $check_sql .= " AND parent_id IS NULL";
            }
            
            $check_result = $conn->query($check_sql);
            if ($check_result->num_rows > 0) {
                jsonError("Bu isimde bir kategori zaten mevcut", 400);
            }
            
            $updates[] = "isim = ?";
            $types .= "s";
            $values[] = $new_isim;
        }
        
        if (isset($input['parent_id'])) {
            $new_parent_id = null;
            if (!empty($input['parent_id'])) {
                $new_parent_id = $conn->real_escape_string($input['parent_id']);
                
                // Parent kategorinin var olup olmadığını kontrol et
                $check_parent = $conn->query("SELECT id FROM kategoriler WHERE id = '$new_parent_id'");
                if ($check_parent->num_rows === 0) {
                    jsonError("Belirtilen parent kategori bulunamadı", 400);
                }
                
                // Kendisini parent olarak seçmeye çalışıyorsa engelle
                if ($new_parent_id == $id) {
                    jsonError("Bir kategori kendisini parent olarak seçemez", 400);
                }
            }
            
            $updates[] = "parent_id = ?";
            $types .= "i";
            $values[] = $new_parent_id;
        }
        
        if (empty($updates)) {
            jsonError("Güncellenecek alan bulunamadı", 400);
        }
        
        // Güncelleme sorgusunu oluştur
        $sql = "UPDATE kategoriler SET " . implode(", ", $updates) . " WHERE id = ?";
        $types .= "i";
        $values[] = $id;
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            // Güncellenmiş kategoriyi getir
            $get_sql = "SELECT k.*, p.isim as parent_isim 
                        FROM kategoriler k 
                        LEFT JOIN kategoriler p ON k.parent_id = p.id 
                        WHERE k.id = ?";
            $get_stmt = $conn->prepare($get_sql);
            $get_stmt->bind_param("i", $id);
            $get_stmt->execute();
            $result = $get_stmt->get_result();
            $updated_category = $result->fetch_assoc();
            
            // Kategori tipini belirle
            $updated_category['tip'] = $updated_category['parent_id'] === null ? 'Ana Kategori' : 'Alt Kategori';
            
            jsonSuccess($updated_category, "Kategori başarıyla güncellendi");
        } else {
            jsonError("Kategori güncellenirken hata oluştu: " . $stmt->error);
        }
        break;
        
    case 'DELETE':
        // DELETE /kategoriler.php?id=X (Kategori silme)
        
        if (!isset($_GET['id'])) {
            jsonError("Kategori ID'si gereklidir", 400);
        }
        
        $id = $conn->real_escape_string($_GET['id']);
        
        // Kategorinin var olup olmadığını kontrol et
        $check_result = $conn->query("SELECT * FROM kategoriler WHERE id = '$id'");
        if ($check_result->num_rows === 0) {
            jsonError("Kategori bulunamadı", 404);
        }
        
        $category = $check_result->fetch_assoc();
        
        // Alt kategorileri var mı kontrol et
        $child_check = $conn->query("SELECT id FROM kategoriler WHERE parent_id = '$id'");
        if ($child_check->num_rows > 0) {
            jsonError("Bu kategorinin alt kategorileri bulunmaktadır. Önce alt kategorileri silmelisiniz.", 400);
        }
        
        // Bu kategoriye ait şarkılar var mı kontrol et
        $songs_check = $conn->query("SELECT id FROM sarkilar WHERE kategori = '{$category['isim']}'");
        if ($songs_check->num_rows > 0) {
            jsonError("Bu kategoriye ait şarkılar bulunmaktadır. Önce şarkıları silmelisiniz.", 400);
        }
        
        // Kategoriyi sil
        $sql = "DELETE FROM kategoriler WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            jsonSuccess(null, "Kategori başarıyla silindi");
        } else {
            jsonError("Kategori silinirken hata oluştu: " . $stmt->error);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed."]);
        break;
}

$conn->close();
?>
