<?php
require_once '../config/session.php';
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
       // Get all songs or filter by parameters
        // Build with prepared statements
        $base = "SELECT * FROM sarkilar";
        $clauses = [];
        $types = '';
        $vals = [];

        if (isset($_GET['id'])) {
            $clauses[] = "id = ?";
            $types .= 'i';
            $vals[] = (int)$_GET['id'];
        }
        if (isset($_GET['linkliler'])) {
            $clauses[] = "kapak LIKE ?";
            $types .= 's';
            $vals[] = '%http%';
        }
        if (isset($_GET['kategori'])) {
            $clauses[] = "kategori = ?";
            $types .= 's';
            $vals[] = (string)$_GET['kategori'];
        }
        if (isset($_GET['cevap'])) {
            $clauses[] = "cevap = ?";
            $types .= 's';
            $vals[] = (string)$_GET['cevap'];
        }

        $sql = $base . (empty($clauses) ? '' : (' WHERE ' . implode(' AND ', $clauses)));
        $stmt = $conn->prepare($sql);
        if (!empty($vals)) {
            $stmt->bind_param($types, ...$vals);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result === false) {
            jsonError("Database query error: " . $conn->error);
        }
        $songs = [];
        
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $songs[] = $row;
            }
        }
        
        echo json_encode($songs);
        break;
        
    case 'POST':
        if (!isset($_SESSION['yetki']) || (int)$_SESSION['yetki'] !== 1) {
            http_response_code(401);
            jsonError('Yetkisiz', 401);
        }
        // Add new song
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->kategori) && !empty($data->cevap) && !empty($data->sarki) && !empty($data->dosya)) {
            $kategori = $conn->real_escape_string($data->kategori);
            $cevap = $conn->real_escape_string($data->cevap);
            $sarki = $conn->real_escape_string($data->sarki);
            $dosya = $conn->real_escape_string($data->dosya);
            $kapak = isset($data->kapak) && $data->kapak !== '' ? $conn->real_escape_string($data->kapak) : null;
            
            // Aynı şarkının olup olmadığını kontrol et (boşlukları temizleyerek)
            $cevap_trimmed = trim($cevap);
            $sarki_trimmed = trim($sarki);
            
            $check_sql = "SELECT id, cevap, sarki FROM sarkilar WHERE TRIM(cevap) = ? AND TRIM(sarki) = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $cevap_trimmed, $sarki_trimmed);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            
            if($check_result->num_rows > 0) {
                $existing_song = $check_result->fetch_assoc();
                http_response_code(409);
                echo json_encode([
                    "error" => "Bu şarkı zaten mevcut!",
                    "existing_song" => [
                        "id" => $existing_song['id'],
                        "cevap" => $existing_song['cevap'],
                        "sarki" => $existing_song['sarki']
                    ]
                ]);
                exit;
            }
            
            // Insert including optional kapak column
            if($kapak !== null) {
                $sql = "INSERT INTO sarkilar (kategori, cevap, sarki, dosya, kapak) VALUES ('$kategori', '$cevap', '$sarki', '$dosya', '$kapak')";
            } else {
                $sql = "INSERT INTO sarkilar (kategori, cevap, sarki, dosya, kapak) VALUES ('$kategori', '$cevap', '$sarki', '$dosya', NULL)";
            }
            
            if($conn->query($sql) === TRUE) {
                // İşlem kaydı ekle
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
                
                $kaynak = $data->kaynak ?? 'manuel';
                
                $islem_kayit = [
                    'islem_tipi' => 'sarki_ekleme',
                    'kaynak' => $kaynak,
                    'kullanici_id' => $kullanici_id,
                    'kullanici_adi' => $kullanici_adi,
                    'detay' => "$cevap - $sarki şarkısı eklendi",
                    'sarki_adi' => $cevap,
                    'sanatci' => $sarki,
                    'kategori' => $kategori
                ];
                
                // İşlem kaydını ekle
                $islem_sql = "INSERT INTO islem_kayitlari (
                    islem_tipi, kaynak, kullanici_id, kullanici_adi, detay, 
                    sarki_adi, sanatci, kategori
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $islem_stmt = $conn->prepare($islem_sql);
                $islem_stmt->bind_param("ssisssss", 
                    $islem_kayit['islem_tipi'],
                    $islem_kayit['kaynak'],
                    $islem_kayit['kullanici_id'],
                    $islem_kayit['kullanici_adi'],
                    $islem_kayit['detay'],
                    $islem_kayit['sarki_adi'],
                    $islem_kayit['sanatci'],
                    $islem_kayit['kategori']
                );
                $islem_stmt->execute();
                
                http_response_code(201);
                echo json_encode(["message" => "Song added successfully."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Error adding song: " . $conn->error]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data."]);
        }
        break;
        
    case 'PUT':
        if (!isset($_SESSION['yetki']) || (int)$_SESSION['yetki'] !== 1) {
            http_response_code(401);
            jsonError('Yetkisiz', 401);
        }
        // Update song
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $id = $conn->real_escape_string($data->id);
            
            // Mevcut şarkı bilgilerini al
            $current_sql = "SELECT * FROM sarkilar WHERE id = ?";
            $current_stmt = $conn->prepare($current_sql);
            $current_stmt->bind_param("i", $id);
            $current_stmt->execute();
            $current_result = $current_stmt->get_result();
            $current_song = $current_result->fetch_assoc();
            
            if (!$current_song) {
                http_response_code(404);
                echo json_encode(["message" => "Song not found."]);
                break;
            }
            
            $updates = [];
            $eski_degerler = [];
            $yeni_degerler = [];
            
            if(!empty($data->kategori)) {
                $updates[] = "kategori = '" . $conn->real_escape_string($data->kategori) . "'";
                $eski_degerler[] = "Kategori: " . $current_song['kategori'];
                $yeni_degerler[] = "Kategori: " . $data->kategori;
            }
            if(!empty($data->cevap)) {
                $updates[] = "cevap = '" . $conn->real_escape_string($data->cevap) . "'";
                $eski_degerler[] = "Şarkı Adı: " . $current_song['cevap'];
                $yeni_degerler[] = "Şarkı Adı: " . $data->cevap;
            }
            if(!empty($data->sarki)) {
                $updates[] = "sarki = '" . $conn->real_escape_string($data->sarki) . "'";
                $eski_degerler[] = "Sanatçı: " . $current_song['sarki'];
                $yeni_degerler[] = "Sanatçı: " . $data->sarki;
            }
            if(!empty($data->dosya)) {
                $updates[] = "dosya = '" . $conn->real_escape_string($data->dosya) . "'";
                $eski_degerler[] = "Dosya: " . $current_song['dosya'];
                $yeni_degerler[] = "Dosya: " . $data->dosya;
            }
            if(isset($data->kapak)) {
                if($data->kapak === '') {
                    // Allow clearing cover by setting to NULL
                    $updates[] = "kapak = NULL";
                    $eski_degerler[] = "Kapak: " . ($current_song['kapak'] ?? 'Yok');
                    $yeni_degerler[] = "Kapak: Yok";
                } else {
                    $updates[] = "kapak = '" . $conn->real_escape_string($data->kapak) . "'";
                    $eski_degerler[] = "Kapak: " . ($current_song['kapak'] ?? 'Yok');
                    $yeni_degerler[] = "Kapak: " . $data->kapak;
                }
            }
            
            if(!empty($updates)) {
                $sql = "UPDATE sarkilar SET " . implode(', ', $updates) . " WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $id);
                if($stmt->execute()) {
                    // İşlem kaydı ekle
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
                    
                    $islem_kayit = [
                        'islem_tipi' => 'sarki_degistirme',
                        'kaynak' => 'manuel',
                        'kullanici_id' => $kullanici_id,
                        'kullanici_adi' => $kullanici_adi,
                        'detay' => "{$current_song['cevap']} şarkısı güncellendi",
                        'sarki_adi' => $current_song['cevap'],
                        'sanatci' => $current_song['sarki'] ?? '',
                        'kategori' => $current_song['kategori'] ?? '',
                        'eski_deger' => implode(', ', $eski_degerler),
                        'yeni_deger' => implode(', ', $yeni_degerler)
                    ];
                    
                    // İşlem kaydını ekle
                    $islem_sql = "INSERT INTO islem_kayitlari (
                        islem_tipi, kaynak, kullanici_id, kullanici_adi, detay, 
                        sarki_adi, sanatci, kategori, eski_deger, yeni_deger
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $islem_stmt = $conn->prepare($islem_sql);
                    $islem_stmt->bind_param("ssisssssss", 
                        $islem_kayit['islem_tipi'],
                        $islem_kayit['kaynak'],
                        $islem_kayit['kullanici_id'],
                        $islem_kayit['kullanici_adi'],
                        $islem_kayit['detay'],
                        $islem_kayit['sarki_adi'],
                        $islem_kayit['sanatci'],
                        $islem_kayit['kategori'],
                        $islem_kayit['eski_deger'],
                        $islem_kayit['yeni_deger']
                    );
                    $islem_stmt->execute();
                    
                    http_response_code(200);
                    echo json_encode(["message" => "Song updated successfully."]);
                } else {
                    http_response_code(503);
                    echo json_encode(["message" => "Error updating song: " . $conn->error]);
                }
            } else {
                http_response_code(400);
                echo json_encode(["message" => "No data to update."]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Song ID is required."]);
        }
        break;
        
    case 'DELETE':
        if (!isset($_SESSION['yetki']) || (int)$_SESSION['yetki'] !== 1) {
            http_response_code(401);
            jsonError('Yetkisiz', 401);
        }
        // Delete song
        if(isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            
            // Şarkı bilgilerini al
            $song_sql = "SELECT * FROM sarkilar WHERE id = ?";
            $song_stmt = $conn->prepare($song_sql);
            $song_stmt->bind_param("i", $id);
            $song_stmt->execute();
            $song_result = $song_stmt->get_result();
            $song = $song_result->fetch_assoc();
            
            if (!$song) {
                http_response_code(404);
                echo json_encode(["message" => "Song not found."]);
                break;
            }
            
            $sql = "DELETE FROM sarkilar WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $id);
            if($stmt->execute()) {
                // İşlem kaydı ekle
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
                
                $islem_kayit = [
                    'islem_tipi' => 'sarki_silme',
                    'kaynak' => 'manuel',
                    'kullanici_id' => $kullanici_id,
                    'kullanici_adi' => $kullanici_adi,
                    'detay' => "{$song['cevap']} şarkısı silindi",
                    'sarki_adi' => $song['cevap'],
                    'sanatci' => $song['sarki'] ?? '',
                    'kategori' => $song['kategori'] ?? ''
                ];
                
                // İşlem kaydını ekle
                $islem_sql = "INSERT INTO islem_kayitlari (
                    islem_tipi, kaynak, kullanici_id, kullanici_adi, detay, 
                    sarki_adi, sanatci, kategori
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                
                $islem_stmt = $conn->prepare($islem_sql);
                $islem_stmt->bind_param("ssisssss", 
                    $islem_kayit['islem_tipi'],
                    $islem_kayit['kaynak'],
                    $islem_kayit['kullanici_id'],
                    $islem_kayit['kullanici_adi'],
                    $islem_kayit['detay'],
                    $islem_kayit['sarki_adi'],
                    $islem_kayit['sanatci'],
                    $islem_kayit['kategori']
                );
                $islem_stmt->execute();
                
                http_response_code(200);
                echo json_encode(["message" => "Song deleted successfully."]);
            } else {
                http_response_code(503);
                echo json_encode(["message" => "Error deleting song: " . $conn->error]);
            }
        } else {
            http_response_code(400);
            echo json_encode(["message" => "Song ID is required."]);
        }
        break;
        
    default:
        http_response_code(405);
        echo json_encode(["message" => "Method not allowed."]);
        break;
}

$conn->close();
?>
