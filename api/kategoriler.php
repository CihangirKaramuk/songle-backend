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

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        if(isset($_GET['is_parent']) && $_GET['is_parent'] == "1") {
            $sql = "SELECT * FROM kategoriler WHERE parent_id IS NULL";
        } else if(isset($_GET['kategori_id'])) {
            $kategori_id = $conn->real_escape_string($_GET['kategori_id']);
            $sql = "SELECT * FROM kategoriler WHERE parent_id = '$kategori_id'"; 
        } else {
            $sql = "SELECT * FROM kategoriler";
        }
        
        $result = $conn->query($sql);
        if ($result === false) {
            jsonError("Database query error: " . $conn->error);
        }
        $kategoriler = [];
        
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $kategoriler[] = $row;
            }
        }
        
        echo json_encode($kategoriler);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed."]);
        break;
}

$conn->close();
?>
