<?php



	include ("../controladores/conex.php");



  

 

  $query = "
  SELECT * FROM no_turnos ho
  WHERE ho.`estado` = 'A'
  ";


	$resultado = mysqli_query($conexion, $query);



    if(!$resultado){

        die("Error");



    }else{

        while($data=mysqli_fetch_assoc($resultado)){

            $arreglo["data"][]=$data;

        }

        echo json_encode($arreglo);

    }



    mysqli_free_result($resultado);

    mysqli_close($conexion);

