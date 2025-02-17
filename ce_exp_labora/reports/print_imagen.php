<?php
session_start();
date_default_timezone_set('America/Mexico_City');
header('Content-Type: text/html; charset=ISO-8859-1');
require('../../fpdf/fpdf.php');
 require_once ("../../so_factura/config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
 require_once ("../../so_factura/config/conexion.php");//Contiene funcion que conecta a la base de datos

//se recibe los paramteros para la generación del reporte
$numero_factura=$_GET['numero_factura'];
$studio=$_GET['studio'];



// Contamos la cantidad de imagenes que tiene el estudio
$sql_max="select count(id_imagen) as num_img,max(img_x_hoja) as img_x_hoja FROM cr_plantilla_ekg_img
where estado = 'A' and fk_id_factura=".$numero_factura." and fk_id_estudio=".$studio;
// echo $sql_max;
$num_img='0';
if ($result = mysqli_query($con, $sql_max)) {
  while($row = $result->fetch_assoc())
  {
      $num_img=$row['num_img'];
      $img_x_hoja=$row['img_x_hoja'];
  }
}

//echo $num_img;

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
            $tamfuem;

    $this->Image('../imagenes/logo_arca.png',15,5,140,0);
    $this->Image('../imagenes/pacal.jpg',160,5,40,0);
    $this->Image('../imagenes/codigo1.jpg',170,42,10,10);
    $this->Ln(18);
    $this->Cell(5);
    $this->SetFont('Arial','B',15);
    //$this->SetDrawColor(0,80,180);
   //$this->SetFillColor(230,230,0);
    $this->SetTextColor(0,0,255);
    //$this->Cell(185,5,'UNIDAD CENTRAL ARCA TULYEHUALCO ',0,0,'C');
    //$this->Ln(5);
    $this->SetFont('Arial','I',10);
    $this->Cell(185,5,'Josefa Ortiz de Dominguez No. 5 San Isidro Tulyehualco, Xochimilco, CDMX',0,0,'C');
    $this->Ln(3);
    $this->Cell(185,5,'________________________________________________________________________________________________',0,0,'C');
    $this->SetTextColor(0,0,0);

// Primer columna de titulos
    $this->Ln(7);
    $this->Cell(2);
    $this->SetFont('Arial','B',11);
    $this->Cell(22,5,'PACIENTE:',0,0,'L');
    $this->SetFont('Arial','',11);
    $this->Cell(88,5,$paciente,0,0,'L');

    $this->SetFont('Arial','B',11);
    $this->Cell(15,5,'DR(A):',0,0,'L');
    $this->SetFont('Arial','',11);
    $this->Cell(70,5,$medico,0,0,'L');
// Segunda linea
    $this->ln(5);
    $this->Cell(2);
    $this->SetFont('Arial','B',11);
    $this->Cell(22,5,'FOLIO:',0,0,'L');
    $this->SetFont('Arial','',11);
    $this->Cell(88,5,$numero_factura,0,0,'L');

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
    $this->MultiCell(88,5,$estudio2,0,'L');
 
    $this->SetFont('Arial','B',11);
    $this->SetXY(122, 51); 
    $this->Write(0,'EDAD:'); 
    $this->SetFont('Arial','',11);
    $this->SetXY(137,51); 
    $this->Write(0,$edad);

// Cuarta linea (nombre del estudio - plantilla -)
    /*
    $this->ln(15);
    $this->Cell(5);
    $this->SetFont('Arial','B',14);
    $this->Cell(170,5,$titulo_desc,0,0,'C'); 
    */

    $this->Ln(15);

}

// Pie de página
  function Footer()
  {

    global $studio,$con,$verificado,$tamfuev,$tipfuev,$posiniv;

    $this->SetY(-50); //
    //$this->ln(10);
    $this->Cell($posiniv);

    $this->SetFont('Arial',$tipfuev,$tamfuev);
    $this->Cell(30,5,$verificado,0,0,'L'); 
    $this->ln(10); // aqui
    //$this->Cell(5);

/*
    $sql="SELECT p2.concepto,posini,tipfue,tamfue FROM cr_plantilla_1 p2 WHERE p2.fk_id_estudio =".$studio." AND p2.estado = 'A' AND p2.tipo = 'F' order by orden";
    if ($result = mysqli_query($con, $sql)) {
      while($row = $result->fetch_assoc())
        {
          $this->Cell(($row['posini']-=6));
          $firma=$row['concepto'];
          $this->Image('../imagenes/firma.gif',153,225,40,0);
          $this->SetFont('Arial','',$row['tamfue']);
          $this->Cell(170,5,$firma,0,0,'L');
          $this->ln(4);
        }
*/
    $this->ln(10);
    $this->Cell(1);
    $this->SetTextColor(0,0,255);
    $this->Cell(185,5,'_____________________________________________________________________________________',0,0,'L');

    $this->SetTextColor(26,35,126); 
    $this->SetFont('Arial','B',16);
    $this->SetXY(65,257); 
    $this->Write(0,'www.laboratoriosarca.com.mx');

    $this->Image('../imagenes/whatsapp.jpg',10,262,7,0);
    $this->SetTextColor(27,94,32); 
    $this->SetFont('Arial','B',12);
    $this->SetXY(16,266); 
    $this->Write(0,'55 3121 0700');
    $this->SetTextColor(0,0,0);

    $this->Image('../imagenes/telefono.jpg',50,262,7,0);
    $this->SetTextColor(230,81,0); 
    $this->SetFont('Arial','B',12);
    $this->SetXY(56,266); 
    $this->Write(0,'ARCATEL: 216 141 44');
    $this->SetTextColor(0,0,0);

    $this->Image('../imagenes/email.jpg',105,262,7,0);
    $this->SetTextColor(26,35,126); 
    $this->SetFont('Arial','B',11);
    $this->SetXY(110,266); 
    $this->Write(0,'atencion.cliente@laboratoriosarca.com.mx');
    $this->SetTextColor(0,0,0);

    $this->SetTextColor(26,35,126); 
    $this->SetFont('Arial','B',10);
    $this->SetXY(20,274); 
    $this->Write(0,'Tulyehualco - San Gregorio - Xochimilco - Santiago - San Pablo - San Pedro - Tecomitl - Tetelco');
    $this->SetTextColor(0,0,0);

//    }
  }
}
//
// Creación del objeto de la clase heredada
//
$pdf = new PDF('P','mm','Letter');
//$pdf->SetMargins(0,0,0);
$pdf->SetAutoPageBreak(true,50);

$pdf->AliasNbPages();
$pdf->AddPage();


// rutina para verificar el numero de imagnes y asignar
// el tamaño de la imagen que le corresponda.

/*
$pdf->Cell(2);
$pdf->SetFont('Arial','',9);
*/
$leido=1;
$derecha=0;

// Ajusta tamaño de las imagenes, segun el numero de imagenes por hoja

      switch ($img_x_hoja){
        case '1':
          
            $alto=198.9;
            $ancho=153.85; 
            break;
        case '2':
          
            $alto=117;
            $ancho=90.5;
            break;
       case '4':
          
            $alto=93.6;
            $ancho=72.4;
            break;

       case '6':
          
            $alto=81.9;
            $ancho=63.35;
            break;

        default:
          if($v_alto>192 && $v_ancho>148){
            $alto=85;
            $ancho=65;
          }else{
            $alto=$v_alto;
            $ancho=$v_ancho;          
          }
            $columna=13;
            $renglon=53;         
          break;
      }


$sql_imagenes="select * FROM cr_plantilla_ekg_img
where estado = 'A' and fk_id_factura=".$numero_factura." and fk_id_estudio=".$studio;
//echo $sql_imagenes;
if ($result = mysqli_query($con, $sql_imagenes)) {
  while($row = $result->fetch_assoc())
  {
      $nombre=$row['nombre'];
      $ruta=$row['ruta'];
      $v_alto=$row['alto'];
      $v_ancho=$row['ancho'];

      $imagen="../img_ekg/".$numero_factura."/".$row['nombre'];

// impresion por lineas, segun numero de imagenes

        switch ($img_x_hoja){
// 1 imagen por hoja
          case '1':
            $columna=13;
            $renglon=65;
            $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
            if ($leido < $num_img){
               $pdf->AddPage();
            }            
            break;
// 2 imagenes pro hoja           
          case '2':
            switch ($leido) {
              case '1':
                $columna=45;
                $renglon=60;
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                break;

              case '2':
                $columna=45;
                $renglon=153;
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                if ($leido < $num_img){
                   $pdf->AddPage();
                   $leido=0;
                }
                break;
            }
            break;
// 4 imagenes por hoja
          case '4':
            switch ($leido) {
              case '1':
                $renglon=60;
                $columna=13;
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                break;

              case '2':
                $renglon=60;              
                $columna=107;
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                break;

              case '3':
                $renglon=140;              
                $columna=13;
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                break;

              case '4':
                $renglon=140;
                $columna=107;
                
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                if ($leido < $num_img){
                   $pdf->AddPage();
                   $leido=0;
                }
                break;
            }
            break;
// 6 imagenes por hoja
          case '6':
            switch ($leido) {
              case '1':
                $renglon=55;
                $columna=13;
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                break;

              case '2':
                $renglon=55;              
                $columna=107;
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                break;

              case '3':
                $renglon=120;              
                $columna=13;
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                break;

              case '4':
                $renglon=120;              
                $columna=107;
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                break;

              case '5':
                $renglon=185;              
                $columna=13;
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                break;

              case '6':
                $renglon=185;
                $columna=107;
                
                $pdf->Image($imagen,$columna,$renglon,$alto,$ancho);
                if ($leido < $num_img){
                   $pdf->AddPage();
                   $leido=0;
                }
                break;
            }
            break;         
        }
        $leido=$leido+1;
  }
}





//$pdf->MultiCell(185,5,$descripcion,0,'L');

/*
$pdf->ln(10);
$pdf->Cell(2);
$pdf->SetFont('Arial','B',9);
$pdf->MultiCell(50,5,trim($firma),0,'L');
*/

//for($i=1;$i<=20;$i++)
//    $pdf->Cell(0,10,'Imprimiendo línea número '.$i,0,1);

$pdf->Output();
?>