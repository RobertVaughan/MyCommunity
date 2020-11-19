<?php
session_start();

$con = mysqli_connect("localhost", "bcsm_magister", "#Bara@2174@Cuda#", "bcsm_magister");
if(mysqli_connect_error()) {
	echo "failed to connect: " . mysqli_connect_errno();
}

// Declaring variables to prevent errors

$fName = ""; //First Name
$lName = ""; //Last Name
$uName = ""; //Username
$em1 = ""; //Email Address
$em2 = ""; //Email Address 2
$password1 = ""; //Password 1
$password2 = ""; //Password 2
$date = ""; //Sign Up Date
$error_array = array(); //Holds Error Messages

if(isset($_POST['register_button'])){
	//Registration Form Validation
	
	//First Name
	$fName = strip_tags($_POST['reg_fname']); //remove html tags
	$fName = str_replace(' ','', $fName); //remove spaces
	$fName = ucfirst(strtolower($fName)); //makes everything lower case then the first letter capital
	$_SESSION['reg_fname']= $fName;
	
	//Last Name
	$lName = strip_tags($_POST['reg_lname']);
	$lName = str_replace(' ','', $lName); //remove spaces
	$lName = ucfirst(strtolower($lName)); //makes everything lower case then the first letter capital
	$_SESSION['reg_lname']= $lName;
	
	//Username
	$uName = strip_tags($_POST['reg_uname']);
	$uName = str_replace(' ','', $uName); //remove spaces
	$_SESSION['reg_uname']= $uName;
	
	//Email Address 
	$em1 = strip_tags($_POST['reg_email1']);
	$em1 = str_replace(' ','', $em1); //remove spaces
	$_SESSION['reg_email1']= $em1;
	
	$em2 = strip_tags($_POST['reg_email2']);
	$em2 = str_replace(' ','', $em2); //remove spaces
	$_SESSION['reg_email2']= $em2;
	
	//Password
	$password1 = strip_tags($_POST['reg_password1']);
	$password2 = strip_tags($_POST['reg_password2']);
	
	$date = date("Y-m-d"); // Gets Current Date
	
	//Eventually replace with javascript/AJAX
	
	if($em1 == $em2){
		//check if email is in a valid format
		if(filter_var($em1, FILTER_VALIDATE_EMAIL)){
			$em1 = filter_var($em1, FILTER_VALIDATE_EMAIL);
			
			//Check to see if Email Already Exists
			$e_check = mysqli_query($con, "SELECT email from users WHERE email='$em1'");
			
			//count number of rows returned
			$num_rows = mysqli_num_rows($e_check);
			
			if($num_rows > 0){
				array_push($error_array, "Email is already in use<br>");
			}
		} else {
			array_push($error_array, "Invalid Format!<br>");
		}
		
	} else {
		array_push($error_array, "Email Addresses do not match!<br>");
	}
	
	if(strlen($fName) > 32 || strlen($fName) < 2){
		array_push($error_array, "Your first name must be between 2 and 32 characters<br>");
	}
	
	if(strlen($lName) > 32 || strlen($lName) < 2){
		array_push($error_array, "Your last name must be between 2 and 32 characters<br>");
	}
	
	if($password1 != $password2) {
		array_push($error_array, "Your passwords do not match!<br>");
	} 
	
	if(strlen($password1 > 64 || strlen($password1) < 16 )) {
		array_push($error_array, "Your password must be between 16 and 64 characters in length<br>");
	}
	
	if(empty($error_array)) {
		$password1 = md5($password1); //encrypt password before storing in DB

		//Check to see if username Already Exists
		$i = 0;
		$u_check = mysqli_query($con, "SELECT username from users WHERE username='$uName'");
		$num_rows = mysqli_num_rows($u_check);
		if($num_rows > 0){
			array_push($error_array, "Username is already in use<br>");
			while(mysqli_num_rows($u_check) != 0){
				$i++;
				$uName = $uName . "_" . $i;
				$u_check = mysqli_query($con, "SELECT username from users WHERE username='$uName'");
			}
		}
		
		//Profile Picture Assignment
		$rand = rand(1,2); //random number between 1 and 16
		if ($rand = 1)
			$profile_pic = "assets/images/profile_img/default/head_deep_blue.png";
		else if ($rand = 2)
			$profile_pic = "assets/images/profile_img/default/head_emerald.png";
		
		$query = mysqli_query($con, "INSERT INTO users VALUES (NULL,'$fName', '$lName', '$uName', '$em1', '$password1', '$date', '$profile_pic', '0', '0', '0', ',')");
		array_push($error_array, "<span style='color: #5575B7;'>You're now registered, please log in!</span><br>");
	}
}

?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"> 
<html>
	<head>
		<title>My Community Network</title>
	</head>
	<body>
		<form action="register.php" method="post">
			<label id="reg_fname_label"><span aria-hidden="true">(*)</span> First Name: </label>
			<input type="text" name="reg_fname" placeholder="First Name" aria-labelledby="reg_fname_label" required value="<?php if(isset($_SESSION['reg_fname'])) { echo $_SESSION['reg_fname'];} ?>">
			<br>
			<?php if(in_array("Your first name must be between 2 and 32 characters<br>", $error_array)) echo "Your first name must be between 2 and 32 characters<br>";?>
			<label id="reg_lname_label"><span aria-hidden="true">(*)</span> Last Name: </label>
			<input type="text" name="reg_lname" placeholder="Last Name" aria-labelledby="reg_lname_label"  required value="<?php if(isset($_SESSION['reg_lname'])) { echo $_SESSION['reg_lname'];} ?>">
			<br>
			<?php if(in_array("Your last name must be between 2 and 32 characters<br>", $error_array)) echo "Your last name must be between 2 and 32 characters<br>";?>
			<br>
			<label id="reg_uname_label"><span aria-hidden="true">(*)</span> Username: </label>
			<input type="text" name="reg_uname" placeholder="Username" aria-labelledby="reg_uname_label" required value="<?php if(isset($_SESSION['reg_uname'])) { echo $_SESSION['reg_uname'];} ?>">
			<br>
			<?php if(in_array("Username is already in use<br>", $error_array)) echo "Username is already in use<br>";?>
			<br>
			<label id="reg_email1_label"><span aria-hidden="true">(*)</span> Enter Email Address: </label>
			<input type="email" name="reg_email1" placeholder="Enter Email Address" aria-labelledby="reg_email1_label" required value="<?php if(isset($_SESSION['reg_email1'])) { echo $_SESSION['reg_email1'];} ?>">
			<br>
			<label id="reg_email2_label"><span aria-hidden="true">(*)</span> Confirm Email Address: </label>
			<input type="email" name="reg_email2" placeholder="Confirm Email Address" aria-labelledby="reg_email2_label" required value="<?php if(isset($_SESSION['reg_email2'])) { echo $_SESSION['reg_email2'];} ?>">
			<br>
			<?php 
				if(in_array("Email is already in use<br>", $error_array)) echo "Email is already in use<br>";
				else if(in_array("Invalid Format!<br>", $error_array)) echo "Invalid Format!<br>";
				else if(in_array("Email Addresses do not match!<br>", $error_array)) echo "Email Addresses do not match!<br>";
			?>
			<br>
			<label id="reg_password1_label"><span aria-hidden="true">(*)</span> Enter Password: </label>
			<input type="password" name="reg_password1" placeholder="Enter Password" aria-labelledby="reg_password1_label" required>
			<br>
			<label id="reg_password2_label"><span aria-hidden="true">(*)</span> Confirm Password: </label>
			<input type="password" name="reg_password2" placeholder="Confirm Password" aria-labelledby="reg_password2_label" required>
			<br>
			<?php 
				if(in_array("Your passwords do not match!<br>", $error_array)) echo "Your passwords do not match!<br>";
				else if(in_array("Your password must be between 16 and 64 characters in length<br>", $error_array)) echo "Your password must be between 16 and 64 characters in length<br>";
			?>
			<br>
			<span>(*) Indicates Required Field</span>
			<br>
			<input type="submit" name="register_button" value="Register">
			<br>
			<?php if(in_array("<span style='color: #5575B7;'>You're now registered, please log in!</span><br>", $error_array)) echo "<span style='color: #5575B7;'>You're now registered, please log in!</span><br>"?>
			
		</form>
	</body>
</html>