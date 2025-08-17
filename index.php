<?php
// Start the session at the very top of the page
session_start();

// Include the database connection file
include 'db.php';

// Check if a user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// --- Step 1 & 2: Implement Search & Pagination Logic ---

// Pagination and search variables
$posts_per_page = 5;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $posts_per_page;

// Initialize the search query
$search_query = "";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    // Add wildcards to the search term
    $search_term = '%' . $_GET['search'] . '%';
    // Modify the SQL query to search in both title and content
    $search_query = "WHERE title LIKE :search_term OR content LIKE :search_term";
}

// Get the total number of posts with the current search query
$total_posts_stmt = $conn->prepare("SELECT COUNT(*) FROM posts $search_query");
if (isset($search_term)) {
    $total_posts_stmt->bindParam(':search_term', $search_term);
}
$total_posts_stmt->execute();
$total_posts = $total_posts_stmt->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);

// Fetch posts for the current page, applying both search and pagination
$stmt = $conn->prepare("SELECT * FROM posts $search_query ORDER BY created_at DESC LIMIT $posts_per_page OFFSET $offset");
if (isset($search_term)) {
    $stmt->bindParam(':search_term', $search_term);
}
$stmt->execute();
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .search-form {
            display: flex;
            gap: 5px;
        }
        .search-form input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-form button {
            padding: 8px 12px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .auth-links a {
            margin-left: 10px;
            text-decoration: none;
            color: #007bff;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        .post {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .post:last-child {
            border-bottom: none;
        }
        h2 {
            margin-top: 0;
            color: #333;
        }
        .post-actions a {
            margin-right: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .post-actions a:hover {
            text-decoration: underline;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            padding: 8px 12px;
            margin: 0 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        .pagination a:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Blog Posts</h1>
            <form action="index.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search posts..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                <button type="submit">Search</button>
            </form>
            <div class="auth-links">
                <?php if ($is_logged_in): ?>
                    <span>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
                    <a href="create.php">Create New Post</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
        
        <?php foreach ($posts as $post): ?>
            <div class="post">
                <h2><?= htmlspecialchars($post['title']) ?></h2>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <?php if ($is_logged_in): ?>
                    <div class="post-actions">
                        <a href="edit.php?id=<?= $post['id'] ?>">Edit</a>
                        <a href="delete.php?id=<?= $post['id'] ?>">Delete</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php
                    // Build the URL, keeping the search term if one exists
                    $link = "index.php?page=$i";
                    if (isset($_GET['search'])) {
                        $link .= "&search=" . urlencode($_GET['search']);
                    }
                    ?>
                    <a href="<?= $link ?>"><?= $i ?></a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>
