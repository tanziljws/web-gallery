<?php
session_start();
require_once 'config/db.php';

if(!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM images WHERE id = ? AND user_id = ?");
$stmt->execute([$_GET['id'], $_SESSION['user_id']]);
$image = $stmt->fetch();

if(!$image) {
    header('Location: my_images.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Image</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <div class="logo">Gallery App</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="my_images.php">My Images</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="edit-container">
        <h2>Edit Image</h2>
        <form action="process_edit.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $image['id']; ?>">
            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($image['title']); ?>" required>
            </div>
            <div class="form-group">
                <label>Description:</label>
                <textarea name="description" required><?php echo htmlspecialchars($image['description']); ?></textarea>
            </div>
            <button type="submit">Update Image</button>
        </form>
    </div>
</body>
</html> 