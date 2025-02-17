                                                                  <?php

	include ("../controladores/conex.php");

	$query = "SELECT
  su.desc_sucursal,
  fa.`id_factura` AS idnota,
  DATE(`fecha_factura`) AS fechafactura,
  CONCAT(cl.nombre,' ',cl.`a_paterno`,' ',cl.`a_materno`) AS nombrecliente,
  es.desc_estudio AS estudio,
   CASE
    WHEN LENGTH(fa.vmedico) > 0 THEN
      trim(fa.vmedico)
    ELSE
      CONCAT(me.nombre,' ',me.a_paterno,' ',me.a_materno) 
  END AS medico,
  CASE
    WHEN afecta_comision = 1 THEN
        'Si'
    ELSE
        'No'
  END AS afectacomision,
  `imp_total`,
  `a_cuenta`,
  `resta`,
  df.fk_id_estudio,
  es.fk_id_plantilla,
  cl.anios
FROM so_factura fa
LEFT OUTER JOIN kg_sucursales su ON (su.id_sucursal = fa.fk_id_sucursal)
LEFT OUTER JOIN so_clientes cl ON (cl.id_cliente = fa.fk_id_cliente)
LEFT OUTER JOIN so_medicos me ON (me.id_medico = fa.fk_id_medico)
LEFT OUTER JOIN se_usuarios us ON (us.id_usuario = fk_id_usuario)
LEFT OUTER JOIN so_detalle_factura df ON (df.id_factura = fa.id_factura)
LEFT OUTER JOIN km_estudios es ON (es.id_estudio = df.fk_id_estudio)
WHERE fa.fecha_factura BETWEEN DATE_SUB(CURDATE(), INTERVAL 40 DAY) AND DATE_ADD(CURDATE(), INTERVAL 40 DAY) 
AND fa.estado_factura!=5
-- AND fa.id_factura = 72598
";
//LEFT OUTER JOIN km_muestras m ON (m.id_muestra = e.fk_id_muestra) where estatus in ('A','S')";
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
