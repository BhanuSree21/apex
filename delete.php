<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// The rest of your existing PHP code for creating a post follows here...
// For example:
// include 'db.php';
// if ($_SERVER['REQUEST_METHOD'] == 'POST') { ... }
?>

<!-- The rest of your HTML form follows here... -->
<?php
include 'db.php';
$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM posts WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();
header('Location: index.php');
exit();
?>