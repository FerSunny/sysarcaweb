                                                                  <?php

	include ("../controladores/conex.php");
date_default_timezone_set('America/Mexico_City');
	$query = "SELECT   su.`desc_sucursal`,
  DATE_FORMAT(fa.fecha_factura,'%Y-%m') AS periodo,
  CONCAT(MAX(DATE_FORMAT(fa.fecha_factura,'%M')),'(',MAX(DATE_FORMAT(fa.fecha_factura,'%d')),')') AS mes,
  su.id_sucursal,
  SUM(CASE
    WHEN es.costo = df.precio_venta THEN
      es.costo
    ELSE
      CONCAT(es.costo)
  END) AS costo,
  SUM(ROUND((df.precio_venta*(CASE
            WHEN es.costo = df.precio_venta THEN
              co.porcentaje
            ELSE
              10
            END))/100,2)) AS participacion
FROM so_factura fa,
     so_medicos me,
      
     kg_sucursales su,
     so_detalle_factura df,
     km_estudios es,
     kg_comisiones co
WHERE DATE(fa.fecha_factura) >= (CONCAT(YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)),'-',MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)),'-01' ))
  AND fa.fk_id_medico = me.id_medico
 
  AND su.id_sucursal = fa.fk_id_sucursal
  AND fa.id_factura = df.id_factura
  AND df.fk_id_estudio = es.id_estudio
  AND es.fk_id_comision = co.id_comision
  and fa.afecta_comision = 1
  AND fa.estado_factura <> '5'
GROUP BY su.`desc_sucursal`,
  DATE_FORMAT(fa.fecha_factura,'%Y-%m'),
  DATE_FORMAT(fa.fecha_factura,'%M'),
  su.id_sucursal
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
