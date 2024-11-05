<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// GET request to fetch comments
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $imageId = $_GET['image_id'];
    
    try {
        $stmt = $pdo->prepare("
            SELECT comments.*, users.username 
            FROM comments 
            JOIN users ON comments.user_id = users.id 
            WHERE image_id = ? 
            ORDER BY comments.created_at DESC
        ");
        $stmt->execute([$imageId]);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the date for each comment
        foreach ($comments as &$comment) {
            $comment['created_at'] = date('M d, Y H:i', strtotime($comment['created_at']));
        }
        
        echo json_encode($comments);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}

// POST request to add comment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'User not logged in']);
        exit;
    }
    
    $data = json_decode(file_get_contents('php://input'), true);
    $imageId = $data['image_id'];
    $comment = trim($data['comment']);
    
    if (empty($comment)) {
        http_response_code(400);
        echo json_encode(['error' => 'Comment cannot be empty']);
        exit;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO comments (user_id, image_id, comment) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$_SESSION['user_id'], $imageId, $comment]);
        
        echo json_encode(['success' => true]);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
}
?>