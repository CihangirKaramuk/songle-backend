<?php
require_once '../config/session.php';
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Sadece admin kullanıcı listesi çekebilir
        if (!isset($_SESSION['yetki']) || (int)$_SESSION['yetki'] !== 1) {
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Yetkisiz']);
            break;
        }

        if (isset($_GET['toplam_kullanici']) && $_GET['toplam_kullanici'] === 'true') {
            $sql = "SELECT COUNT(*) as toplam_kullanici FROM kullanicilar";
            $result = $conn->query($sql);
            $count_result = $result->fetch_assoc();
            echo json_encode(['success' => true, 'toplam_kullanici' => (int)$count_result['toplam_kullanici']]);
            break;
        }

        $base = "SELECT id, kullanici_adi, yetki, created_at FROM kullanicilar";
        $clauses = [];
        $types = '';
        $vals = [];
        if (isset($_GET['kullanici_adi'])) {
            $clauses[] = "kullanici_adi = ?";
            $types .= 's';
            $vals[] = (string)$_GET['kullanici_adi'];
        }
        if (isset($_GET['yetki'])) {
            $clauses[] = "yetki = ?";
            $types .= 'i';
            $vals[] = (int)$_GET['yetki'];
        }
        $sql = $base . (empty($clauses) ? '' : (' WHERE ' . implode(' AND ', $clauses)));
        $stmt = $conn->prepare($sql);
        if (!empty($vals)) { $stmt->bind_param($types, ...$vals); }
        $stmt->execute();
        $res = $stmt->get_result();
        $users = [];
        while ($row = $res->fetch_assoc()) { $users[] = $row; }
        echo json_encode(['success' => true, 'data' => $users]);
        break;

    case 'POST':
        // body: login veya admin tarafından kullanıcı oluşturma/güncelleme/silme
        $data = json_decode(file_get_contents("php://input"));
        
        // Admin kullanıcı oluşturma: { op: 'create', kullanici_adi, sifre, yetki }
        if (isset($data->op) && $data->op === 'create') {
            if (!isset($_SESSION['yetki']) || (int)$_SESSION['yetki'] !== 1) {
                http_response_code(401);
                echo json_encode(["success" => false, "message" => "Yetkisiz"]);
                break;
            }
            $newUser = trim((string)$data->kullanici_adi);
            $newPass = (string)$data->sifre;
            $newYetki = isset($data->yetki) ? (int)$data->yetki : 1;
            if ($newUser === '' || strlen($newPass) < 8) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "Geçersiz kullanıcı adı/şifre"]);
                break;
            }
            // Unique kontrolü
            $check = $conn->prepare("SELECT id FROM kullanicilar WHERE kullanici_adi = ? LIMIT 1");
            $check->bind_param('s', $newUser);
            $check->execute();
            $exists = $check->get_result()->fetch_assoc();
            if ($exists) {
                http_response_code(409);
                echo json_encode(["success" => false, "message" => "Bu kullanıcı adı zaten mevcut"]);
                break;
            }
            $hash = password_hash($newPass, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO kullanicilar (kullanici_adi, sifre, yetki) VALUES (?, ?, ?)");
            $ins->bind_param('ssi', $newUser, $hash, $newYetki);
            if ($ins->execute()) {
                echo json_encode(["success" => true, "message" => "Kullanıcı oluşturuldu", "id" => $conn->insert_id]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Kullanıcı oluşturulamadı"]);
            }
            break;
        }

        // Admin kullanıcı güncelleme: { op: 'update', id, kullanici_adi?, yetki?, yeni_sifre? }
        if (isset($data->op) && $data->op === 'update') {
            if (!isset($_SESSION['yetki']) || (int)$_SESSION['yetki'] !== 1) {
                http_response_code(401);
                echo json_encode(["success" => false, "message" => "Yetkisiz"]);
                break;
            }
            $id = isset($data->id) ? (int)$data->id : 0;
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "Geçersiz ID"]);
                break;
            }
            $updates = [];
            $types = '';
            $vals = [];
            if (isset($data->kullanici_adi) && trim($data->kullanici_adi) !== '') {
                $newUser = trim((string)$data->kullanici_adi);
                $check = $conn->prepare("SELECT id FROM kullanicilar WHERE kullanici_adi = ? AND id <> ? LIMIT 1");
                $check->bind_param('si', $newUser, $id);
                $check->execute();
                if ($check->get_result()->fetch_assoc()) {
                    http_response_code(409);
                    echo json_encode(["success" => false, "message" => "Bu kullanıcı adı zaten mevcut"]);
                    break;
                }
                $updates[] = 'kullanici_adi = ?';
                $types .= 's';
                $vals[] = $newUser;
            }
            if (isset($data->yetki)) {
                $updates[] = 'yetki = ?';
                $types .= 'i';
                $vals[] = (int)$data->yetki;
            }
            if (isset($data->yeni_sifre) && $data->yeni_sifre !== '') {
                $pass = (string)$data->yeni_sifre;
                if (strlen($pass) < 8) {
                    http_response_code(400);
                    echo json_encode(["success" => false, "message" => "Şifre en az 8 karakter olmalı"]);
                    break;
                }
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $updates[] = 'sifre = ?';
                $types .= 's';
                $vals[] = $hash;
            }
            if (empty($updates)) {
                echo json_encode(["success" => true, "message" => "Değişiklik yok"]);
                break;
            }
            $types .= 'i';
            $vals[] = $id;
            $sql = 'UPDATE kullanicilar SET ' . implode(', ', $updates) . ' WHERE id = ?';
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$vals);
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Kullanıcı güncellendi"]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Güncellenemedi"]);
            }
            break;
        }

        // Admin kullanıcı silme: { op: 'delete', id }
        if (isset($data->op) && $data->op === 'delete') {
            if (!isset($_SESSION['yetki']) || (int)$_SESSION['yetki'] !== 1) {
                http_response_code(401);
                echo json_encode(["success" => false, "message" => "Yetkisiz"]);
                break;
            }
            $id = isset($data->id) ? (int)$data->id : 0;
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "Geçersiz ID"]);
                break;
            }
            // Kendi hesabını silme koruması (opsiyonel)
            if (isset($_SESSION['kullanici_id']) && (int)$_SESSION['kullanici_id'] === $id) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "Kendi hesabınızı silemezsiniz"]);
                break;
            }
            $stmt = $conn->prepare('DELETE FROM kullanicilar WHERE id = ?');
            $stmt->bind_param('i', $id);
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "Kullanıcı silindi"]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Silinemedi"]);
            }
            break;
        }

        if(!empty($data->kullanici_adi) && !empty($data->sifre)) {
            $kullanici_adi = $conn->real_escape_string($data->kullanici_adi);
            $sifre = (string)$data->sifre;

            // Passwords now stored as password_hash; keep backward-compat for md5 rows
            $sql = "SELECT id, kullanici_adi, sifre, yetki FROM kullanicilar WHERE kullanici_adi = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $kullanici_adi);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            $ok = false;
            if ($user) {
                $hashed = $user['sifre'];
                if (strlen($hashed) === 32 && ctype_xdigit($hashed)) {
                    // Legacy md5 row
                    $ok = (md5($sifre) === $hashed);
                    // Optional: upgrade to password_hash on successful login
                    if ($ok) {
                        $newHash = password_hash($sifre, PASSWORD_DEFAULT);
                        $up = $conn->prepare("UPDATE kullanicilar SET sifre = ? WHERE id = ?");
                        $up->bind_param('si', $newHash, $user['id']);
                        $up->execute();
                        $hashed = $newHash;
                    }
                } else {
                    // Modern hash
                    $ok = password_verify($sifre, $hashed);
                }
            }

            if ($ok) {
                // Reset session
                session_regenerate_id(true);
                $_SESSION['kullanici_id'] = (int)$user['id'];
                $_SESSION['kullanici_adi'] = $user['kullanici_adi'];
                $_SESSION['yetki'] = (int)$user['yetki'];

                echo json_encode([
                    "message" => "Login successful.",
                    "data" => [
                        'id' => (int)$user['id'],
                        'kullanici_adi' => $user['kullanici_adi'],
                        'yetki' => (int)$user['yetki'],
                    ],
                    "success" => true,
                    "is_admin" => ((int)$user['yetki'] === 1),
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Invalid credentials.", "success" => false, "is_admin" => false]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data.", "success" => false, "is_admin" => false]);
        }
        break;
        
    case 'DELETE':
        // Logout işlemi
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }
            session_destroy();
        }
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
