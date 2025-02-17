<?php

session_start();

date_default_timezone_set('America/Mexico_City');
header('Content-Type: text/html; charset=ISO-8859-1');
require('../../fpdf/fpdf.php');
 require_once ("../../so_factura/config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
 require_once ("../../so_factura/config/conexion.php");//Contiene funcion que conecta a la base de datos

 $id_usuario=$_SESSION['id_usuario'];

//echo 'usuario:'.$id_usuario;

//se recibe los paramteros para la generación del reporte
$numero_factura=$_GET['numero_factura'];
$studio=$_GET['studio'];

// actualiza las veces que se ha impreso el resultado
$sql_max="select max(num_imp) as num_imp FROM cr_plantilla_esp_re
where fk_id_factura=".$numero_factura." and fk_id_estudio=".$studio;
// echo $sql_max;
$veces='0';
if ($result = mysqli_query($con, $sql_max)) {
  while($row = $result->fetch_assoc())
  {
      $veces=$row['num_imp']+1;
      //echo $veces;
      $sql_update="UPDATE cr_plantilla_esp_re SET num_imp = '".$veces."'
      where fk_id_factura=".$numero_factura." and fk_id_estudio=".$studio;
      //echo $sql_update;
      $execute_query_update = mysqli_query($con,$sql_update);
  }
}

// OBTENEMOS LOS DATOS DE LA ESTUDIO REGISTRADO
$sql_usg="SELECT us.nombre_plantilla, us.titulo_desc, us.descripcion, us.firma, us.fk_id_medico
FROM cr_plantilla_esp_re us 
WHERE us.estado = 'A'
AND fk_id_factura=".$numero_factura." and fk_id_estudio=".$studio;

//echo $sql_usg;

if ($result = mysqli_query($con, $sql_usg)) {
  while($row = $result->fetch_assoc())
  {
      $titulo_desc=$row['titulo_desc'];
      $descripcion=$row['descripcion'];
      $firma=$row['firma'];
	  $fk_id_medico=$row['fk_id_medico'];

  }
}


//Obtener los datos, de la cabecera, (datos del estudio)
$sql="
SELECT fa.id_factura,
  SUBSTR(es.desc_estudio,1,32) AS estudio,
  SUBSTR(es.desc_estudio,33,100) AS estudio1,
  es.desc_estudio AS estudio2,
  CONCAT(cl.nombre,' ',cl.a_paterno,' ',cl.a_materno) AS paciente,
  CASE
    WHEN LENGTH(fa.vmedico) > 0 THEN
      trim(fa.vmedico)
    ELSE
      CONCAT(me.nombre,' ',me.a_paterno,' ',me.a_materno) 
  END AS medico,
  DATE(fa.`fecha_factura`) AS fecha,
  CASE WHEN cl.anios > 0 THEN 
    CONCAT(cl.anios,' Años') 
        WHEN cl.meses > 0 THEN 
    CONCAT(cl.meses,' Meses') 
        WHEN cl.dias > 0 THEN 
    CONCAT(cl.dias,' Dias') 
  END AS edad
FROM so_factura fa
     LEFT OUTER JOIN so_clientes cl ON (cl.id_cliente = fa.fk_id_cliente)
     LEFT OUTER JOIN so_medicos me ON (me.id_medico = fa.fk_id_medico),
     so_detalle_factura df, 
     km_perfil_detalle dp,
     km_estudios es
WHERE fa.`id_factura` = ".$numero_factura."
  AND fa.`id_factura` = df.`id_factura`
  AND df.`fk_id_estudio`= dp.`fk_id_perfil`
  AND dp.`fk_id_estudio` = es.`id_estudio`
  AND dp.fk_id_estudio = ".$studio."
UNION

    SELECT df.id_factura,
       SUBSTR(es.desc_estudio,1,32) AS estudio,
       SUBSTR(es.desc_estudio,33,100) AS estudio1, 
       es.desc_estudio AS estudio2,
       CONCAT(cl.nombre,' ',cl.a_paterno,' ',cl.a_materno) AS paciente,
      CASE
        WHEN LENGTH(fa.vmedico) > 0 THEN
          trim(fa.vmedico)
        ELSE
          CONCAT(me.nombre,' ',me.a_paterno,' ',me.a_materno) 
      END AS medico,
    date(fa.`fecha_factura`) AS fecha,
    CASE WHEN cl.anios > 0 THEN 
        CONCAT(cl.anios,' Años') 
         WHEN cl.meses > 0 THEN 
        CONCAT(cl.meses,' Meses') 
         WHEN cl.dias > 0 THEN 
        CONCAT(cl.dias,' Dias') 
    END AS edad 
FROM km_paquetes pq
     LEFT OUTER JOIN km_estudios es ON (es.id_estudio = pq.fk_id_estudio),
     so_detalle_factura df,
     so_factura fa
     LEFT OUTER JOIN so_clientes cl ON (cl.id_cliente = fa.fk_id_cliente) 
     LEFT OUTER JOIN so_medicos me ON (me.id_medico = fa.fk_id_medico) 
WHERE  pq.id_paquete = df.fk_id_estudio
   AND df.id_factura = fa.id_factura
   AND df.id_factura = ".$numero_factura." AND pq.fk_id_estudio = ".$studio."
    UNION
    SELECT  df.id_factura,
    substr(es.desc_estudio,1,32) AS estudio,
    substr(es.desc_estudio,33,100) AS estudio1,
    es.desc_estudio AS estudio2,
    CONCAT(cl.nombre,' ',cl.a_paterno,' ',cl.a_materno) AS paciente,
      CASE
        WHEN LENGTH(fa.vmedico) > 0 THEN
          trim(fa.vmedico)
        ELSE
          CONCAT(me.nombre,' ',me.a_paterno,' ',me.a_materno) 
      END AS medico,
    date(fa.fecha_factura) AS fecha,
    CASE
        WHEN cl.anios > 0 THEN 
            CONCAT(cl.anios,' Años')
        WHEN cl.meses > 0 THEN 
            CONCAT(cl.meses,' Meses')
        WHEN cl.dias > 0 THEN 
            CONCAT(cl.dias,' Dias') 
    END AS edad
  FROM so_detalle_factura df
  LEFT OUTER JOIN so_factura fa ON (fa.id_factura=df.id_factura)
  LEFT OUTER JOIN km_estudios es ON (es.id_estudio = df.fk_id_estudio)
  LEFT OUTER JOIN so_clientes cl ON (cl.id_cliente = fa.fk_id_cliente)
  LEFT OUTER JOIN so_medicos me ON (me.id_medico = fa.fk_id_medico)
  WHERE df.id_factura = ".$numero_factura." AND df.fk_id_estudio=".$studio;
 //echo $sql;

  $paciente='0';

     if ($result = mysqli_query($con, $sql)) {
        while($row = $result->fetch_assoc())
        {
            $paciente=$row['paciente'];
            $medico=$row['medico'];
            $fecha=$row['fecha'];
            //$estudio=$row['estudio'];
            $edad=utf8_decode($row['edad']);
            $estudio2=$row['estudio2'];
            //$estudio1=$row['estudio1'];
        }
    }

// OBTENEMOS LOS DATOS DE las imagenes guardadas
$sql_img="SELECT a.*
FROM cr_plantilla_esp_img a 
WHERE a.estado = 'A'
AND fk_id_factura=".$numero_factura." and fk_id_estudio=".$studio." limit 1";

//echo $sql_usg;

if ($result = mysqli_query($con, $sql_img)) {
  while($row = $result->fetch_assoc())
  {
      $nombre_img=$row['nombre'];
      $ruta_img=$row['ruta'];
      $alto_img=$row['alto'];
      $ancho_img=$row['ancho'];

  }
}


class PDF extends FPDF
{
// Cabecera de página
function Header()
{

    global $paciente,
            $medico,
            $numero_factura,
            $fecha,
            $estudio2,
            $studio,
            $edad,
            $metodo,
            $posinim,
            $tipfuem,
            $tamfuem,
            $titulo_desc;

    //$this->Image('../../imagenes/logo_lab3.jpg',65,5,80,0);
    $this->Image('../imagenes/logo_lab.jpg',45,3,130,27);
    $this->Image('../imagenes/codigo3.png',179,40,20,20);

/*
    $this->Image('../imagenes/logo_arca.png',15,5,140,0);
    $this->Image('../imagenes/pacal.jpg',160,5,40,0);
    $this->Image('../imagenes/codigo1.jpg',170,50,30,30);
*/
    //$this->Ln(18);
    //$this->Cell(5);
    $this->SetFont('Arial','B',15);
    //$this->SetDrawColor(0,80,180);
   //$this->SetFillColor(230,230,0);
    $this->SetTextColor(0,0,255);
    //$this->Cell(185,5,'UNIDAD CENTRAL ARCA TULYEHUALCO ',0,0,'C');
    $this->Ln(13);
    $this->SetFont('Arial','I',10);
    //$this->Cell(185,5,utf8_decode('Av. Cuauhtémoc No. 27 Local 6 Col. Centro, Chalco, Edo Mex. C.P.56600'),0,0,'C');
    //$this->Cell(195,5,'Blvd San Buenaventura No. 51, Col. La Venta, Ixtapaluca EdoMex',0,0,'C');
    $this->Ln(3);
    $this->SetTextColor(0,0,255);
    $this->Cell(193,5,'________________________________________________________________________________________________',0,0,'C');
    $this->SetTextColor(0,0,0);

// Primer columna de titulos
    $this->Ln(9);
    $this->Cell(2);
    $this->SetFont('Arial','B',11);
    $this->Cell(22,5,'PACIENTE:',0,0,'L');
    $this->SetFont('Arial','',11);
    $this->Cell(87,5,utf8_decode($paciente),0,0,'L');

    $this->SetFont('Arial','B',11);
    $this->Cell(15,5,'DR(A):',0,0,'L');
    $this->SetFont('Arial','',11);
    $this->Cell(70,5,utf8_decode($medico),0,0,'L');
// Segunda linea
    $this->ln(5);
    $this->Cell(2);
    $this->SetFont('Arial','B',11);
    $this->Cell(22,5,'FOLIO:',0,0,'L');
    $this->SetFont('Arial','',11);
    $this->Cell(87,5,$numero_factura,0,0,'L');

    $this->SetFont('Arial','B',11);
    $this->Cell(15,5,'FECHA:',0,0,'L');
    $this->SetFont('Arial','',9);
    $this->Cell(81,5,$fecha,0,0,'L');

// Tercer linea
    $this->ln(5);
    $this->Cell(2);
    $this->SetFont('Arial','B',11);
    $this->Cell(22,5,'ESTUDIO:',0,0,'L');
    $this->SetFont('Arial','',11);
    $this->MultiCell(88,5,utf8_decode($estudio2),0,'L');
 
    $this->SetFont('Arial','B',11);
    $this->SetXY(121, 48); 
    $this->Write(0,'EDAD:'); 
    $this->SetFont('Arial','',11);
    $this->SetXY(137,48); 
    $this->Write(0,$edad);

// Cuarta linea (nombre del estudio - plantilla -)
    $this->ln(15);
    $this->Cell(5);
    $this->SetFont('Arial','B',14);
    $this->Cell(150,5,utf8_decode($titulo_desc),0,0,'C'); 
   

    $this->Ln();

}

// Pie de página
  function Footer()
  {

    global $studio,$con,$verificado,$tamfuev,$tipfuev,$posiniv,$id_usuario,$fk_id_medico,$fecha;

	  //echo 'Usuario:'.$id_usuario;
	  
    $this->SetY(-40); //
    //$this->ln(10);
    $this->Cell($posiniv);
	//echo 'usuario: '.$fk_id_medico;

/*
    switch ($fk_id_medico){
        case 33:
            $this->Image('../imagenes/frima_dra isabel.jpg',77,215,42,0);
            break;
        case 4:
            $this->Image('../imagenes/firmajoaquinramirez.jpg',77,220,55,0);
            break;
        case 17:
            $this->Image('../imagenes/firmajoaquinramirez.jpg',77,220,55,0);
            break;
        case 37:
            $this->Image('../imagenes/firma_dra_silvia.jpg',77,205,45,0);
            break;
        case 41:
            $this->Image('../imagenes/firma_dra_Barbara.jpeg',77,205,45,0);
        case 46:
          $this->Image('../imagenes/firma_dr_misael.jpeg',77,205,45,0);
        case 48:
          $this->Image('../imagenes/firma_dr_jesus_sanchez.jpg',77,225,50,0);
    }
*/

    $this->Image('../imagenes/firma_juan_silva.jpg',77,225,40,0);

/*
    if ($fk_id_medico==33)
    {
      $this->Image('../imagenes/frima_dra isabel.jpg',77,220,42,0);
    }
    else
    {
		if ($fk_id_medico==4 or $fk_id_medico==17)
		{
      		$this->Image('../imagenes/firmajoaquinramirez.jpg',77,220,55,0);
		}else
		{
			if ($fk_id_medico==37)
			{
				$this->Image('../imagenes/firma_dra_silvia.jpg',77,205,45,0);
			}
		}
    }
*/	  
    $this->SetFont('Arial',$tipfuev,$tamfuev);
    $this->Cell(30,5,$verificado,0,0,'L'); 
    $this->ln(10); // aqui
    //$this->Cell(5);

    $sql="SELECT p2.concepto,posini,tipfue,tamfue FROM cr_plantilla_esp p2 WHERE p2.fk_id_estudio =".$studio." AND p2.estado = 'A' AND p2.tipo = 'F' order by orden";
    if ($result = mysqli_query($con, $sql)) {
      while($row = $result->fetch_assoc())
        {
          $this->Cell(($row['posini']-=6));
          $firma=$row['concepto'];
          //$this->Image('../imagenes/firma.gif',153,225,40,0);
          $this->SetFont('Arial','',$row['tamfue']);
          $this->Cell(170,5,$firma,0,0,'L');
          $this->ln(4);
        }

        $this->ln(-2);
        $this->Cell(5);
        $this->SetTextColor(0,0,255);
        $this->Cell(185,5,'_______________________________________________________________________________________________________________________',0,0,'L');
        $this->SetTextColor(26,35,126); 
        $this->SetFont('Arial','B',10);
        $this->SetXY(118,260); 
        $this->Write(0,'www.estudiosclinicosanbuenaventura.com.mx-'.$id_usuario);
    
        $this->SetTextColor(27,94,32); 
        $this->SetFont('Arial','',12);
        $this->SetXY(15,260); 
        $this->Write(0,'Matriz:');
    
        $this->SetTextColor(27,94,32); 
        $this->SetFont('Arial','',10);
        $this->SetXY(15,264); 
        $this->Write(0,'Ixtapaluca');
    
        $this->SetXY(15,268); 
        $this->SetFont('Arial','',8);
        $this->Write(0,'Blvd. San Buenaventura S/N (Frente al INE)');
    
        $this->SetXY(15,271);
        $this->Write(0,'Col. La Venta');
        
        $this->SetXY(15,274);
        $this->Write(0,'Ixtapaluca Edo. Mex.');
    
        $this->SetXY(15,277);
        $this->Write(0,'Tel. 55 5972 5169 - 55 6298 2670');
    
    // sucursal
		if($fecha < '2021-06-01'){
			$this->SetTextColor(27,94,32); 
			$this->SetFont('Arial','',12);
			$this->SetXY(65,260); 
			$this->Write(0,'Sucursal:');

			$this->SetTextColor(27,94,32); 
			$this->SetFont('Arial','',10);
			$this->SetXY(65,264); 
			$this->Write(0,'Chalco');

			$this->SetXY(65,268); 
			$this->SetFont('Arial','',8);
			$this->Write(0,'Av. Cuauhutemoc No. 27 Local 6');

			$this->SetXY(65,271);
			$this->Write(0,'Col. Centro');

			$this->SetXY(65,274);
			$this->Write(0,'Chalco Edo. Mex.');

			$this->SetXY(65,277);
			$this->Write(0,'Tel. 55 8865 1720 - 55 8865 1721');
		}

			$this->SetTextColor(26,35,126); 
			$this->SetFont('Arial','B',9);
			$this->SetXY(114,266); 
			$this->Write(0,'atencion.cliente@estudiosclinicosanbuenaventura.com.mx');
			$this->SetXY(135,271); 
			$this->Write(0,'Estudios Clinicos San Buenaventura');    
			$this->Image('../imagenes/fb.png',130,268,5,5);

			$this->SetXY(142,276); 
			$this->Write(0,'estclinbuenaventura');
			$this->Image('../imagenes/instagram.png',137,273,5,5);  
			$this->SetTextColor(0,0,0);

			$this->SetTextColor(26,35,126); 
			$this->SetFont('Arial','B',10);
			$this->SetXY(90,272); 

    
        $this->SetTextColor(0,0,0);//subir codigo de seguridad
    }
  }
}
//
// Creación del objeto de la clase heredada
//
$pdf = new PDF('P','mm','Letter');
//$pdf->SetMargins(0,0,0);
$pdf->SetAutoPageBreak(true,40);

$pdf->AliasNbPages();
$pdf->AddPage();



$pdf->Image($ruta_img.'/'.$nombre_img   ,35,55,140,140);

    $pdf->ln(-6);
    $pdf->Cell(15);
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(75,5,utf8_decode($titulo_desc),0,0,'C'); 

$pdf->ln(140);
$pdf->Cell(2);
$pdf->SetFont('Arial','',10);
$pdf->MultiCell(185,5,utf8_decode($descripcion),0,'J');

$pdf->ln(1);
$pdf->Cell(2);
$pdf->SetFont('Arial','B',8);
//$pdf->MultiCell(55,5,trim($firma),0,'L');
//$this->Image('../imagenes/firmajoaquinramirez.jpg',153,225,40,0);


//for($i=1;$i<=20;$i++)
//    $pdf->Cell(0,10,'Imprimiendo línea número '.$i,0,1);

$pdf->Output();
?>