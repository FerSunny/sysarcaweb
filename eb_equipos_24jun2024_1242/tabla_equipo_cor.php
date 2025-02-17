<?php

  session_start();

  if (isset($_SESSION['nombre']) && $_SESSION['ingreso']=='YES')
  {
    include ("../controladores/conex.php");
	  $id_equipo=$_GET['id_equipo'];
    //$plantilla=$_GET['plantilla'];
    
	  $_SESSION['id_equipo']=$id_equipo;
    //$_SESSION['plantilla']=$plantilla;
    $equipo='';
    $sql_1="
      SELECT 
      CONCAT(e.id_equipo,' - ',e.descripcion ) AS equipo
      FROM eb_equipos e
      WHERE e.id_equipo = $id_equipo
    ";
    if ($result = mysqli_query($conexion, $sql_1)) {
      while($row = $result->fetch_assoc())
      {
          $equipo=$row['equipo'];
      }
      }
   // echo  '<b>('.$studio.') '.$desc_estudio
  

?>

<!DOCTYPE html>

<html lang="es">

<head>

  <meta http-equiv="Content-Type" content="text/html; charset=gb18030">

  <title>Servicios</title> <!-- CAMBIO  Titulo de la forma -->

  <link rel="icon" type="image/png" href="../imagenes/ico/capital.png" />

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">



    <!-- DataTable 1.10.19 14/03/2019-->

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.css"/>

    <!-- Font Awesome -->

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css">

    <!-- Bootstrap core CSS -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">

    <!-- Material Design Bootstrap -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.6/css/mdb.min.css" rel="stylesheet">



    <link rel="stylesheet" type="text/css" href="../media/alert/dist/sweetalert2.css">

  

</head>

<style>

  option:hover{

    background-color:red !important;

    -webkit-box-shadow: 17px 10px 31px 5px rgba(0,0,0,0.75);

    -moz-box-shadow: 17px 10px 31px 5px rgba(0,0,0,0.75);

    box-shadow: 17px 10px 31px 5px rgba(0,0,0,0.75);

  }

</style>

<body background="../imagenes/logo_arca_sys_web.jpg">

  <?php

    include("../includes/barra.php");

    include("formularios/formularios_equipo_cor.php"); // CAMBIO programa de la forma

  ?>



  <div class="container" style="margin-top: 30px;">

    <h1 style="text-align: center;">Tabla de Mantenimientos  <!-- CAMBIO Se cambia el titulo de la tabla -->

      <button type="button" class="btn btn-primary pull-right menu" data-toggle="modal" data-target="#myModals"><i class="fa fa-user-plus" aria-hidden="true"></i>&nbsp;Nuevo servicio</button> <!-- CAMBIO Se cambia el boton de altas -->

    </h1>

    <h3 style="text-align: center;">Mantenimiento para el equipo:  <?php echo $equipo ?> <!-- CAMBIO Se cambia el titulo de la tabla -->

      

    </h3>

  </div>



  <div id="cuadro1" class="col-sm-12 col-md-12 col-lg-12"> <!-- REVISAR -->

    <div class="col-sm-offset-2 col-sm-8">

      <h3 class="text-center">

        <small class="mensaje"></small>

      </h3>

    </div>

    <div class="container table-responsive">

      <table id="dt_productos" class="table table-bordered table-hover" cellspacing="1" width="100%">

        <thead>

          <tr>

            <!-- CAMBIO Se cambian las columnas segun las columnas a mostrar -->

            <th>Id </th>

            <th>Tipo </th>

            <th>Origen </th>

            <th>Fecha Reporte</th>
            
            <th>Fecha Inicio</th>
            
            <th>Fecha Termino</th>

            <th>Proveedor</th>

            <th>Contacto</th>

            <th>Responsable</th>

            <th>Editar</th>

            <th>Eliminar</th>

            <th>Piezas</th>

          </tr>

        </thead>

      </table>

    </div>

  </div>

  <script src="../media/js/jquery-1.12.3.js"></script>

  <script src="../media/alert/dist/sweetalert2.js"></script>

  <!-- JQuery -->



  <!-- Bootstrap tooltips -->

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.4/umd/popper.min.js"></script>

  <!-- Bootstrap core JavaScript -->

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/js/bootstrap.min.js"></script>

  <!-- MDB core JavaScript -->

  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdbootstrap/4.7.6/js/mdb.min.js"></script>

  

  <!-- DataTable 1.10.19 14/03/2019-->

  <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.js"></script>

  <script language="javascript" src="js/tabla_equipo_cor.js"></script>

</body>

</html>

<?php



  }

  else

  {

  header("location: index.html");

  }

 ?>

