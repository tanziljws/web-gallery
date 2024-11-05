<?php
session_start();
require_once 'config/db.php';

if(isset($_GET['id']) && isset($_SESSION['user_id'])) {
    // Verify ownership
    $stmt = $pdo->prepare("SELECT * FROM images WHERE id = ? AND user_id = ?");
    $stmt->execute([$_GET['id'], $_SESSION['user_id']]);
    $image = $stmt->fetch();

    if($image) {
        // Delete the physical file
        if(file_exists($image['image_url'])) {
            unlink($image['image_url']);
        }

        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
        $stmt->execute([$_GET['id']]);
    }
}

header('Location: my_images.php');
?> 