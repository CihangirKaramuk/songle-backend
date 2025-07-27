<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
       // Get all songs or filter by parameters
        $sql = "SELECT * FROM sarkilar";
        $conditions = [];

        if (isset($_GET['id'])) {
            $id = $conn->real_escape_string($_GET['id']);
            $conditions[] = "id = $id";
        }

        if (isset($_GET['kategori'])) {
            $kategori = $conn->real_escape_string($_GET['kategori']);
            $conditions[] = "kategori = '$kategori'";
        }

        if (isset($_GET['cevap'])) {
            $cevap = $conn->real_escape_string($_GET['cevap']);
            $conditions[] = "cevap = '$cevap'";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        
        $result = $conn->query($sql);
        $songs = [];
        
        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $songs[] = $row;
            }
        }
        
        echo json_encode($songs);
        break;
        
    case 'POST':
        // Add new song
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->kategori) && !empty($data->cevap) && !empty($data->sarki) && !empty($data->dosya)) {
            $kategori = $conn->real_escape_string($data->kategori);
            $cevap = $conn->real_escape_string($data->cevap);
            $sarki = $conn->real_escape_string($data->sarki);
            $dosya = $conn->real_escape_string($data->dosya);
            $kapak = isset($data->kapak) && $data->kapak !== '' ? $conn->real_escape_string($data->kapak) : null;
            
            // Insert including optional kapak column
            if($kapak !== null) {
                $sql = "INSERT INTO sarkilar (kategori, cevap, sarki, dosya, kapak) VALUES ('$kategori', '$cevap', '$sarki', '$dosya', '$kapak')";
            } else {
                $sql = "INSERT INTO sarkilar (kategori, cevap, sarki, dosya, kapak) VALUES ('$kategori', '$cevap', '$sarki', '$dosya', NULL)";
            }
            
            if($conn->query($sql) === TRUE) {
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
        // Update song
        $data = json_decode(file_get_contents("php://input"));
        
        if(!empty($data->id)) {
            $id = $conn->real_escape_string($data->id);
            $updates = [];
            
            if(!empty($data->kategori)) $updates[] = "kategori = '" . $conn->real_escape_string($data->kategori) . "'";
            if(!empty($data->cevap)) $updates[] = "cevap = '" . $conn->real_escape_string($data->cevap) . "'";
            if(!empty($data->sarki)) $updates[] = "sarki = '" . $conn->real_escape_string($data->sarki) . "'";
            if(!empty($data->dosya)) $updates[] = "dosya = '" . $conn->real_escape_string($data->dosya) . "'";
            if(isset($data->kapak)) {
                if($data->kapak === '') {
                    // Allow clearing cover by setting to NULL
                    $updates[] = "kapak = NULL";
                } else {
                    $updates[] = "kapak = '" . $conn->real_escape_string($data->kapak) . "'";
                }
            }
            
            if(!empty($updates)) {
                $sql = "UPDATE sarkilar SET " . implode(', ', $updates) . " WHERE id = $id";
                
                if($conn->query($sql) === TRUE) {
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
        // Delete song
        if(isset($_GET['id'])) {
            $id = $conn->real_escape_string($_GET['id']);
            $sql = "DELETE FROM sarkilar WHERE id = $id";
            
            if($conn->query($sql) === TRUE) {
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
