<?php
date_default_timezone_set('America/Chihuahua');
session_start();
include("../../controladores/conex.php");
$empresa ="1";
$id_usuario=$_SESSION['id_usuario'];
/*
$id_doc= $_SESSION['id_doc'];
$num_version= $_SESSION['num_version'];
$desc_doc = $_SESSION['desc_doc'];
*/
$nuevaversion_p  = $_POST['nuevaversion'];
$id_imagen_p  = $_POST['id_imagen'];
$porcentaje_p  = $_POST['porcentaje'];
$version_p  = $_POST['version'];
$id_doc_p  = $_POST['id_doc'];

$id_doc= $_SESSION['id_doc'];
$num_version= $_SESSION['num_version'];
$desc_doc = $_SESSION['desc_doc'];

//die('id_doc'.$id_doc);

if($nuevaversion_p == 'N'){
	$stm_update=
	"
	update sgc_lista_ficheros
	set estatus = 'O'
	where id_imagen = $id_imagen_p
	";
	$res_update = mysqli_query($conexion, $stm_update);
	if ($res_update){
		//die('update ok'.$id_doc.$num_version.$desc_doc);
		echo "<script>location.href='../tabla_ficheros.php?id_doc=$id_doc&num_version=$num_version&desc_doc=$desc_doc&fk_id_numeral_1=$id_numeral_1&fk_id_numeral_2=$id_numeral_2'</script>";
		//echo "<script>location.href='../tabla_imagenes.php?numero_factura=$numero_factura&studio=$studio'</script>";
	}else{
		die('Nueva version, error --> res_update'.$res_update);
	}
}else{
	// colocamos como obseleto el documento actual
	$stm_update_1=
	"
	update sgc_lista_ficheros
	set estatus = 'A'
	where id_imagen = $id_imagen_p
	";
	$res_update_1 = mysqli_query($conexion, $stm_update_1);
	if ($res_update_1){
		//calculamos la version
		if($porcentaje_p <25){
			$nueva_version = $version_p + .1;
		}else{
			$nueva_version = $version_p + 1;
		}
		//cargamos nuevo fichero (nueva version)
		// rutina para subir los ficheros //  
		$se_subio=0;
		$id_insert=$id_doc;
		$ruta = '../ficheros/'.$id_insert.'/';

		$files_post = $_FILES['fn_archivo'];

		$files = array();
		$file_count = count($files_post['name']);
		$file_keys = array_keys($files_post);

		$permitidos = array("image/gif","image/png","image/jpeg","image/jpg","text/x-comma-separated-values", "text/comma-separated-values", "application/octet-stream", 
		"application/vnd.ms-excel", "application/x-csv", "text/x-csv", "text/csv", "application/csv", "application/excel",
		"application/vnd.msexcel", "text/plain");
		$limite_kb = 4000;
		for ($i=0; $i < $file_count; $i++) 
		{ 
			if($files_post["size"][$i] <= $limite_kb * 1024)
			{
				$archivo = $ruta.$files_post["name"][$i];
				$nombre=$files_post["name"][$i];
				$tipo=$files_post["type"][$i];
				$extension = strtolower(pathinfo($files_post["name"][$i], PATHINFO_EXTENSION));
				if(!file_exists($ruta)){
					mkdir($ruta);
				}
				if(!file_exists($archivo)){
					$resultado = @move_uploaded_file($files_post["tmp_name"][$i], $archivo);
					if($resultado){
						$atributos=getimagesize($archivo);
						//rename ($ruta.$archivo $ruta);
						$se_subio=1;
					} else {
							$nombre= "No pudo subir el fichero";
					}
				} else{
					// rutina para subir los ficheros duplicados
					$nombre= "Archivo ya existe";
				}
			}else{
				$nombre= "Tamaño excedente (max 819,200)";
			}

		}

		// guardamos el nuevo fichero
		$stm_insert_0 = "
		INSERT INTO sgc_lista_ficheros
				(fk_id_empresa,
				 id_imagen,
				 fk_id_doc,
				 fk_id_usuario,
				 fecha_publicacion,
				 ver,
				 revision,
				 nombre,
				 tipo,
				 ruta,
				 fecha_registro,
				 estatus,
				 estado)
		VALUES (1,
				0,
				'$id_doc',
				'$id_usuario',
				NOW(),
				$nueva_version,
				0,
				'$nombre',
				'$extension',
				'$ruta',
				NOW(),
				'O',
				'A');
		";
		$resultado_0 = mysqli_query($conexion, $stm_insert_0);
		if ($resultado) {
			echo "<script>location.href='../tabla_ficheros.php?id_doc=$id_doc&num_version=$num_version&desc_doc=$desc_doc&fk_id_numeral_1=$id_numeral_1&fk_id_numeral_2=$id_numeral_2'</script>";
			//echo "<script>location.href='../tabla_imagenes.php?numero_factura=$numero_factura&studio=$studio'</script>"
		}else{
			die('Error -->  $stm_insert_0'.$stm_insert_0);
		}
	}else{
		die('Version existente, error --> res_update'.$stm_update_1);
	}
}
/*





	// obtenemos el movimiento para copiarlo al histoico antes de sobre escvribirlo
	$stm_select ="
	SELECT * FROM sgc_lista_ficheros
	WHERE estado = 'A'
	AND id_imagen = $id_imagen
	";
	//die('stm_select'.$stm_select);
	if ($res_select = mysqli_query($conexion, $stm_select)) {
		while($row = $res_select->fetch_assoc())
		{

			$fk_id_doc = $row['fk_id_doc'];
			$fk_id_usuario = $row['fk_id_usuario'];
			$fecha_publicacion = $row['fecha_publicacion'];
			$ver = $row['ver'];
			$revision = $row['revision'];
			$nombre = $row['nombre'];
			$tipo = $row['tipo'];
			$ruta = $row['ruta'];

			$stm_insert = "
			INSERT INTO sgc_lista_ficheros
            (fk_id_empresa,
             id_imagen,
             fk_id_doc,
             fk_id_usuario,
             fecha_publicacion,
             ver,
             revision,
             nombre,
             tipo,
             ruta,
             fecha_registro,
             fk_id_usuario_estatus,
             fecha_status,
             estatus,
             estado)
			VALUES (1,
					0,
					$fk_id_doc,
					$fk_id_usuario,
					'$fecha_publicacion',
					$fk_id_doc,
					$revision,
					'$nombre',
					'$tipo',
					'$ruta',
					'$fecha_registro',
					$id_usuario,
					NOW(),
					'A',
					'A');
			";
			$res_insert = mysqli_query($conexion, $stm_insert);
			if ($res_insert){
				$stm_update=
				"
				update sgc_lista_ficheros
				set estatus = 'E'
				where id_imagen = $id_imagen
				";
				$res_update = mysqli_query($conexion, $stm_update);
				if ($res_update){

				}else{
					die('res_update'.$res_update);
				}
			}else{
				die('res_insert'.$res_insert);
			}
		}
	}else{
		die('error al recuperar el documento'.$stm_select);
		//die('Error de Conexión: ' . $query);
		
	}

}else{

if($porcentaje <25){
	$nueva_version = $version + .1;
}else{
	$nueva_version = $version + 1;
}
//die ('Nueva Version: '.$nueva_version) ;
// rutina para subir los ficheros //  
$se_subio=0;
$id_insert=$id_doc;
$ruta = '../ficheros/'.$id_insert.'/';

$files_post = $_FILES['fn_archivo'];

$files = array();
$file_count = count($files_post['name']);
$file_keys = array_keys($files_post);

$permitidos = array("image/gif","image/png","image/jpeg","image/jpg","text/x-comma-separated-values", "text/comma-separated-values", "application/octet-stream", 
"application/vnd.ms-excel", "application/x-csv", "text/x-csv", "text/csv", "application/csv", "application/excel",
"application/vnd.msexcel", "text/plain");
$limite_kb = 4000;
for ($i=0; $i < $file_count; $i++) 
{ 
	if($files_post["size"][$i] <= $limite_kb * 1024)
	{
		$archivo = $ruta.$files_post["name"][$i];
		$nombre=$files_post["name"][$i];
		$tipo=$files_post["type"][$i];
		$extension = strtolower(pathinfo($files_post["name"][$i], PATHINFO_EXTENSION));
		if(!file_exists($ruta)){
			mkdir($ruta);
		}
		if(!file_exists($archivo)){
			$resultado = @move_uploaded_file($files_post["tmp_name"][$i], $archivo);
			if($resultado){
				$atributos=getimagesize($archivo);
				rename ($ruta.$archivo $ruta);
				$se_subio=1;
			} else {
					$nombre= "No pudo subir el fichero";
			}
		} else{
			// rutina para subir los ficheros duplicados
			$nombre= "Archivo ya existe";
		}
	}else{
		$nombre= "Tamaño excedente (max 819,200)";
	}
	$query = "
	INSERT INTO sgc_lista_ficheros
            (fk_id_empresa,
             id_imagen,
             fk_id_doc,
             fk_id_usuario,
             fecha_publicacion,
             ver,
             revision,
             nombre,
             tipo,
             ruta,
             fecha_registro,
             estatus,
             estado)
	VALUES (1,
			0,
			'$id_doc',
			'$id_usuario',
			NOW(),
			$nueva_version,
			0,
			'$nombre',
			'$extension',
			'$ruta',
			NOW(),
			'C',
			'A');
	";
	//die('$query'.$query);
    $resultado = mysqli_query($conexion, $query);
	if ($resultado) {
		if($se_subio == 1){
			$stm_update1=
			"
			update sgc_lista_ficheros
			set estatus = 'E'
			where id_imagen = $id_imagen
			";
			$res_update1 = mysqli_query($conexion, $stm_update1);
			if ($res_update1){

			}else{
				die('res_update'.$res_update1);
			}			
		}

		header("location: ../tabla_ficheros.php?id_doc=$id_doc&num_version=$num_version&desc_doc=$desc_doc");
		//echo "<script>location.href='../tabla_usuarios.php'</script>";
	}
	else {
		echo "error en la ejecucion de la consulta. <br />";
		  die('Error de Conexión: ' . $query);
	}
}

}
*/
//header("location: ../tabla_ficheros.php?id_doc=$fk_id_doc&num_version=$$ver&desc_doc=$nombre");

?>
