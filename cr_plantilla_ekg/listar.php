<?php

  session_start();
	include ("../controladores/conex.php");
  $id_usuario=$_SESSION['id_usuario'];
  $fk_id_perfil=$_SESSION['fk_id_perfil'];
  //header("Content-Type: text/html;charset=utf-8");	
      if ($fk_id_perfil==1 or $fk_id_perfil==45 or $fk_id_perfil==46 or $fk_id_perfil==33 ) 
    {
      $condicion=' > 0';
    }
    else
    {
      //if($fk_id_perfil==9){
      //  $condicion=" IN (SELECT ru.fk_id_sucursal FROM cr_plantilla_ruta ru WHERE fk_id_medico = ".$id_usuario.")";
      //}else{
        $condicion=' = '.$id_usuario; 
     // }
    }

  $query = "SELECT 
  p1.`fk_id_empresa`,
  p1.`fk_id_medico`,
  p1.`id_plantilla`,
  p1.`nombre_plantilla`,
  p1.`fk_id_estudio`,
  p1.titulo_desc,
  p1.`descripcion`,
  concat(substr(p1.`descripcion`,1,75),'... ') as desc_corta,
  p1.`estado`,
  p1.firma,
  es.desc_estudio,
  CONCAT(us.nombre,' ',us.a_paterno,' ',us.a_materno) AS nom_usuario
FROM cr_plantilla_ekg p1
LEFT OUTER JOIN km_estudios es ON (es.id_estudio = p1.fk_id_estudio)
LEFT OUTER JOIN se_usuarios us ON (us.id_usuario = p1.fk_id_medico)
WHERE p1.estado = 'A' AND p1.fk_id_medico ".$condicion."
ORDER BY us.nombre,us.a_paterno,us.a_materno,p1.fk_id_estudio,p1.nombre_plantilla";


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
