<?php
require_once 'config/database.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Songle API</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .endpoint {
            background-color: #f5f5f5;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        code {
            background-color: #f0f0f0;
            padding: 2px 5px;
            border-radius: 3px;
        }
        .method {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            color: white;
            font-weight: bold;
            margin-right: 10px;
            font-size: 0.9em;
        }
        .get { background-color: #61affe; }
        .post { background-color: #49cc90; }
        .put { background-color: #fca130; }
        .delete { background-color: #f93e3e; }
    </style>
</head>
<body>
    <?php
     if(isset($_GET['id'])) {
        $id = $conn->real_escape_string($_GET['id']);
        $sql = "SELECT * FROM sarkilar WHERE id = $id";
    } else if(isset($_GET['kategori'])) {
        $kategori = $conn->real_escape_string($_GET['kategori']);
        $sql = "SELECT * FROM sarkilar WHERE kategori = '$kategori'";
    } else {
        $sql = "SELECT * FROM sarkilar";
    }
    
    $result = $conn->query($sql);
    $songs = [];
    
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $songs[] = $row;
        }
    }
    
    ?>
    muzıkler lıstesı
    <ul>
    <?php
    foreach($songs as $song) {
        echo "<li>" . $song['cevap'] . "</li>";
    }
    ?>
    </ul>

    <br>

    <h1>Songle API Documentation</h1>
    <p>Welcome to the Songle API. Below are the available endpoints:</p>
    
    <div class="endpoint">
        <h2>Get All Songs</h2>
        <div class="method get">GET</div> <code>/songle-backend/api/songs.php</code>
        <p>Get a list of all songs.</p>
        <p><strong>Example Response:</strong></p>
        <pre>[
    {
        "id": 1,
        "kategori": "turkce-rock",
        "cevap": "Duman - Kufi",
        "sarki": "🎵 (Duman - Kufi)",
        "dosya": "https://...",
        "created_at": "2025-07-25 12:00:00"
    }
]</pre>
    </div>
    
    <div class="endpoint">
        <h2>Get Songs by Category</h2>
        <div class="method get">GET</div> <code>/songle-backend/api/songs.php?kategori={kategori}</code>
        <p>Get songs by category.</p>
        <p><strong>Example:</strong> <code>/songle-backend/api/songs.php?kategori=turkce-rock</code></p>
    </div>
    
    <div class="endpoint">
        <h2>Add New Song</h2>
        <div class="method post">POST</div> <code>/songle-backend/api/songs.php</code>
        <p>Add a new song to the database.</p>
        <p><strong>Request Body (JSON):</strong></p>
        <pre>{
    "kategori": "turkce-rock",
    "cevap": "Duman - Kufi",
    "sarki": "🎵 (Duman - Kufi)",
    "dosya": "https://..."
}</pre>
    </div>
    
    <div class="endpoint">
        <h2>Update Song</h2>
        <div class="method put">PUT</div> <code>/songle-backend/api/songs.php</code>
        <p>Update an existing song.</p>
        <p><strong>Request Body (JSON):</strong></p>
        <pre>{
    "id": 1,
    "kategori": "turkce-rock",
    "cevap": "Duman - Kufi (Updated)",
    "sarki": "🎵 (Duman - Kufi) - Updated",
    "dosya": "https://..."
}</pre>
        <p>Note: Only include the fields you want to update.</p>
    </div>
    
    <div class="endpoint">
        <h2>Delete Song</h2>
        <div class="method delete">DELETE</div> <code>/songle-backend/api/songs.php?id={id}</code>
        <p>Delete a song by ID.</p>
        <p><strong>Example:</strong> <code>/songle-backend/api/songs.php?id=1</code></p>
    </div>
</body>
</html>
