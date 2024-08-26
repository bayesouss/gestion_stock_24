<?php 
   // $bdd= new PDO('mysql:host=127.0.0.1;dbname=ges_stock','root','');
   // $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try{
	$bdd= new PDO('mysql:host=127.0.0.1;dbname=ges_stock','root','');
}catch(Exception $e){
	die('Erreur de conexion:'.$e->getMessage());
}

// $servername = "localhost";
// 	$username = "root";
// 	$password = "";
// 	$database = "ges_stock";

// 	// Create connection
// 	$bdd = new mysqli($servername, $username, $password, $database);

// 	// Check connection
// 	if ($bdd->connect_error) {
// 	    die("Connection failed: " . $bdd->connect_error);
// 	} 


     // $bdd = new PDO('mysql:host=localhost;dbname=ges_stock','root','');


//    $sql = 'select * from client';
// $data = $bdd->query($sql);
// $rows = $data->fetchAll();
// $nblig_client = count($rows);

 ?>