<?php
require_once 'config/database.php';

// Example data
$example_song = [
    'kategori' => 'turkce-rock',
    'cevap' => 'Duman - Kufi',
    'sarki' => '🎵 (Duman - Kufi)',
    'dosya' => 'https://cdnt-preview.dzcdn.net/api/1/1/c/7/1/0/c712a7344197b852c187c27ab63b85c5.mp3?hdnea=exp=1753434916~acl=/api/1/1/c/7/1/0/c712a7344197b852c187c27ab63b85c5.mp3*~data=user_id=0,application_id=42~hmac=a565503233f58f9be8c1a382b902a82c2056693818edcaf3c50b802c43734368'
];

// Check if the example song already exists
$check_sql = "SELECT id FROM sarkilar WHERE cevap = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("s", $example_song['cevap']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Insert the example song
    $insert_sql = "INSERT INTO sarkilar (kategori, cevap, sarki, dosya) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ssss", 
        $example_song['kategori'],
        $example_song['cevap'],
        $example_song['sarki'],
        $example_song['dosya']
    );
    
    if ($stmt->execute()) {
        echo "Example song added successfully!\n";
    } else {
        echo "Error adding example song: " . $conn->error . "\n";
    }
} else {
    echo "Example song already exists in the database.\n";
}

// Show all songs in the database
$sql = "SELECT * FROM sarkilar";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "\nCurrent songs in the database:\n";
    echo str_repeat("-", 80) . "\n";
    echo sprintf("%-5s | %-15s | %-30s | %s\n", "ID", "Kategori", "Cevap", "Oluşturulma Tarihi");
    echo str_repeat("-", 80) . "\n";
    
    while($row = $result->fetch_assoc()) {
        // Trim long strings for display
        $kategori = strlen($row['kategori']) > 15 ? substr($row['kategori'], 0, 12) . '...' : $row['kategori'];
        $cevap = strlen($row['cevap']) > 30 ? substr($row['cevap'], 0, 27) . '...' : $row['cevap'];
        
        echo sprintf("%-5d | %-15s | %-30s | %s\n", 
            $row['id'], 
            $kategori, 
            $cevap, 
            $row['created_at']
        );
    }
    echo str_repeat("-", 80) . "\n";
    echo $result->num_rows . " songs found.\n";
} else {
    echo "No songs found in the database.\n";
}

$conn->close();
?>
