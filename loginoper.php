<?php
$response = array();
include 'db/db_connect.php';
include 'functions.php';

//Get the input request parameters
$inputJSON = trim(file_get_contents("php://input"));
$inputt = json_decode($inputJSON, TRUE); //convert JSON into array

//Check for Mandatory parameters
if(isset($inputt['reso_username']) && isset($inputt['password'])){
	$username = $inputt['reso_username'];
	$password = $inputt['password'];
	$query    = "SELECT reso_name,password_hash, salt FROM member WHERE reso_username = ?";

	if($stmt = $con->prepare($query)){
		$stmt->bind_param("s",$username);
		$stmt->execute();
		$stmt->bind_result($name,$passwordHashDB,$salt);
		if($stmt->fetch()){
			//Validate the password
			if(password_verify(concatPasswordWithSalt($password,$salt),$passwordHashDB)){
				$response["status"] = 0;
				$response["message"] = "Login successful";
				$response["full_name"] = $name;
			}
			else{
				$response["status"] = 1;
				$response["message"] = "Invalid username and password combination";
			}
		}
		else{
			$response["status"] = 1;
			$response["message"] = "Invalid username and password combination";
		}
		
		$stmt->close();
	}
}
else{
	$response["status"] = 2;
	$response["message"] = "Missing mandatory parameters";
}
//Display the JSON response
echo json_encode($response);
?>