<?php
session_start();
include 'db.php';

// Check if the user is logged in AND if their role is 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "You do not have permission to delete posts.";
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM posts WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
header('Location: index.php');
exit();
?>
