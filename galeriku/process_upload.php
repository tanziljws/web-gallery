<?php
session_start();
require_once 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id'];

    // Validate inputs
    if (empty($title) || empty($description)) {
        $_SESSION['error'] = "Please fill all required fields";
        header('Location: upload.php');
        exit();
    }

    // Create uploads directory if it doesn't exist
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Handle file upload
    $file_extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension; // Generate unique filename
    $target_file = $target_dir . $new_filename;

    // Check file type
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($file_extension, $allowed_types)) {
        $_SESSION['error'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        header('Location: upload.php');
        exit();
    }

    // Check file size (5MB max)
    if ($_FILES["image"]["size"] > 5000000) {
        $_SESSION['error'] = "Sorry, your file is too large. Maximum size is 5MB.";
        header('Location: upload.php');
        exit();
    }

    try {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // File uploaded successfully, now save to database
            $stmt = $pdo->prepare("INSERT INTO images (title, description, image_url, user_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $description, $target_file, $user_id]);
            
            $_SESSION['success'] = "Image uploaded successfully!";
            header('Location: my_images.php');
            exit();
        } else {
            throw new Exception("Failed to move uploaded file.");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error: " . $e->getMessage();
        header('Location: upload.php');
        exit();
    }
}

// If we get here, something went wrong
$_SESSION['error'] = "Invalid request";
header('Location: upload.php');
exit();
?>