<?php
session_start();
require_once 'config/db.php';

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Images</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <div class="logo">Gallery App</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="upload.php">Upload</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="gallery">
        <?php
        $stmt = $pdo->prepare("SELECT * FROM images WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        
        while($row = $stmt->fetch()) {
            echo '<div class="pin">';
            echo '<img src="' . htmlspecialchars($row['image_url']) . '" alt="' . htmlspecialchars($row['title']) . '">';
            echo '<div class="pin-info">';
            echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
            echo '<p>' . htmlspecialchars($row['description']) . '</p>';
            echo '<div class="pin-actions">';
            echo '<a href="edit.php?id=' . $row['id'] . '" class="edit-btn">Edit</a>';
            echo '<a href="delete.php?id=' . $row['id'] . '" class="delete-btn" onclick="return confirm(\'Are you sure?\')">Delete</a>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html> 