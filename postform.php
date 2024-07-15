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

// Declaring variables
$f_loca = $f_class = $f_dead = $f_sub = $f_uni = $f_medi = "";

if (isset($_SESSION['u_post'])) {
    $f_loca = $_SESSION['f_loca'];
    $f_dead = $_SESSION['f_dead'];
    $f_uni = $_SESSION['f_uni'];
}

// Posting
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $f_loca = $_POST['location'];
    $f_dead = $_POST['deadline'];
    $f_uni = $_POST['p_university'];

    // Create session for all fields
    $_SESSION['f_loca'] = $f_loca;
    $_SESSION['f_class'] = $f_class;
    $_SESSION['f_dead'] = $f_dead;
    $_SESSION['f_uni'] = $f_uni;

    try {
        if (empty($_POST['sub_list'])) throw new Exception('Subject cannot be empty');
        if (empty($_POST['class_list'])) throw new Exception('Class cannot be empty');
        if (empty($_POST['medium_list'])) throw new Exception('Medium cannot be empty');
        if (empty($_POST['list_district'])) throw new Exception('Location cannot be empty');
        if (empty($_POST['p_university'])) throw new Exception('Preferred University cannot be empty');
        if (empty($_POST['deadline'])) throw new Exception('Deadline cannot be empty');
        if (empty($_POST['sal_range'])) throw new Exception('Salary range cannot be empty');

        if ($user != "" && $utype_db != "teacher") {
            $d = date("Y-m-d"); // Year - Month - Day
            $sublist = implode(',', $_POST['sub_list']);
            $classlist = implode(',', $_POST['class_list']);
            $mediumlist = implode(',', $_POST['medium_list']);
            $listdistrict = implode(',', $_POST['list_district']);
            $salary = $_POST['sal_range'];

            $stmt = $con->prepare("INSERT INTO post (postby_id, subject, class, medium, salary, location, p_university, deadline) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('isssssss', $user, $sublist, $classlist, $mediumlist, $salary, $listdistrict, $_POST['p_university'], $_POST['deadline']);

            if ($stmt->execute()) {
                $success_message = '
                <div class="signupform_content"><h2><font face="bookman">Post successful!</font></h2>
                <div class="signupform_text" style="font-size: 18px; text-align: center;"></div></div>';

                // Destroy all session
                session_destroy();
                // Again start user login session
                session_start();
                $_SESSION['user_login'] = $user;
                header("Location: home.php");
                exit;
            } else {
                throw new Exception('Error: ' . $stmt->error);
            }
        } else {
            $_SESSION['u_post'] = "post";
            header("Location: login.php");
            exit;
        }
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

// Get sub list
include_once("inc/listclass.php");
$list_check = new checkboxlist();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Make a Post</title>
    <link rel="stylesheet" type="text/css" href="css/post.css">
    <link rel="stylesheet" type="text/css" href="css/Navbar.css">
</head>
<body class="body1" style="background:fixed url(./image/backgr.jpg); background-size: 100%;">
<header>
<nav>
    <div class="logo">
        <a href="index.php"><img src="./Image/logO.png" alt="Logo Image"></a>
    </div>
    <div class="hamburger">
        <div class="line1"></div>
        <div class="line2"></div>
        <div class="line3"></div>
    </div>
    <ul class="nav-links">
        <?php 
        if ($utype_db != "teacher") {
            echo '<li><a class="navlink" href="postform.php">Post</a></li>';
        }
        ?>
        <?php
        if ($user != "") {
            $resultnoti = $con->query("SELECT * FROM applied_post WHERE post_by='$user' AND student_ck='no'");
            $resultnoti_cnt = $resultnoti->num_rows;
            echo '<div class="btn"><li><a href="notification.php"><button class="join-button">Notification ('.$resultnoti_cnt.')</button></a>
            <a href="profile.php?uid='.$user.'"><button class="join-button">'.$uname_db.'</button></a>
            <a href="logout.php"><button class="join-button">Logout</button></a>';
        } else {
            echo '<a href="login.php"><button class="join-button">Login</button></a>
            <a href="registration.php"><button class="join-button">Register</button></a><div></li>';
        }
        ?>
    </div>
    </ul>
</nav>
</header>        
<div class="post" style="height: 120px;"></div>
<center>
    <div class="post_form">
        <h1>Make Your Post</h1>
        <?php
        echo '<div class="signup_error_msg">';
        if (isset($error_message)) {
            echo $error_message;
        }
        echo '</div>';
        ?>
        <form action="postform.php" method="post">
            <p>You can Select More Than One Subject</p>
            <div class="subject">
                <h3>Subject</h3>
                <div class="sb">
                    <?php $list_check->sublist(); ?>
                </div>
            </div>
            <div class="class">
                <h3>Class</h3>
                <div class="cls">
                    <?php $list_check->classlist(); ?>
                </div>
            </div>
            <div class="medium">
                <h3>Medium</h3>
                <div class="md">
                    <?php $list_check->mediumlist(); ?>
                </div>
            </div>
            <div class="location">
                <h3>Location</h3>
                <div class="lc">
                    <?php echo $list_check->listdistrict(); ?>
                </div>
            </div>
            <div class="university">
                <h3>University/School</h3>
                <div class="uni">
                    <select name="p_university">
                        <?php if ($f_uni != "") echo '<option value="'.$f_uni.'">'.$f_uni.'</option>'; ?>
                        <option value="None">None</option>
                        <option value="IIT">IIT</option>
                        <option value="APJ Abdul Kalam University">APJ Abdul Kalam University</option>
                        <option value="Cochin University">Cochin University</option>
                        <option value="Kerala University">Kerala University</option>
                        <option value="Government Higher Secondary School">Government Higher Secondary School</option>
                        <option value="Jawahar Navodaya Vidyalaya">Jawahar Navodaya Vidyalaya</option>
                        <option value="Government Medical College">Government Medical College</option>
                        <option value="Army Public School">Army Public School</option>
                        <option value="ST. Peters Senior Secondary School">ST. Peters Senior Secondary School</option>
                    </select>
                </div>
            </div>
            <div class="deadline">
                <h3>Deadline till you need</h3>
                <div class="dl">
                    <p><?php echo '<input name="deadline" type="date" id="datepicker" value="'.$f_dead.'">'; ?></p>
                </div>
            </div>
            <div class="salary">
                <h3>Salary</h3>
                <div class="sl">
                    <select name="sal_range">
                        <?php 
                        for ($i = 800; $i <= 2000; $i += 100) {
                            echo '<option value="'.$i.'">'.$i.' per hour</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <input type="submit" name="submit" class="sub_button" value="Post"/>
        </form>
    </div>
</center>
</body>
</html>
