<?php
include("inc/connection.inc.php");

ob_start();
session_start();
if (!isset($_SESSION['user_login'])) {
    $user = "";
    $utype_db = "";
} else {
    $user = $_SESSION['user_login'];
    $result = $con->query("SELECT * FROM user WHERE id='$user'");
    $get_user_name = $result->fetch_assoc();
    $uname_db = $get_user_name['fullname'];
    $utype_db = $get_user_name['type'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $con->real_escape_string($_POST['title']);
    $content = $con->real_escape_string($_POST['content']);
    $author = isset($_SESSION['user_login']) ? $con->real_escape_string($_SESSION['user_login']) : 'Guest'; // Use session or default to 'Guest'

    // Insert the new post into the database
    $sql = "INSERT INTO posts (title, content, author) VALUES ('$title', '$content', '$author')";
    
    if ($con->query($sql) === TRUE) {
        // Redirect to the same page to display the new post
        header("Location: blog.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $con->error;
    }
}

// Update SQL query to join posts and user tables
$sql = "SELECT posts.title, posts.content, posts.created_at, user.fullname AS author 
        FROM posts 
        JOIN user ON posts.author = user.id 
        ORDER BY posts.created_at DESC";
$result = $con->query($sql);

if (!$result) {
    echo "Error: " . $sql . "<br>" . $con->error;
} else {
    $posts = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $posts[] = $row;
        }
    } else {
        echo "No posts found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blog</title>
    <link rel="stylesheet" type="text/css" href="css/blog.css">
    <link rel="stylesheet" type="text/css" href="css/Navbar.css">
    <link rel="stylesheet" type="text/css" href="css/nfeed.css">
</head>
<body>
<header>
<nav>
    <div class="logo">
        <a href="index.php"><img src="./Image/logO.png" alt="Logo Image"></a>
    </div>
    <div class="hamburger">
        <div class="line1"></div>
        <div class="line3"></div>
        <div class="line4"></div>
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="search.php">Search Tutor</a></li>
        <li><a href="blog.php">Blog</a></li>
        <?php 
        if ($utype_db == "teacher") {
        } else {
            echo '<li><a class=" navlink" href="postform.php">Post</a></li>';
        }
        ?>
        <?php
        if ($user != "") {
            $resultnoti = $con->query("SELECT * FROM applied_post WHERE post_by='$user' AND student_ck='no'");
            $resultnoti_cnt = $resultnoti->num_rows;
            if ($resultnoti_cnt == 0) {
                $resultnoti_cnt = "";
            } else {
                $resultnoti_cnt = '('.$resultnoti_cnt.')';
            }
            echo '<div class="btn"><li><a href="notification.php"><button class="join-button">Notification'.$resultnoti_cnt.'</button></a>
            <a  href="profile.php?uid='.$user.'"><button class="join-button">'.$uname_db.'</button></a>
            <a  href="logout.php"><button class="join-button">Logout</button></a>';
        } else {
            echo '<a href="login.php"><button class="join-button">Login</button></a>
            <a href="registration.php"><button class="join-button">Register</button></a></div></li>';
        }
        ?>
    </div>
    </ul>
</nav>
</header>
<section>
<!-- Blog Post Form -->
<div class="form-container">
    <h2>Create a New Post</h2>
    <form action="blog.php" method="POST">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Content:</label>
        <textarea id="content" name="content" rows="5" required></textarea>

        <input type="submit" value="Post">
    </form>
</div>
</section>
<!-- Display Existing Posts -->
<h2>All Posts</h2>
<div class="container-bottom-spacing">
    <?php foreach ($posts as $post): ?>
        <div class="post">
            <h3 class="title"><?php echo htmlspecialchars($post['title']); ?></h3>
            <p class="content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <p class="author">Posted by <?php echo htmlspecialchars($post['author']); ?> on <?php echo $post['created_at']; ?></p>
        </div>
    <?php endforeach; ?>
</div>
</div>

</body>
</html>
