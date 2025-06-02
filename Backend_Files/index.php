<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vue_tutorial";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


if (isset($_SERVER['HTTP_ORIGIN'])) {
	// Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
	// you want to allow, and if so:
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Max-Age: 1000');
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
			// may also be using PUT, PATCH, HEAD etc
			header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
	}

	if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
			header("Access-Control-Allow-Headers: Accept, Content-Type, Content-Length, Accept-Encoding, X-CSRF-Token, Authorization");
	}
	exit(0);
}

$res = array('error' => false);
$action='';

if (isset($_GET['action'])) {
	
	$action=$_GET['action'];
}
if($action=='login'){
    $username=$_POST['username'];
    $password=$_POST['password'];
    $sql="Select * from admin where username='$username' AND password='$password'";
    $result=$conn->query($sql);
    $num=mysqli_num_rows($result);
    if($num > 0){
        $res['message']="Login Successfuly";
    }
    else{
        $res['error']=true;
        $res['message']="Your Login Username or Password is invalid";
    }
}

if($action=='addusers'){

	$name=$_POST['name'];
	$email=$_POST['email'];
	$education=$_POST['education'];
	 
	$sql="INSERT INTO `usersdata`(`id`, `name`, `email`, `education`) VALUES(NULL,'$name','$email','$education')";
	$result=$conn->query($sql);
	if($result===true){
		$res['error']=false;
        $res['message']="User Added Successfully";
	}else{
		$res['error']=true;
        $res['message']="Somthing Went Wrong";
	}

}


if($action=='getusersinfo'){
	$sql="SELECT * FROM `usersdata`";
	$result=$conn->query($sql);
	$num=mysqli_num_rows($result);
	$userData=array();
	if($num >0){
		while($row=$result->fetch_assoc()){
			array_push($userData,$row);
		}
		$res['error']=false;
        $res['user_Data']=$userData;

	}else{
		$res['error']=false;
        $res['message']="No Data Found!";
	}
}

if($action=='editusers'){
	$id=$_POST['id'];

	$name=$_POST['name'];
	$email=$_POST['email'];
	$education=$_POST['education'];

	$sql = "UPDATE `usersdata` SET `name` = '$name', `email` = '$email', `education` = '$education' WHERE `usersdata`.`id` = '$id'  ";

	$result=$conn->query($sql);
	if($result===true){
		$res['error']=false;
        $res['message']="User Added Successfully";
	}else{
		$res['error']=true;
        $res['message']="Somthing Went Wrong";
	}

}

if($action=='deleteusers'){

	$json_data = file_get_contents('php://input');
	//  // Para POST
	$data = json_decode($json_data, true);
	$id = $data['id'];

	$stmt = $conn->prepare("DELETE FROM usersdata WHERE `usersdata`.`id` = '$id' ");

	if ($stmt->execute()) {
		// Verificar si se eliminó una fila
		if ($stmt->affected_rows > 0) {
			echo json_encode(['success' => true, 'message' => 'Fila eliminada exitosamente']);
		} else {
			echo json_encode(['success' => false, 'message' => 'No se eliminó ninguna fila']);
		}

	} else {
		echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta']);
	}

	$stmt->close();

}




$conn -> close();
header("Content-type: application/json");
echo json_encode($res);
die();
 ?>