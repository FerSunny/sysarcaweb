<?php
// conexion a la base de datos bd_arca
	$server = "localhost";
	$user = "admon";
	$password = base64_decode("eTMyd3NnNDVv");
	$bd = "labora41_bd_arca";

	/*
	$server = "localhost";
	$user = "labora41_root";
	$password = "ArcaRoot2017";
	$bd = "labora41_bd_arca";
	*/

	$conexion = mysqli_connect($server, $user, $password, $bd);
	if (!$conexion){
		die('Error de Conexion: ' . mysqli_connect_errno());
	}
       //$conexion->set_charset("utf8");
       
       //$conexion->set_charset("SET NAMES 'utf8'");
       @mysqli_query($conexion, "SET NAMES 'utf8'");

?>
