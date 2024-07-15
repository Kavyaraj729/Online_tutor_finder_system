<!DOCTYPE html>
<html>
<head>
	<title>Search</title>
	<link rel="stylesheet" type="text/css" href="css/Navbar.css">
	<link rel="stylesheet" type="text/css" href="css/search.css">
    
</head>
<body class="body1" style="background:fixed url(./image/backgr.jpg); background-size: 110%;">
<header>
<?php
 include ( "inc/connection.inc.php" );
ob_start();
session_start();
if (!isset($_SESSION['user_login'])) {
	$user = "";
	$utype_db = "";
}
else {
	$user = $_SESSION['user_login'];
	$result = $con->query("SELECT * FROM user WHERE id='$user'");
		$get_user_name = $result->fetch_assoc();
			$uname_db = $get_user_name['fullname'];
			$utype_db = $get_user_name['type'];
}
//time ago convert
include_once("inc/timeago.php");
$time = new timeago();
//declearing variable
$f_loca = "";
$f_class = "";
$f_dead = "";
$f_sub = "";
$f_uni = "";
$f_medi = "";
//get sub list
include_once("inc/listclass.php");
$list_check = new checkboxlist();
?>
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
            <li><a href="search.php">Search Tutor</a></li>
			<?php 
			if($utype_db == "teacher")
				{

				}else {
					echo '<li><a class=" navlink" href="blog.php">Blog </a></li>';
				}
			 ?>
						<?php
							if($user != ""){
								$resultnoti = $con->query("SELECT * FROM applied_post WHERE post_by='$user' AND student_ck='no'");
								$resultnoti_cnt = $resultnoti->num_rows;
								if($resultnoti_cnt == 0){
									$resultnoti_cnt = "";
								}else{
									$resultnoti_cnt = '('.$resultnoti_cnt.')';
								}
								echo '<div class="btn"><li><a href="notification.php"><button class="join-button">Notification'.$resultnoti_cnt.'</button></a>
								<a  href="profile.php?uid='.$user.'"><button class="join-button">'.$uname_db.'</button></a>
								<a  href="logout.php"><button class="join-button" >Logout</button></a>';
							}else{
								echo '<a href="login.php"><button class="join-button">Login</button></a>
								<a href="registration.php"><button class="join-button">Register</button></a><d/iv></li>';
							}
						?>
			</div>
			</ul>
	</nav>
</header>
	<!-- Students -->
	<div class="nbody" >
		<div class="nfeedleftsearch">
			<div class="postbox">
			<?php
				echo '<div class="signup_error_msg">';
						if (isset($error_message)) {echo $error_message;}		
				echo'</div>';
				?>
			  	<form method="post">
				  <center>
        <div class="Search">
            <h1>Search Your Tutor</h1>
            <div class="s_contents">
                <div class="sb">
                    <p>Subject</p>
                    <?php $list_check->sublistcombo(); ?>
                </div>
                <div class="cl">
                    <p>Class</p>
                    <?php $list_check->classlistcombo(); ?>
                </div>
                <div class="md">
                    <p>Medium</p>
                    <?php $list_check->mediumlistcombo(); ?>
                </div>
                <div class="lc">
                    <p>Location</p>
                    <?php $list_check->listdistrictcombo(); ?>
                </div>
                <div class="uni">
                    <p>University</p>
                    <select name="p_university" style="width: 180px;">
                        <?php if($f_uni!="") echo '<option value="'.$f_uni.'">'.$f_uni.'</option>';  ?>
						<option value="None">None</option>
              <option value="IIT">IIT</option>
              <option value="APJ Abdul Kalam University">APJ Abdul Kalam University</option>
              <option value="Cochin University">Cochin University</option>
			  <option value="Kerela University">Kerela University</option>
			  <option value=" Government Higher Secondary School">Government Higher Secondary School</option>
			  <option value="Jawahar Navodya Vidyalaya">Jawahar Navodya Vidyalaya</option>
			  <option value="Government Medical College">Government Medical College</option>
			  <option value="Army Public School">Army Public School</option>
			  <option value="ST. Peters Senior Secondary School">ST. Peters Senior Secondary School</option>
                    </select>
                </div>
                <input type="submit" name="submit" class="s_button" value="Search"/></br>
            </div>   
        </div>
    </center>
			</div>
		</div>
<center><div class="srch_main">
<?php
				if (isset($_POST['submit'])){
					$f_sub = $_POST['sublistcombo'];
					$f_sub = mysqli_real_escape_string($con, $f_sub);
					$f_class = $_POST['classlistcombo'];
					$f_class = mysqli_real_escape_string($con, $f_class);
					$f_medium = $_POST['mediumlistcombo'];
					$f_medium = mysqli_real_escape_string($con, $f_medium);
					$f_loca = $_POST['listdistrictcombo'];
					$f_loca = mysqli_real_escape_string($con, $f_loca);
					$f_university = $_POST['p_university'];
					$f_university = mysqli_real_escape_string($con, $f_university);
					$condition = '1=1';
					if ($f_sub != "None"){
					    $condition = $condition . " AND prefer_sub LIKE '%{$f_sub}%' ";
					}
					if ($f_class != "None"){
					    $condition =  $condition . " AND class LIKE '%{$f_class}%' ";
					} 
					if ($f_medium != "None"){
					    $condition =  $condition . " AND medium LIKE '%{$f_medium}%' ";
					} 
					if ($f_loca != ""){
					    $condition =  $condition . " AND prefer_location LIKE '%{$f_loca}%' ";
					} 
					if ($f_university != "None"){
					    $condition =  $condition . " AND inst_name LIKE '%{$f_university}%' ";
					} 
					if($condition == "1=1"){
						echo '<h1><label>Please Select Search Item</label></h1>';
					}else{
						$query_sub = $con->query("SELECT * FROM `tutor` WHERE ".$condition);
						$query_sub_cnt = $query_sub->num_rows;
						if($query_sub_cnt == 0){
							$query_sub_cnt = "No";
						}
						echo '<p><label text color="black">'.$query_sub_cnt.' Tutor Found </label></p>';
					while($row = $query_sub->fetch_assoc()) { 
						$post_id = $row['id'];
						$tutor_id = $row['t_id'];
						$sub = $row['prefer_sub'];
						$sub = str_replace(",", ", ", $sub);
						$class = $row['class'];
						$class = str_replace(",", ", ", $class);
						$location = $row['prefer_location'];
						$location = str_replace(",", ", ", $location);
						$p_university = $row['inst_name'];
						$medium = $row['medium'];

						$query1 = $con->query("SELECT * FROM user WHERE id='$tutor_id'");
						$user_fname = $query1->fetch_assoc();
						$uname_db = $user_fname['fullname'];
						$pro_pic_db = $user_fname['user_pic'];
						$ugender_db = $user_fname['gender'];
						$last_login = $user_fname['last_logout'];
						$cntnm = "";
						if($user != ""){
							$query6 = $con->query("SELECT * FROM applied_post WHERE applied_by='$user' AND applied_to='$tutor_id'");
							$cntnm = $query6->num_rows;
						}
						if($pro_pic_db == ""){
								if($ugender_db == "male"){
								$pro_pic_db = "malepic.png";
							}else if($ugender_db == "female"){
								$pro_pic_db = "femalepic.png";
							}
						}else {
							if (file_exists("image/profilepic/".$pro_pic_db)){
							//nothing
							}else{
									if($ugender_db == "male"){
									$pro_pic_db = "malepic.png";
								}else if($ugender_db == "female"){
									$pro_pic_db = "femalepic.png";
								}
							}
						}?>
<div class="main">
<?php						
echo '<div class="bt_prof">';
		if($cntnm > 0){
			echo '
		<input type="button" class="sub_button" value="Selected" />
	';
		}elseif($user == $tutor_id){
			
		}else{
			echo '<form action="confirmteacher.php?confirm='.$tutor_id.'" method="post">
		<input type="submit" class="sub_button" /></form>'; }
echo '<div class="image"><br><img src="image/profilepic/'.$pro_pic_db.'" width="100px" height="100px"></div>
            <h2>'.$uname_db.'</h2>
            <h5><a class="c_ptime" href="viewpost.php?pid='.$post_id.'">Active'.$time->time_ago($last_login).'</a></h5>
			<table class="table-data" border="1">
			<tr>
			<th>Subject:</th> <td>'.$sub.'</td>
			</tr>
			<tr>
			<th>Class:</th> <td>'.$class.'</td>
			</tr>
			<tr>
			<th>Medium:</th><td>'.$medium.'</td>
			</tr>
			<tr>
			<tr>
			<th>Location:</th> <td>'.$location.'</td>
			</tr>
			<tr>
			<th>University:</th><td>'.$p_university.'</td>
			</tr>
			</table>
        </div>
';}}}
?></center></div>
</div>
<script src="./js/script.js"></script>
</body>
</html>