<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// GET request to fetch likes
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $imageId = $_GET['image_id'];
    
    try {
        // Get total likes
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE image_id = ?");
        $stmt->execute([$imageId]);
        $totalLikes = $stmt->fetchColumn();
        
        // Check if user liked this image
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE image_id = ? AND user_id = ?");
        $stmt->execute([$imageId, $_SESSION['user_id']]);
        $userLiked = $stmt->fetchColumn() > 0;
        
        echo json_encode([
            'likes' => $totalLikes,
            'userLiked' => $userLiked
        ]);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

// POST request to toggle like
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $imageId = $data['image_id'];
    
    try {
        // Check if already liked
        $stmt = $pdo->prepare("SELECT id FROM likes WHERE image_id = ? AND user_id = ?");
        $stmt->execute([$imageId, $_SESSION['user_id']]);
        $existing = $stmt->fetch();
        
        if ($existing) {
            // Unlike
            $stmt = $pdo->prepare("DELETE FROM likes WHERE image_id = ? AND user_id = ?");
            $stmt->execute([$imageId, $_SESSION['user_id']]);
            $userLiked = false;
        } else {
            // Like
            $stmt = $pdo->prepare("INSERT INTO likes (image_id, user_id) VALUES (?, ?)");
            $stmt->execute([$imageId, $_SESSION['user_id']]);
            $userLiked = true;
        }
        
        // Get updated like count
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM likes WHERE image_id = ?");
        $stmt->execute([$imageId]);
        $totalLikes = $stmt->fetchColumn();
        
        echo json_encode([
            'likes' => $totalLikes,
            'userLiked' => $userLiked
        ]);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}
?>