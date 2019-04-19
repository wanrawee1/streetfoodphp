<?php
$response = array();
include 'db/db_connect.php';
include 'functions.php';

//Get the input request parameters
$inputJSON = trim(file_get_contents("php://input"));
$inputt = json_decode($inputJSON, TRUE); //convert JSON into array

//Check for Mandatory parameters
if(isset($inputt['reso_username']) && isset($inputt['password']) && isset($inputt['reso_name']) && isset($inputt['idcard']) 
&& isset($inputt['reso_address']) && isset($inputt['reso_count']) && isset($inputt['reso_tel']) && isset($inputt['reso_email'])){
	$username = $inputt['reso_username'];
	$password = $inputt['password'];
    $name = $inputt['reso_name'];
    $idcard = $inputt['idcard'];
	$address = $inputt['reso_address'];
    $country = $inputt['reso_count'];
    $tel = $inputt['reso_tel'];
	$email = $inputt['reso_email'];
	
	//Check if user already exist
	if(!userExists($username)){

		//Get a unique Salt
		$salt = getSalt();
		
		//Generate a unique password Hash
		$passwordHash = reso_password_hash(concatPasswordWithSalt($password,$salt),PASSWORD_DEFAULT);
		
		//Query to register new user
		$insertQuery  = "INSERT INTO restaurantoperator(reso_username, reso_name, reso_password_hash, reso_salt, idcard, reso_address, reso_count, reso_tel, reso_email) VALUES (?,?,?,?,?,?,?,?,?)";
		if($stmt = $con->prepare($insertQuery)){
			$stmt->bind_param("sssssssss",$username,$name,$passwordHash,$salt,$idcard,$address,$country,$tel,$email);
			$stmt->execute();
			$response["status"] = 0;
			$response["message"] = "User created";
			$stmt->close();
		}
    }

	else{
		$response["status"] = 1;
		$response["message"] = "User exists";
	}
}
else{
	$response["status"] = 2;
	$response["message"] = "Missing mandatory parameters";
}
echo json_encode($response);
?>