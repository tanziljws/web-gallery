<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    // Verify ownership
    $stmt = $pdo->prepare("SELECT user_id FROM images WHERE id = ?");
    $stmt->execute([$id]);
    $image = $stmt->fetch();

    if ($image && $image['user_id'] == $_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE images SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $description, $id]);
        header('Location: my_images.php');
    } else {
        die("Unauthorized access");
    }
}
?> 