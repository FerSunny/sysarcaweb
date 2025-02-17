<?php

function multiple($tipo,$valor,$atach)
{
    include ("../controladores/conex.php");
    include("envia_email.php");

    switch ($tipo) {
        case '1': // paciente
            // obtener datos del paciente  
            $sql_pac="select mail as email FROM so_clientes
            where activo = 'A' AND id_cliente = $valor";
            //echo $sql_max;
            if ($result_pac = mysqli_query($conexion, $sql_pac)) {
              while($row_pac = $result_pac->fetch_assoc())
              {
                  $email=$row_pac['email'];
              }
            }
            break;
        case '2': // empleado
            // obtener datos del paciente  
            $sql_pac="select mail as email FROM se_usuarios
            where activo = 'A' AND id_usuario = $valor";
            //echo $sql_max;
            if ($result_pac = mysqli_query($conexion, $sql_pac)) {
                while($row_pac = $result_pac->fetch_assoc())
                {
                    $email=$row_pac['email'];
                }
            }
            break;
        case '3': // medico
            // obtener datos del paciente  
            $sql_pac="select e_mail as email FROM so_medicos
            where estado = 'A' AND id_medico = $valor";
            //echo $sql_max;
            if ($result_pac = mysqli_query($conexion, $sql_pac)) {
                while($row_pac = $result_pac->fetch_assoc())
                {
                    $email=$row_pac['email'];
                }
            }
            break;
        default:
            $email=NULL;
            break;
    }

// validamos e enviamos el email.

    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $valida = 0; //exit("invalid format");
    }else{
        $valida = envia_email($email,$atach);
    }


}   

?>