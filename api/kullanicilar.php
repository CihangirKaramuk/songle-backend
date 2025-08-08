<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Eğer toplam_kullanici parametresi varsa sadece sayıyı döndür
        if (isset($_GET['toplam_kullanici']) && $_GET['toplam_kullanici'] === 'true') {
            $sql = "SELECT COUNT(*) as toplam_kullanici FROM kullanicilar";
            $result = $conn->query($sql);
            $count_result = $result->fetch_assoc();
            
            echo json_encode([
                'success' => true,
                'toplam_kullanici' => $count_result['toplam_kullanici']
            ]);
            break;
        }
        
        // Get users with optional filters
        $sql = "SELECT * FROM kullanicilar";
        $conditions = [];

        if (isset($_GET['kullanici_adi'])) {
            $kullanici_adi = $conn->real_escape_string($_GET['kullanici_adi']);
            $conditions[] = "kullanici_adi = '$kullanici_adi'";
        }

        if (isset($_GET['yetki'])) {
            $yetki = $conn->real_escape_string($_GET['yetki']);
            $conditions[] = "yetki = '$yetki'";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $result = $conn->query($sql);
        $kullanicilar = [];
    
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $kullanicilar[] = $row;
            }
        }
    
        echo json_encode($kullanicilar);
        break;

    case 'POST':
        // body username, password login işlemleri
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->kullanici_adi) && !empty($data->sifre)) {
            $kullanici_adi = $conn->real_escape_string($data->kullanici_adi);
            $sifre = $conn->real_escape_string($data->sifre);
            $sifre = md5($sifre);
            
            // login
            $sql = "SELECT * FROM kullanicilar WHERE kullanici_adi = '$kullanici_adi' AND sifre = '$sifre'";
            
            $result = $conn->query($sql);
            $kullanicilar = [];
        
            if($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $kullanicilar[] = $row;
                }
                
                // Session'ı temizle ve yeniden oluştur
                session_destroy();
                session_start();
                
                // Session'a güncel kullanıcı bilgisini kaydet
                $_SESSION['kullanici_id'] = $kullanicilar[0]['id'];
                $_SESSION['kullanici_adi'] = $kullanicilar[0]['kullanici_adi'];
                $_SESSION['yetki'] = $kullanicilar[0]['yetki'];
            }
        
            echo json_encode(
                [
                    "message" => "Login successful.",
                    "data" => $kullanicilar,
                    "success" => true,
                    "is_admin" => isset($kullanicilar[0]["yetki"]) && $kullanicilar[0]["yetki"] == "1" ? true : false,
                ]
            );
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data.", "success" => false, "is_admin" => false]);
        }
        break;
        
    case 'DELETE':
        // Logout işlemi
        session_destroy();
        echo json_encode([
            "message" => "Logout successful.",
            "success" => true
        ]);
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed.", "success" => false, "is_admin" => false]);
        break;
    }


$conn->close();
?>
